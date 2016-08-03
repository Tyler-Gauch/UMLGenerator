<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\ClassObj;

class Attribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'class_id', 'visibility', 'type', 'default_value'
    ];

    public $timestamps = false;
    
    public function ClassObj() {
         return $this->belongsTo("App\\Models\\ClassObj");
    }
}
