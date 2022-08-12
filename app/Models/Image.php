<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $image = 'images';
    public $timestamps = false;

    public function comments(){
        return $this->hasMany('App\Models\Comment')->select('id','content','user_id')->orderBy('id','asc');
    }

    public function likes(){
        return $this->hasMany('App\Models\Like');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

}
