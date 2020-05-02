<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Submit extends Model
{
    protected   $table = "submits",
                $id = "id",
                $casts = [
                    "questions" => "array"
                ];
    
    // Relationship with user
    public function user(){
        return $this->belongsTo("App\User", "user_id", "id");
    }


    // Ralationship with interview
    public function interview(){
        return $this->belongsTo("App\Interview", "interview_id", "id");
    }
}
