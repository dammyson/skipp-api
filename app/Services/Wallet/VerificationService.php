<?php

namespace App\Services\Wallet;

use App\Services\BaseServiceInterface;
use DB;
use App\Models\Game;
use App\Models\GameCategory;

class VerificationService implements BaseServiceInterface
{
    protected $ref_number;

    public function __construct($ref_number)
    {
        $this->ref_number = $ref_number;
    }

    public function run()
    {
        return $this->storeCategories();
    }

    public function storeCategories()
    {
         $result = array();
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/'. $this->ref_number;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
          $ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer sk_test_7229ffee506b0c463beafbd03f13bd5091132a10']
        );
        $request = curl_exec($ch);
        if(curl_error($ch)){
         echo 'error:' . curl_error($ch);
         }
        curl_close($ch);
        
        if ($request) {
          $result = json_decode($request, true);
        }
        
        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
            return $result;
        }else{
            return $result;
        }
       
    }
}