<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- YEH LINE HONA ZARURI HAI
use Exception;

class OrderController extends Controller
{
    private $receiverAddress = '0xd1dfA7363C644ca8600EF858F333e8A945eCE372'; // Apna sahi address rakhein
    private $expectedAmountWei = 1000000000000000; 

    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('payment', compact('orders'));
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'tx_hash' => 'required|string|unique:orders,tx_hash',
            ]);

            $txHash = $request->tx_hash;
            $rpcUrl = 'https://rpc-amoy.polygon.technology';

            // withoutVerifying() add kiya hai taaki local XAMPP par SSL error na aaye
            $txResponse = Http::withoutVerifying()->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionByHash',
                'params' => [$txHash],
                'id' => 1,
            ])->json('result');

            $receiptResponse = Http::withoutVerifying()->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionReceipt',
                'params' => [$txHash],
                'id' => 2,
            ])->json('result');

            if (!$txResponse || !$receiptResponse) {
                return response()->json(['success' => false, 'message' => 'Transaction RPC par nahi mili.'], 400);
            }

            if ($receiptResponse['status'] !== '0x1') {
                return response()->json(['success' => false, 'message' => 'Transaction fail ho chuki hai.'], 400);
            }

            if (strtolower($txResponse['to']) !== strtolower($this->receiverAddress)) {
                return response()->json(['success' => false, 'message' => 'Receiver address match nahi hua.'], 400);
            }

            $actualAmountWei = hexdec($txResponse['value']);
            if ($actualAmountWei < $this->expectedAmountWei) {
                return response()->json(['success' => false, 'message' => 'Amount match nahi hua.'], 400);
            }

            $order = Order::create([
                'tx_hash' => $txHash,
                'wallet_address' => $txResponse['from'],
                'amount' => 0.001,
                'status' => 'paid',
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Payment verify aur save ho gayi!',
                'data' => $order
            ]);

        } catch (Exception $e) {
            // Ab Laravel HTML nahi, balki clean JSON error bhejaiga
            return response()->json([
                'success' => false, 
                'message' => 'Backend Error: ' . $e->getMessage()
            ], 500);
        }
    }
}