<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function comment(Request $request,$imageId){

        $validator = Validator::make($request->all(),[
            'content' => 'string|required'
        ],[
            'content.string' => 'Debe ser texto',
            'content.required' => 'Campo obligatorio'
        ]);

        if($validator->fails()){
            return response()->json($validator->getMessageBag(),400);
        }

        $user = auth()->user();

        $image = Image::where('id',$imageId)->first();

        if(!$image){
            return response()->json([
                'ok' => false,
                'msg' => 'La imagen no existe'
            ],400);
        }

        $comment = new Comment();
        $comment->image_id = $image->id;
        $comment->user_id = $user->id;
        $comment->content = $request->input('content');

        $comment->save();

        return response()->json([
            'ok' => true,
            'comment' => [
                'content' => $comment->content,
                'id' => $comment->id,
                'user_id' => $comment->user_id
            ]
        ]);

    } 

    public function uncomment($commentId){

        $user = auth()->user();

        $comment = Comment::where('id',$commentId)->where('user_id',$user->id)->first();

        if(!$comment){
            return response()->json([
                'ok' => false,
                'msg' => 'No existe el comentario'
            ],400);
        }

        $comment->delete();

        return response()->json([
            'ok' => true
        ]);

    }
}
