<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\Models\User','user_id')->select('name','surname','username','avatar_path');
    }

    public function image(){
        return $this->belongsTo('App\Models\Image','image_id');
    }


}
