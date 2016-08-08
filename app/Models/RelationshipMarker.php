<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Relationship;

class RelationshipMarker extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relationship_markers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type'
    ];

    // public $timestamps = false;
    
    public function Relationship() {
         return $this->belongsTo("App\\Models\\Relationship");
    }
}
