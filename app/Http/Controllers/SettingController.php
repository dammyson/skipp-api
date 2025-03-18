<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\ChangeTransactionPinRequest;
use App\Http\Requests\Transaction\SetTransactionPinRequest;
use App\Models\Question;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends BaseController
{   
    protected $transactionService;

    public function __construct(TransactionService $transactionService) {
        $this->transactionService = $transactionService;
    }

    public function setTransactionPin(SetTransactionPinRequest $request) {
        try {
            $updatedUser = $this->transactionService->setTransactionPin($request->user(), $request->validated());
            return $this->sendResponse($updatedUser, 'pin set sucessfully', 200);

        } catch(\Throwable $th) {
            $this->sendError($th, [$th->getMessage()], 400);
        }


    }

    public function changeTransactionPin(ChangeTransactionPinRequest $request) {

        $updatedUser =$this->transactionService->changeTransactionPin($request->user(), $request->validated());

        return $this->sendResponse($updatedUser, 'pin updated sucessfully', 200);

    }


    public function fulfilmentMethod(Request $request, $orderId) {
       $updatedInvoice = $this->transactionService->setPickUpTime($orderId, $request['pickup_time']);
        
       return $this->sendResponse($updatedInvoice, 'pin updated sucessfully', 200);

        
    }

}
