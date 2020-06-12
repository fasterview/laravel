<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected   $table = "interviews",
                $primaryKey = "id",
                $casts  = [
                    "questions" => "array"
                ];

    
    // Relationship with org
    public function org(){
        return $this->belongsTo("App\Org", "org_id", "id");
    }

    // Relationship with submits
    public function submits(){
        return $this->hasMany("App\Submit", "interview_id", "id");
    }

    // Relationship with users
    public function users(){
        return $this->belongsToMany("App\User", "submits", "interview_id", "user_id");
    }

}
