<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Auth;

class Org extends Model
{

    use Sluggable;

    protected   $table = "orgs",
                $primaryKey = "id";
    
    protected $appends = ["is_owner"];

    
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

    // Is owner attribute
    public function getIsOwnerAttribute(){
        return $this->attributes['user_id'] == Auth::user()->id;
    }

    // Relationship with intervies
    public function interviews(){
        return $this->hasMany("App\Org", "org_id", "id");
    }
}
