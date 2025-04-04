<?php
     
namespace App\Http\Controllers;
     
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PasswordChanged;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
     
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validated  = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|numeric|digits:11',
            'password' => 'required|confirmed',
        ]);
     
       
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create([...$input, 'user_type' => 'regular_user']);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        $this->createWallet($user);
;   
        return $this->sendResponse($success, 'User register successfully.');
    }
     
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
           
            $suser = User::with('wallet')->find( $user->id);

            // $details = [
            //     'title' => 'Ebuka New Message',
            //     'body' => 'You have received a new message From Ebuka.',
            //     'url' => '/messages/1'
            // ];

            // $suser->notify(new PasswordChanged($details));

            // dd('');

            $success['user'] =  $suser;
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    private function createWallet($user)
    {
        $wallet = new Wallet();
        $wallet->user_id =$user->id;
        $wallet->balance = 0;
        $wallet->ledger_balance = 0;
        $wallet->save();
        return $wallet;
    }
}