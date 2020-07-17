<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at', 'pic'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Attribute to be appended while serializing
     */
    protected $appends = [
        "profile_pic"
    ];


    /**
     * Get the full path for the profile picture
     */
    public function getProfilePicAttribute($value){
        
        return $this->attributes['pic'] ? url(Storage::url($this->attributes['pic'])) : null;
    }


    // Relationship with organization that user owns
    public function orgs(){
        return $this->hasMany("App\Org", "user_id", "id");
    }

    /**
     * Relationship with orgs that user work on
     */
    public function workingOrgs(){
        return $this->belongsToMany("App\User", "org_user");
    }


    // Relationship with submits
    public function submits(){
        return $this->hasMany("App\Submit", "user_id", "id");
    }

    // Relationship with interviews
    public function interviews(){
        return $this->belongsToMany("App\Interview", "submits", "user_id", "interview_id");
    }
}
