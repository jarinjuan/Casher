<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('data.index');
    }

    public function export(Request $request, ExportService $exportService)
    {
        $types = $request->input('types', []);
        $format = $request->input('format', 'xlsx');

        if (empty($types)) {
            return back()->withErrors(['types' => 'Please select at least one data type to export.']);
        }

        $teamId = $request->user()->currentTeam->id ?? null;

        try {
            if ($format === 'xlsx') {
                $data = $this->collectData($teamId, $types);
                $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.xlsx';
                return Excel::download(new \App\Exports\DataExport($data), $filename);
            }

            if ($format === 'csv') {
                $data = $this->collectData($teamId, $types);
                $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.csv';
                return Excel::download(new \App\Exports\DataExport($data), $filename, \Maatwebsite\Excel\Excel::CSV);
            }

            if ($format === 'pdf') {
                $data = $this->collectData($teamId, $types);
                $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.pdf';
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf', ['data' => $data])
                    ->setOptions(['defaultFont' => 'sans-serif']);
                return $pdf->download($filename);
            }

            return back()->withErrors(['format' => 'Invalid format.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['export' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    public function import(Request $request, ImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:10240',
        ]);

        $teamId = $request->user()->currentTeam->id ?? null;
        $file = $request->file('file');

        try {
            $data = Excel::toArray(new \App\Imports\DataImport(), $file);

            $importData = [];

            $currencyRule = \App\Enums\CurrencyList::validationRule();

            if (isset($data[0]) && ! empty($data[0])) {
                $rawTransactions = array_slice($data[0], 1);
                $validTransactions = [];
                foreach ($rawTransactions as $idx => $row) {
                    $rowData = [
                        'title'    => $row[0] ?? '',
                        'amount'   => $row[1] ?? 0,
                        'type'     => $row[2] ?? 'expense',
                        'note'     => $row[3] ?? '',
                        'category_name' => $row[4] ?? '',
                        'currency' => $row[5] ?? 'USD',
                        'created_at' => $row[6] ?? now(),
                    ];
                    $validator = \Illuminate\Support\Facades\Validator::make($rowData, [
                        'title'    => 'required|string|max:255',
                        'amount'   => 'required|numeric|min:0.01|max:99999999.99',
                        'type'     => 'required|in:income,expense',
                        'note'     => 'nullable|string|max:10000',
                        'currency' => ['required', 'string', 'size:3', $currencyRule],
                    ]);
                    if ($validator->fails()) {
                        continue; // Skip invalid rows
                    }
                    $validTransactions[] = $rowData;
                }
                $importData['transactions'] = $validTransactions;
            }

            if (isset($data[1]) && ! empty($data[1])) {
                $rawCategories = array_slice($data[1], 1);
                $validCategories = [];
                foreach ($rawCategories as $idx => $row) {
                    $rowData = [
                        'name'            => $row[0] ?? '',
                        'monthly_budget'  => $row[1] ?? 0,
                        'budget_currency' => $row[2] ?? 'CZK',
                    ];
                    $validator = \Illuminate\Support\Facades\Validator::make($rowData, [
                        'name'            => 'required|string|max:255',
                        'monthly_budget'  => 'nullable|numeric|min:0|max:999999999999.99',
                        'budget_currency' => ['nullable', 'string', 'size:3', $currencyRule],
                    ]);
                    if ($validator->fails()) {
                        continue;
                    }
                    $validCategories[] = $rowData;
                }
                $importData['categories'] = $validCategories;
            }

            if (isset($data[2]) && ! empty($data[2])) {
                $rawInvestments = array_slice($data[2], 1);
                $validInvestments = [];
                foreach ($rawInvestments as $idx => $row) {
                    $rowData = [
                        'type'          => $row[0] ?? 'stock',
                        'name'          => $row[1] ?? '',
                        'symbol'        => $row[2] ?? '',
                        'external_id'   => $row[3] ?? '',
                        'quantity'      => $row[4] ?? 0,
                        'average_price' => $row[5] ?? 0,
                        'currency'      => $row[6] ?? 'USD',
                    ];
                    $validator = \Illuminate\Support\Facades\Validator::make($rowData, [
                        'type'          => 'required|in:stock,crypto',
                        'symbol'        => 'required|string|max:15',
                        'name'          => 'nullable|string|max:100',
                        'external_id'   => 'nullable|string|max:100',
                        'quantity'      => 'required|numeric|min:0.00000001|max:9999999999',
                        'average_price' => 'required|numeric|min:0|max:9999999999',
                        'currency'      => ['required', 'string', 'size:3', $currencyRule],
                    ]);
                    if ($validator->fails()) {
                        continue;
                    }
                    $validInvestments[] = $rowData;
                }
                $importData['investments'] = $validInvestments;
            }

            $result = $importService->importData($teamId, $importData);

            $message = 'Import completed: ';
            $message .= 'Transactions: ' . $result['success']['transactions'] . ', ';
            $message .= 'Categories: ' . $result['success']['categories'] . ', ';
            $message .= 'Investments: ' . $result['success']['investments'];

            if (! empty($result['errors'])) {
                $errorMsg = implode("\n", $result['errors']);
                return back()->with('success', $message)->with('import_errors', $result['errors']);
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->withErrors(['import' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    protected function collectData(int $teamId, array $types): array
    {
        $data = [];

        if (in_array('transactions', $types)) {
            $transactions = \App\Models\Transaction::where('team_id', $teamId)
                ->with('category')
                ->orderBy('created_at')
                ->get();

            $data['transactions'] = $transactions->map(function ($t) {
                return [
                    'title' => $t->title,
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'note' => $t->note,
                    'category_name' => $t->category?->name ?? '',
                    'currency' => $t->currency,
                    'created_at' => $t->created_at->toDateString(),
                ];
            })->toArray();
        }

        if (in_array('categories', $types)) {
            $categories = \App\Models\Category::where('user_id', auth()->id())
                ->where('team_id', $teamId)
                ->orderBy('name')
                ->get();

            $data['categories'] = $categories->map(function ($c) {
                return [
                    'name' => $c->name,
                    'monthly_budget' => $c->monthly_budget,
                    'budget_currency' => $c->budget_currency,
                ];
            })->toArray();
        }

        if (in_array('investments', $types)) {
            $investments = \App\Models\Investment::where('team_id', $teamId)
                ->orderBy('created_at')
                ->get();

            $data['investments'] = $investments->map(function ($i) {
                return [
                    'type' => $i->type,
                    'name' => $i->name,
                    'symbol' => $i->symbol,
                    'external_id' => $i->external_id,
                    'quantity' => $i->quantity,
                    'average_price' => $i->average_price,
                    'currency' => $i->currency,
                ];
            })->toArray();
        }

        return $data;
    }
}
