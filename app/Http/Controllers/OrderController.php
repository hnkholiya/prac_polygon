<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function verify(Request $request)
    {
        $tx = $request->tx_hash;

        $response = Http::get("https://api.etherscan.io/api", [
            'module' => 'transaction',
            'action' => 'gettxreceiptstatus',
            'txhash' => $tx,
            'chainid' => 80001,
            'apikey' => config('services.etherscan.key'),
        ]);

        if ($response['result']['status'] == '1') {
            Order::create([
                'tx_hash' => $tx,
                'status' => 'paid'
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

}
