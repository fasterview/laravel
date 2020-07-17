<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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


    /**
     * Send loggedin user data
     */
    public function me(){
        $user = Auth::user();

        return response()->json($user);
    }


    /**
     * Change user settinsg
     */
    public function settings(Request $request){

        $validate = Validator::make($request->all(), [
            "name" => "bail|required|string|min:3|max:15",
            "image" => "nullable|image|max:4096",
            "bio" => "nullable|string|max:500",
        ]);

        if($validate->fails()){
            return response()->json([
                "errors"    => $validate->errors()
            ], 400);
        }

        $user = Auth::user();

        // Upload the image if exsits
        $filePath = $user->pic;
        
        if($request->file("image")){
            $filePath = $request->image->store("public/images");
            
            // Delete the old one
            Storage::delete($user->pic);
        }

        // Update information
        $user->name = $request->name;
        $user->pic = $filePath;
        $user->bio = $request->bio;

        $user->save();

        return response()->json($user);
    }


    /**
     * Change authenticated user password
     */
    public function password(Request $request){
        
        $request->validate([
            "old_password" => "required|min:6",
            "new_password" => "required|min:6|confirmed"
        ]);
        $user = Auth::user();

        if( !Hash::check($request->old_password, $user->password)){
            return response()->json([
                "errors"    => [
                    "Wrong password" . " " . $request->old_password
                ]
            ], 401);
        }


        // Change user password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            "success"   => true
        ], 200);
    }


}
