<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
        'avatar_path'
    ];

    protected $hidden = [
        'password'
    ];

    public function images(){
        return $this->hasMany('App\Models\Image')->orderBy('id','desc');
    }

    public function follows(){
        return $this->hasMany('App\Models\Follow','followedby_id')->orderBy('id','desc');
    }

    public function followers(){
        return $this->hasMany('App\Models\Follow','followed_id')->orderBy('id','desc');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
