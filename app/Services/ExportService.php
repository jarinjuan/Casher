<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Investment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    public function exportToFormat(int $teamId, array $types, string $format): string
    {
        $data = $this->collectData($teamId, $types);

        if ($format === 'xlsx') {
            return $this->exportToXlsx($data);
        }

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        if ($format === 'pdf') {
            return $this->exportToPdf($data);
        }

        throw new \InvalidArgumentException("Unsupported format: $format");
    }

    protected function collectData(int $teamId, array $types): array
    {
        $data = [];

        if (in_array('transactions', $types)) {
            $transactions = Transaction::where('team_id', $teamId)
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
            $categories = Category::where('user_id', auth()->id())
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
            $investments = Investment::where('team_id', $teamId)
                ->with('latestPrice')
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

    protected function exportToXlsx(array $data): string
    {
        $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new \App\Exports\DataExport($data), $filename);
    }

    protected function exportToCsv(array $data): string
    {
        $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.csv';

        return Excel::download(new \App\Exports\DataExport($data), $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    protected function exportToPdf(array $data): string
    {
        $filename = 'casher_export_' . now()->format('Y-m-d_His') . '.pdf';

        $pdf = Pdf::loadView('exports.pdf', ['data' => $data])
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename);
    }
}
