<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $table = "follows";
    public $timestamps = false;

    public function followedBy(){
        return $this->belongsTo('App\Models\User','followedby_id');
    }

    public function userFollowed(){
        return $this->belongsTo('App\Models\User','followed_id');
    }

}
