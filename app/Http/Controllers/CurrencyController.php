<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CurrencyConverter;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function convert(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'from'   => ['required', 'string', 'size:3', CurrencyList::validationRule()],
            'to'     => ['required', 'string', 'size:3', CurrencyList::validationRule()],
            'date'   => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $converter = new CurrencyConverter();

        try {
            $result = $converter->convert((float)$data['amount'], $data['from'], $data['to'], $data['date'] ?? null);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'amount' => (float) $data['amount'],
            'from' => strtoupper($data['from']),
            'to' => strtoupper($data['to']),
            'result' => $result,
        ]);
    }
}
