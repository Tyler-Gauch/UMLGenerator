<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
