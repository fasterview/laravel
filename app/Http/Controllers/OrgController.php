<?php

namespace App\Http\Controllers;

use App\Interview;
use App\Org;
use App\Submit;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrgController extends Controller
{
    /**
     * Dispaly onganization for logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orgs = Auth::user()->orgs;

        return response()->json([
            "organizations"  => $orgs
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $results = Validator::make($request->all(), [
            "name"  => "required|string|min:3|max:25"
        ]);

        if($results->fails()){
            return response()->json([
                "errors"    => $results->errors()
            ], 401);
        }

        // Create new org and associate it with the uathed user
        $org = new Org();

        $org->name = $request->name;
        $org->user()->associate(Auth::user());

        // Save the new org
        $org->save();

        // Return the org data to the user in json format
        return response()->json([
            "success"   => true,
            "org"   => $org
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Org  $org
     * @return \Illuminate\Http\Response
     */
    public function show(Org $org)
    {
        return response()->json([
            "org"   => $org
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Org  $org
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Org $org)
    {
        // Validate the request
        $results = Validator::make($request->all(), [
            "name"  => "required|string|min:3|max:25"
        ]);

        
        if($results->fails()){
            return response()->json([
                "errors"    => $results->errors()
            ], 401);
        }
        
        // Update the new values
        $org->name = $request->name;
        
        // Save the changes
        $org->save();

        // Return the org data to the user in json format
        return response()->json([
            "success"   => true,
            "org"   => $org
        ], 202);
    }

    /**
     * Interviews
     */
    public function interviews(Org $org){
        if(Auth::id() != $org->user_id){
            return response()->json([
                "errors"    => [
                    "Unauthorized"
                ]
            ], 401);
        }

        $interviews = $org->interviews()->paginate();

        return response()->json($interviews);
    }

    /**
     * Add or remove user from organization
     */
    public function user(Request $request, Org $org, Interview $interview){
        // Validate the request
        $results = Validator::make($request->all(), [
            "user_id" => "required|numeric|exists:users,id",
            "status" => [
                "required",
                Rule::in(['accepted', 'rejected'])
            ]
        ]);

        
        if($results->fails()){
            return response()->json([
                "errors"    => $results->errors()
            ], 401);
        }
        
        if($org->user_id != Auth::id() && $interview->org_id == $org->id){
            return response()->json([
                "errors"    => [
                    "Unauthorized"
                ]
            ], 401);
        }

        $submit = Submit::where("interview_id", $interview->id)
                            ->where("user_id", $request->user_id)->first();

        $submit->status = $request->status;
        $submit->save();


        $request->status == "accepted" ? 
                    $org->users()->syncWithoutDetaching([$request->user_id => ["role" => $submit->interview->role]]) :
                    $org->users()->detach($request->user_id);

        

        return response()->json([
            "success"   => true,
        ],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Org  $org
     * @return \Illuminate\Http\Response
     */
    public function destroy(Org $org)
    {
        // Check if the user own the org
        if(Auth::user()->id != $org->user_id){
            
            return response()->json([
                "errors"    => [
                    "Unuthorized"
                ]
            ], 401);
        }

        
        $deleted = $org->delete();


        if($deleted){
            return response()->json([
                "success"   => true
            ], 200);
        } else {
            return response()->json([
                "errors"    => [
                    "Organization not exsists"
                ]
            ], 400);
        }

    }
}
