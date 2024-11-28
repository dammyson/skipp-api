<?php

namespace App\Services\Wallet;

use App\Services\BaseServiceInterface;
use DB;
use App\Models\Game;
use App\Models\GameCategory;



class FlutterVerificationService implements BaseServiceInterface
{
    protected $ref_number;

    public function __construct($ref_number)
    {
        $this->ref_number = $ref_number;
    }

    public function run()
    {
      
        return $this->verfiy();
    }

    public function verfiy()
    { 
        $result = array();
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.flutterwave.com/v3/transactions/'. $this->ref_number .'/verify';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
          $ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer FLWSECK_TEST-25575cadfe6302277a58f1ad237051a0-X']
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
