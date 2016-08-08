<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;
use App\Models\Operation;
use App\Models\Relationship;

class ClassObj extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locationX', 'locationY', 'name', 'type', 'package', 'model_id'
    ];

    public function Attributes(){
        return $this->hasMany("App\\Models\\Attribute", "class_id");
    }
    public function Operations() {
        return $this->hasMany("App\\Models\\Operation", "class_id");
    }
    public function StartingRelationship(){
        return $this->hasMany("App\\Models\\Relationship", "starting_class_id");
    }
    public function EndingRelationship(){
        return $this->hasMany("App\\Models\\Relationship", "ending_class_id");
    }
}
