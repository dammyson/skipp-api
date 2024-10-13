<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Models\WalletTopupTransaction;
use App\Services\BaseServiceInterface;
use DB;


class TopUpService implements BaseServiceInterface
{
    protected $wallet;
    protected $validated;

    public function __construct($validated, $wallet)
    {
        $this->validated = $validated;
        $this->wallet = $wallet;
    }

    public function run()
    {
        return $this->processTopup();
    }

    private function processTopup()
    {
       // dd( $this->wallet );
        return DB::transaction(function () {
            // $wallet_transaction = WalletTopupTransaction::create([
            //     'company_id' => $this->wallet[0]->company_id,
            //     'wallet_id' => $this->wallet[0]->id,
            //     'amount' => $this->validated['data']['amount'],
            //     'reference' => 'Paystack',
            //     'status' => $this->validated['data']['status']
            // ]);
            $credit = $this->creditWallet();
            return  $credit;
        });
    }


    private function creditWallet()
    {
        $wallet = Wallet::findOrFail($this->wallet[0]->id);
        $wallet->balance =  $wallet->balance + $this->validated['data']['amount'];
        $wallet->ledger_balance =  $wallet->ledger_balance + $this->validated['data']['amount'];
        $wallet->save();
        return $wallet;
    }
}
