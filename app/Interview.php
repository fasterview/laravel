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
}
