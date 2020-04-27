<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;

class UserController extends Controller
{
    
    /*
        Create new user
    */
    public function register(Request $request){

        // Validate the request
        $results = Validator::make($request->all(), [
            "email" => "bail|required|email|unique:users,email",
            "name" => "bail|required|string|min:3|max:15",
            "password" => "bail|required|min:6|confirmed"
        ]);

        if($results->fails()){
            return response()->json([
                "errors"    => $results->errors()
            ], 401);
        }

        // Create new user
        $user = new User([
            "email" => $request->email,
            "name" => $request->name,
            "password" => Hash::make($request->password)
        ]);

        $user->save();


        // Make internal request to send the JWT to the user
        
        // Get client id and secret
        $client = Client::where("password_client", 1)->first();


        // Add data to the request
        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $request->email,
            'password'      => $request->password,
            'scope'         => ''
        ]);


        $token = Request::create(
            'oauth/token',
            'POST'
        );
    
        return \Route::dispatch($token);
    }
}
