<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\PasswordResetNotification;

// original import
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authen;

class User extends Authen implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'activated',
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'last_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
    * Get the roles of the user, all the roles user own
    * based off a pivot table, remember DB3 that class was useful
    * @return - Eloquent object that grabs role that belong to user
    */
    public function roles(){
       
       return $this->belongsToMany('App\Models\Roles', 'user_roles', 'user_id', 'role_id');
    }

    /**
    * Check whether the user is banned 
    */
    public function is_banned() {
        return $this->banned == 2;
    }

    /**
    * Check whether the user is suspended
    */
    public function is_suspended() {
        return $this->banned == 1;
    }

    /**
    * Check whether the user has a specific role
    * @param role - string containing the role we would like to check
    * @return Boolean whether the role is exists with the user
    */
    public function hasRole($role) {

        $roles_results = $this->roles;

        $roles_array = [];

        // loop through roles and get name and place in array
        foreach($roles_results as $role_item) {
            array_push($roles_array,$role_item->name);
        }

        return in_array($role, $roles_array);
    }

    /**
    * Reports about user, reports on this user
    * How many accusations of this user has receieved
    */
    public function reports() {
        return $this->hasMany('App\Models\Report', 'reporter_id');
    }

    /*
    * Get all the posts associated with the user
    */
    public function posts() {
        return $this->hasMany('App\Models\Post', 'user_id');
    }

     /*
    * Get all the posts associated with the user
    */
    public function replies() {
        return $this->hasMany('App\Models\Reply', 'user_id');
    }

    /*
    * Get the object representing the profile image assigned to the user
    */
    public function profile_image() {
        return $this->hasOne('App\Models\Profile_Image', 'id', 'profile_image_id');
    }

    public function likes() {
        return $this->hasMany('App\Models\Post_Like');
    }

    public function dislikes() {
        return $this->hasMany('App\Models\Post_Dislike');
    }
}
