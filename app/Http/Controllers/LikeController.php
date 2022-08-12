<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    
    public function like($imageId){

        $user = auth()->user();

        $like = Like::where('image_id',$imageId)->count();

        if($like > 0){
            return response()->json([
                'ok' => false,
                'msg' => 'Ya tiene like'
            ],400);
        }

        $like = new Like();
        $like->user_id = $user->id;
        $like->image_id = $imageId;
        $like->save();

        return response()->json([
            'ok' => true
        ]);

    }

    public function myLikes(){

        $user = auth()->user();

        $data = Like::select('likes.image_id')
                ->where('likes.user_id',$user->id)
                ->get();

        return response()->json([
            'myLikes' => $data
        ]);

    }


    public function dislike($imageId){

        $user = auth()->user();

        $like = Like::where('image_id',$imageId)->first();

        if(!$like){
            return response([
                'ok' => false,
                'msg' => 'No existe el like'
            ],400);
        }

        $like->delete();

        return response()->json([
            'ok' => true
        ]);
        
    }

}
