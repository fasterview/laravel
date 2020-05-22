<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Register new user
Route::post("/oauth/register", "UserController@register");

// Get user loggedin user data
Route::get("/me", "UserController@me")
            ->middleware("auth:api")
            ->name("me");



// ============ Organization ============
Route::resource("organization", "OrgController", [
    "middleware"    => ["auth:api"],
    "only" => ["store", "show", "update", "destroy"],
    "parameters" => [
        "organization" => "org"
    ]
]);


// ============ Interviews ============
Route::group(["middleware" => "auth:api"], function(){
    
    Route::post("/{org}/interview", "InterviewController@store");
    Route::put("/interview/{interview}", "InterviewController@update");
    Route::get("/interview/{interview}", "InterviewController@show");
    Route::delete("/interview/{interview}", "InterviewController@destroy");


    // Submits
    Route::post("/interview/{interview}/submit", "SubmitController@submit");
    

});