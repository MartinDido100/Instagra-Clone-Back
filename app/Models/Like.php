<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function image(){
        return $this->belongsTo('App\Models\Image','image_id');
    }

}
