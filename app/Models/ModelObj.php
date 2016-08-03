<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\ClassObj;

class ModelObj extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'models';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch', 'project_id'
    ];

    public function ClassObj(){
        return $this->hasMany("App\\Models\\ClassObj", "model_id");
    }
}
