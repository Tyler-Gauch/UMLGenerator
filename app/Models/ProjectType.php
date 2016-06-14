<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\User;

class ProjectType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function Projects(){
        return $this->hasMany("App\\Models\\Project");
    }
}
