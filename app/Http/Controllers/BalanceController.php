<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function add_balance(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'lt:100000'],
        ]);

        $balance = \App\Models\Balance::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $balance->amount += $request->amount;
        $balance->save();

        return response()->json([
            'message' => 'Successfully added balance',
            'amount' => number_format($balance->amount, 2, '.', ' '),
        ], 200);
    }

    public function get_balance()
    {
        $balance = \App\Models\Balance::firstOrCreate([
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'Successfully retrieved balance',
            'amount' => number_format($balance->amount, 2, '.', ' '),
        ], 200);
    }
}
