<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Org extends Model
{

    use Sluggable;

    protected   $table = "orgs",
                $primaryKey = "id";
    
    

    
    // Sluggable
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

        

    public function user(){
        return $this->belongsTo("App\User", "user_id", "id");
    }
}
