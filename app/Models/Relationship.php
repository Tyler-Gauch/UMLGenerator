<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\ClassObj;
use App\Models\RelationshipLine;
use App\Models\RelationshipMarker;

class Relationship extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relationships';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'starting_class_id', 'ending_class_id', 'starting_marker_id', 'ending_marker_id', 'line_id'
    ];

    public function StartingClass() {
        return $this->belongsTo("App\\Models\\ClassObj", "starting_class_id");        
    }
    public function EndingClass() {
        return $this->belongsTo("App\\Models\\ClassObj", "ending_class_id");        
    }
    public function RelationshipLine(){
        return $this->belongsTo("App\\Models\\RelationshipLine", "line_id");
    }
    public function StartingRelationshipMarker() {
        return $this->belongsTo("App\\Models\\RelationshipMarker", "starting_marker_id");
    }
    public function EndingRelationshipMarker() {
        return $this->belongsTo("App\\Models\\RelationshipMarker", "ending_marker_id");
    }
}
