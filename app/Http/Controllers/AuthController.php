<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateRequest;
use App\Models\User;
use App\Services\User\UpdateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|between:4,32|confirmed',
        ]);

        try {
            $user = Auth::user();
            $info = User::where('id', $user->id)->first();
            if ($info) {

                if (Hash::check(trim($validated['old_password']), $info->password)) {
                    $info->password = $validated['password'];
                    $info->save();
                    return response()->json(['status' => true, 'data' => $info,  'message' => 'Password Changed'], 201);
                } else {
                    return response(['message' => 'Email or Password Incorrect'], 401);
                }
            } else {
                return response(['message' => 'Email or Password Incorrect'], 401);
            }
        } catch (Exception $exception) {
            return response()->json(['status' => false,  'error' => $exception->getMessage(), 'message' => 'Error processing request'], 500);
        }
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function resetPassword(Request $request){
     
        $this->validate($request, [
            'password' => 'required|between:4,32|confirmed',
            'email' => 'required|exists:users,email'
        ]);

        try {
            $authUser = User::where('email', $request->email)->firstOrFail();
            $authUser->password = $request->password;
            $authUser->save();
          
            return response()->json(['status' => true, 'message' => 'Password Successfully Reset', 'data' => $authUser ], 200);
        } catch (\Exception $exception) {
            return response()->json(['status' => false,  'message' => $exception->getMessage()], 500);
        }

    }


    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        try {
            (new UpdateService($user, $validated))->run();
           
            return response()->json(['status' => true, 'data' => $user, 'message' => 'profile update successful'], 200);
        } catch (\Exception $exception) {
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

}
