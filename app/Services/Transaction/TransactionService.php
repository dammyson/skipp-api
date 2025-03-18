<?php

namespace App\Services\Transaction;

use Error;
use App\Models\User;
use App\Models\Question;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class TransactionService
{
    public function setTransactionPin($user, $data) {
        if ($user->pin_number) {
            // throw new Error("Pin already set");
            return response()->json([
                "error" => true,
                "message" => "Pin already set"
            ], 400);
        }


        if (!Hash::check($data["password"], $user->password)) {
            return response()->json([
                "error" => true,
                "message" => "password is invalid"
            ], 400);
        }

        // dd($data['pin']);


        $user->pin_number = $data["pin"];
        $user->save();

        return $user;
    }

    public function changeTransactionPin($user, $data) {
    

        $user = User::find($user['id']);
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                "error" => true,
                "message" => "password is invalid"
            ], 400);
        }

        $user->pin_number = $data['pin'];
        $user->save();

       return $user;

    }

    public function setPickUpTime($orderId, $pickUpTime) {
        $transaction = Transaction::find($orderId)->with('invoice');
        
        // Check if transaction exists
        if (!$transaction) {
            return response()->json([
                "error" => true,
                "message" => "Transaction not found"
            ], 404);
        }

       if (!$transaction->invoice) {
            return response()->json([
                "error" => true,
                "message" => "Transaction does not have an invoice"
            ]);
       }

       $transaction->invoice->pickup_time = $pickUpTime;
       $transaction->invoice->save();
       return $transaction->invoice;
    }

    
}