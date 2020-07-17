<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use DateTimeInterface;
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

    /**
     * Relationship with users
     */
    public function users(){
        return $this->belongsToMany("App\User", "org_user");
    }

    // Is owner attribute
    public function getIsOwnerAttribute(){
        return $this->attributes['user_id'] == Auth::user()->id;
    }

    // Relationship with intervies
    public function interviews(){
        return $this->hasMany("App\Interview", "org_id", "id");
    }

    // Relationship with submits
    public function submits(){
        return $this->hasManyThrough("App\Submit", "App\Interview", "org_id", "interview_id");
    }

     /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d F y D g:i A');
    }
}
