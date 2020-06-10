<?php

namespace App\Http\Controllers;

use App\Interview;
use App\Submit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubmitController extends Controller
{



    /**
     * Submit the interview video.
     *
     * @param  \App\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request, Interview $interview)
    {
        // Validate the request
        $results = Validator::make($request->all(), [
            'video' => 'required|file|mimetypes:video/mp4,video/mpeg,video/x-matroska,video/x-flv,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-m4v,video/3gpp,video/3gpp2',
            "questions" => "required|array",
            "questions.*.time"    => "required|numeric|min:0",
    	]);


        $questions = $request->questions;


        if ($results->fails()) {

            return response()->json([
                "errors"    => $results->errors()
            ], 401);
        } else if (count($questions) != count($interview->questions)) {
            return response()->json([
                "errors"    => [
                    "Questions number doesn't match"
                ]
            ], 401);
        } else if ($interview->org->user_id == Auth::user()->id) {
            return response()->json([
                "errors"    => [
                    "Can't do that action"
                ]
            ], 401);
        }

        // Check if the user submitted before
        $user = Auth::user();
        if ($interview->users->contains($user)) {
            return response()->json([
                "errors"    => [
                    "You submitted this interview before"
                ]
            ], 403);
        }


        // Add titles to questions

        for($i = 0; $i < count($questions); $i++) {
            $questions[$i]["title"] = $interview->questions[$i]["title"];
        }


        // Upload the video
        $videoPath = $request->video->store("public/videos");


        // Add new submit
        $submit = new Submit();
        
        $submit->user_id = $user->id;
        $submit->interview_id = $interview->id;
        $submit->questions = $questions;
        $submit->video = $videoPath;

        $submit->save();

        $submit->video = Storage::url($submit->video);

        return response()->json([
            "submit"    => $submit
        ], 200);
    }


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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Submit  $submit
     * @return \Illuminate\Http\Response
     */
    public function show(Submit $submit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Submit  $submit
     * @return \Illuminate\Http\Response
     */
    public function edit(Submit $submit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Submit  $submit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Submit $submit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Submit  $submit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submit $submit)
    {
        //
    }
}
