<?php

namespace App\Http\Controllers;

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
            'amount' => ['required','numeric'],
            'from' => ['required','string','size:3'],
            'to' => ['required','string','size:3'],
            'date' => ['nullable','date'],
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
