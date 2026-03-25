<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Investment;
use Illuminate\Support\Collection;

class ImportService
{
    public function importData(int $teamId, array $data): array
    {
        $result = [
            'success' => ['transactions' => 0, 'categories' => 0, 'investments' => 0],
            'errors' => [],
        ];

        if (isset($data['transactions'])) {
            $this->importTransactions($teamId, $data['transactions'], $result);
        }

        if (isset($data['categories'])) {
            $this->importCategories($teamId, $data['categories'], $result);
        }

        if (isset($data['investments'])) {
            $this->importInvestments($teamId, $data['investments'], $result);
        }

        return $result;
    }

    protected function importTransactions(int $teamId, array $transactions, array &$result): void
    {
        foreach ($transactions as $row => $item) {
            try {
                $row = $row + 2;

                $categoryId = null;
                if (! empty($item['category_name'])) {
                    $category = Category::where('user_id', auth()->id())
                        ->where('team_id', $teamId)
                        ->where('name', $item['category_name'])
                        ->first();

                    if ($category) {
                        $categoryId = $category->id;
                    }
                }

                Transaction::create([
                    'user_id' => auth()->id(),
                    'team_id' => $teamId,
                    'title' => $item['title'] ?? '',
                    'amount' => (float) ($item['amount'] ?? 0),
                    'type' => $item['type'] ?? 'expense',
                    'note' => $item['note'] ?? '',
                    'category_id' => $categoryId,
                    'currency' => $item['currency'] ?? 'USD',
                    'created_at' => isset($item['created_at']) ? \Illuminate\Support\Carbon::parse($item['created_at']) : now(),
                ]);

                $result['success']['transactions']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Transaction row $row: " . $e->getMessage();
            }
        }
    }

    protected function importCategories(int $teamId, array $categories, array &$result): void
    {
        foreach ($categories as $row => $item) {
            try {
                $row = $row + 2;

                $existing = Category::where('user_id', auth()->id())
                    ->where('team_id', $teamId)
                    ->where('name', $item['name'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'monthly_budget' => $item['monthly_budget'] ?? 0,
                        'budget_currency' => $item['budget_currency'] ?? 'CZK',
                    ]);
                } else {
                    Category::create([
                        'user_id' => auth()->id(),
                        'team_id' => $teamId,
                        'name' => $item['name'] ?? '',
                        'monthly_budget' => $item['monthly_budget'] ?? 0,
                        'budget_currency' => $item['budget_currency'] ?? 'CZK',
                    ]);
                }

                $result['success']['categories']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Category row $row: " . $e->getMessage();
            }
        }
    }

    protected function importInvestments(int $teamId, array $investments, array &$result): void
    {
        foreach ($investments as $row => $item) {
            try {
                $row = $row + 2;

                $existing = Investment::where('team_id', $teamId)
                    ->where('type', $item['type'] ?? 'stock')
                    ->where('symbol', strtoupper($item['symbol'] ?? ''))
                    ->first();

                if ($existing) {
                    $oldQty = (float) $existing->quantity;
                    $newQty = (float) ($item['quantity'] ?? 0);
                    $oldAvg = (float) $existing->average_price;
                    $newAvg = (float) ($item['average_price'] ?? 0);
                    $importCurrency = strtoupper($item['currency'] ?? 'USD');
                    if ($existing->currency !== $importCurrency) {
                        try {
                            $converter = app(\App\Services\CurrencyConverter::class);
                            $oldAvg = $converter->convert($oldAvg, $existing->currency, $importCurrency);
                        } catch (\Exception $e) {
                            $result['errors'][] = "Investment row $row: Failed to convert currency for merge ({$existing->currency} → {$importCurrency})";
                            continue;
                        }
                    }

                    $totalQty = $oldQty + $newQty;

                    $weightedAvg = $totalQty > 0
                        ? (($oldQty * $oldAvg) + ($newQty * $newAvg)) / $totalQty
                        : $newAvg;

                    $existing->update([
                        'quantity' => $totalQty,
                        'average_price' => $weightedAvg,
                        'name' => $item['name'] ?: $existing->name,
                        'external_id' => $item['external_id'] ?: $existing->external_id,
                        'currency' => $importCurrency,
                    ]);
                } else {
                    Investment::create([
                        'user_id' => auth()->id(),
                        'team_id' => $teamId,
                        'type' => $item['type'] ?? 'stock',
                        'symbol' => strtoupper($item['symbol'] ?? ''),
                        'name' => $item['name'] ?? '',
                        'external_id' => $item['external_id'] ?? '',
                        'quantity' => (float) ($item['quantity'] ?? 0),
                        'average_price' => (float) ($item['average_price'] ?? 0),
                        'currency' => $item['currency'] ?? 'USD',
                    ]);
                }

                $result['success']['investments']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Investment row $row: " . $e->getMessage();
            }
        }
    }
}
