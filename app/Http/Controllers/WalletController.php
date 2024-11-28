<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Requests\Feature\ListRequest;
use App\Models\Wallet;
use App\Services\Wallet\TopUpService;
use App\Services\Wallet\VerificationService;
use Illuminate\Support\Facades\Log;
use App\Notifications\Wallet\RechargeWallet As TopUpWalletNotification;
use App\Services\Wallet\FlutterVerificationService;

class WalletController extends Controller
{
     /**
     * Return a list of features in the application
     * Filter parameters are allowed
     */

    public function verify($ref)
    {
        $user_id = \Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->get();
       
        try {
            $new_top_request = new VerificationService($ref);
            $verified_request = $new_top_request->run();
             $top_up  =  new TopUpService($verified_request,  $wallet);
             $top_up_result =  $top_up->run();
            return response()->json(['status' => true, 'data' =>  $top_up_result, 'message' => 'Wallet top up successfully'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }
    
    
    public function fluterVerify($ref)
    {
        $user_id = \Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->get();
        try {
            $new_top_request = new FlutterVerificationService($ref);
            $verified_request = $new_top_request->run();
              $top_up  =  new TopUpService($verified_request,  $wallet);
             $top_up_result =  $top_up->run();
             return response()->json(['status' => true, 'data' =>  $top_up_result, 'message' => 'Wallet top up successfully'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }
    
}
