<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Org extends Model
{
    protected   $table = "orgs",
                $primaryKey = "id";
    
    
    public function user(){
        return $this->belongsTo("App\User", "user_id", "id");
    }
}
