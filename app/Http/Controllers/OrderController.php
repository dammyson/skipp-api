<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display the authenticated user's order history.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderHistory()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the user's transactions with associated invoices and items
        $transactions = Transaction::with(['invoice.items.product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the data
        $orderHistory = $transactions->map(function ($transaction) {
            return [
                'transaction_id' => $transaction->id,
                'total_amount' => $transaction->total_amount,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
                'invoice' => [
                    'invoice_id' => $transaction->invoice->id,
                    'items' => $transaction->invoice->items->map(function ($item) {
                        return [
                            'product_id' => $item->product->id,
                            'product_name' => $item->product->name ?? 'Unknown Product',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ];
                    }),
                ],
            ];
        });

        // Return the order history as a JSON response
        return response()->json([
            'success' => true,
            'data' => $orderHistory,
        ], 200);
    }
}
