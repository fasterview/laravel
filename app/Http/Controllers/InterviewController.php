<?php

namespace App\Http\Controllers;

use App\Interview;
use App\Org;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Org $org)
    {
        $user = Auth::user();

        if($user->id != $org->user_id){
            return response()->json([
                "errors"    => [
                    "Unauthorized"
                ]
            ], 401);
        }


        // Validate the request
        $results = Validator::make($request->all(), [
            "questions" => "required|array",
            "questions.*.title"   => "required|string|min:8|max:100",
            "questions.*.time"    => "required|numeric|min:1|max:5",
            "role"    => "required|string|min:3|max:30",
            "descripotion" => "nullable|min:50",
        ]);

        if($results->fails()){  // Bad request error
            return response()->json([
                "errors"    => $results->errors()
            ], 400);
        }


        $interview = new Interview();

        $interview->questions = $request->questions;
        $interview->role = $request->role;
        $interview->org_id = $org->id;
        $interview->description = $request->description ?? "";
        $interview->require_cv = $request->require_cv ? true : false;

        $interview->save();

        
        return response()->json([
            "interview" => $interview
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function show(Interview $interview)
    {
        $interview->load("org");
        $interview->submitted = $interview->submits()->where("user_id", Auth::id())->exists();
        
        return response()->json([
            "interview" => $interview
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function edit(Interview $interview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interview $interview)
    {
        $user = Auth::user();
        $org = $interview->org;

        if($user->id != $org->user_id){
            return response()->json([
                "errors"    => [
                    "Unauthorized"
                ]
            ], 401);
        }


        // Validate the request
        $results = Validator::make($request->all(), [
            "questions" => "required|array",
            "questions.*.title"   => "required|string|min:8|max:100",
            "questions.*.time"    => "required|numeric|min:1|max:5",
            "role"    => "required|string|min:3|max:30",
            "descripotion" => "nullable|min:50",
        ]);

        if($results->fails()){  // Bad request error
            return response()->json([
                "errors"    => $results->errors()
            ], 400);
        }


        $interview->questions = $request->questions;
        $interview->role = $request->role;
        $interview->org_id = $org->id;
        $interview->description = $request->description ?? "";
        $interview->active  = !!$request->active;
        $interview->require_cv = $request->require_cv ? true : false;

        $interview->save();

        
        return response()->json([
            "interview" => $interview
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interview $interview)
    {
        $user = Auth::user();
        $org = $interview->org;

        if($user->id != $org->user_id){
            return response()->json([
                "errors"    => [
                    "Unauthorized"
                ]
            ], 401);
        }

        $deleted = $interview->delete();

        return response()->json([
            "success"   => $deleted,
        ], 200);
        
    }
}
