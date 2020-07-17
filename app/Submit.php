<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Submit extends Model
{
    protected   $table = "submits",
                $id = "id",
                $casts = [
                    "questions" => "array"
                ],
                $hidden = [ "cv" ];

    protected   $appends = [
        "cv_file"
    ];
    
    // Relationship with user
    public function user(){
        return $this->belongsTo("App\User", "user_id", "id");
    }


    // Ralationship with interview
    public function interview(){
        return $this->belongsTo("App\Interview", "interview_id", "id");
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

    public function getCvFileAttribute(){
        return $this->attributes['cv'] ? Storage::url($this->attributes['cv']) : null;
    }
}
