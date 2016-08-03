<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Attribute;
use App\Operation;

class ClassObj extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locationX', 'locationY', 'name', 'type', 'package', 'model_id'
    ];

    public function Attributes(){
        return $this->hasMany("App\\Models\\Attribute");
    }
    public function Operations() {
        return $this->hasMany("App\\Models\\Operation");
    }
}
