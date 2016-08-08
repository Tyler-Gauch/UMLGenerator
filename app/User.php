<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;
use Log;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', "provider_id", "username", "access_token"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
    * Get the roles a user has
    */
    public function Roles()
    {
       return $this->belongsToMany('App\\Models\\Role', "user_roles");
    }

    /**
     * Get key in array with corresponding value
     *
     * @return int
     */
    private function getIdInArray($array, $term)
    {
        foreach ($array as $key => $value) {
		if ($value["name"] == $term) {
                return $value["id"];
            }
        }
        return -1;
    }

    function addRole($role)
    {
        $roles = Role::all()->toArray();

        $id = $this->getIdInArray($roles, $role);

        if($id == -1)
        {
            Log::error("Role: {$role} not found");
            return;
        }

        $this->Roles()->attach($id);
    }

    function hasRole($role)
    {
        Log::info("hasRole($role)");
        foreach($this->Roles as $key=>$value)
        {
            Log::info("{$value->name} == $role");
            if($value->name == $role)
            {
                return true;
            }
        }
        return false;
    }
}
