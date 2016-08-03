<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\User;

class Language extends Model
{
    public function Projects(){
        return $this->hasMany("App\\Models\\Project");
    }
}
