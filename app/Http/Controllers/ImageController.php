<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Image;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['images']]);
    }
    
    public function upload(Request $request){

        $validator = Validator::make($request->all(),[
            'titulo' => 'string|required',
            'image_path' => 'image|required'
        ],[
            'image_path.image' => 'El archivo debe ser una imagen'
        ]);

        if($validator->fails()){
            return response()->json($validator->getMessageBag(),400);
        }

        $user = auth()->user();
        $image = new Image();
        $image->user_id = $user->id;
        $image->titulo = $request->input('titulo');
        $image->uploaded_at = date('Y-m-d H:i:s');

        $image_path = $request->file('image_path');

        $image_path_name = time() . $image_path->getClientOriginalName();
        Storage::disk('images')->put($image_path_name,File::get($image_path));
        $image->image_path = $image_path_name;

        $image->save();

        return response()->json([
            'id' => $image->id,
            'titulo' => $image->titulo,
            'user_id' => $image->user_id,
            'username' => $user->username,
            'avatar_path' => $user->avatar_path,
            'likes' => 0,
            'comment_number' => 0,
            'comments' => [],
            'uploaded_at' => $image->uploaded_at,
            'image_path' => $image->image_path
        ]);

    }

    public function getLikes($imageId){

        $user = auth()->user();

        $image = Image::where('id',$imageId)->first();

        if(!$image){
            return response()->json([
                'ok' => false,
                'msg' => 'La imagen no existe'
            ],400);
        }

        return response()->json([
            'likes' => count($image->likes)
        ]);

    }

    public function images($userId){
        
        $user = User::where('id',$userId)->first();

        if(!$user){
            return response()->json([
                'ok' => false,
                'msg' => 'Usuario no encontrado'
            ]);
        }

        $images = $user->images;

        return response()->json([
            'ok' => true,
            'images' => $images
        ]);

    }

    public function myImages(){

        $user = auth()->user();

        $images = $user->images;

        return response()->json([
            'ok' => true,
            'images' => $images
        ]);

    }

    public function getComments($imageId){

        $user = auth()->user();

        $image = Image::where('id',$imageId)->where('user_id',$user->id)->first();

        if(!$image){
            return response()->json([
                'ok' => false,
                'msg' => 'La imagen no existe'
            ],400);
        }

        return response()->json([
            'comments' => $image->comments
        ]);

    }
    
    public function dashboard(){

        $user = auth()->user();

        $data = Image::select('images.*','u.username as username','u.avatar_path as avatar_path',DB::raw('count(DISTINCT l.*) as likes,count(DISTINCT c.*) as comment_number'))
                ->join('users as u','images.user_id','=','u.id')
                ->joinWhere('follows','follows.followedby_id','=',$user->id)
                ->join('likes as l','l.image_id','=','images.id','left')
                ->join('comments as c','c.image_id','=','images.id','left')
                ->groupBy('images.id','u.id')
                ->orderBy('images.uploaded_at','DESC')->get();


        if(count($data) == 0){
            $data = Image::select('images.*','u.username as username','u.avatar_path as avatar_path',DB::raw('count(DISTINCT l.*) as likes,count(DISTINCT c.*) as comment_number'))
                    ->join('users as u','images.user_id','=','u.id')
                    ->join('likes as l','l.image_id','=','images.id','left')
                    ->join('comments as c','c.image_id','=','images.id','left')
                    ->having('u.id','=',$user->id)
                    ->groupBy('images.id','u.id')
                    ->orderBy('images.uploaded_at','DESC')->get();
        }


        return response()->json([
            'ok' => true,
            'data' => $data
        ]);

    }

}