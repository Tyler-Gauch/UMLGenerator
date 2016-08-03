<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\User;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id', 'language_id', "project_type_id"
    ];


    public function User(){
        return $this->belongsTo("App\\User");
    }

    public function ProjectType(){
        return $this->belongsTo("App\\Models\\ProjectType");
    }
}
