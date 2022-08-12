<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Follower;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    public function update(Request $request){

        $user = auth()->user();

        $id = $user->id;

        $validate = Validator::make($request->all(),[
            'name' => 'string|required',
            'surname' => 'string|required',
            'username' => "string|required|unique:users,username,$id",
            'email' => "email|required|unique:users,email,$id",
            'avatar_path' => 'image'
        ]);

        if($validate->fails()){
            return response()->json($validate->getMessageBag(), 400);
        }

        $name = $request->input('name');
        $surname = $request->input('surname');
        $username = $request->input('username');
        $email = $request->input('email');

        $user->name = $name;
        $user->surname = $surname;
        $user->username = $username;
        $user->email = $email;

        $image_path = $request->file('avatar_path');
        if($image_path){
            $image_path_name = time() . $image_path->getClientOriginalName();
            Storage::disk('users')->put($image_path_name,File::get($image_path));

            $user->avatar_path = $image_path_name;
        }

        $user->update();

        return response()->json([
            'ok' => true,
            'user' => $user,
        ]);

    }

    public function follow($userId){

        $loggedUser = auth()->user();

        if($loggedUser->id == $userId){
            return response()->json([
                'ok' => false,
                'msg' => 'No te podes seguir a vos mismo'
            ],400);
        }

        $follow = new Follow();

        $follow->followedby_id = $loggedUser->id;
        $follow->followed_id = $userId;
        $follow->save();

        return response()->json([
            'ok' => true,
            'followed' => $follow->userFollowed
        ]);

    }

    public function unfollow($userId){

        $loggedUser = auth()->user();

        if($loggedUser->id == $userId){
            return response()->json([
                'ok' => false,
                'msg' => 'No te podes seguir a vos mismo'
            ],400);
        }

        $follow = Follow::where('followedby_id',$loggedUser->id)->where('followed_id',$userId)->first();

        if(!$follow){
            return response()->json([
                'ok' => false,
                'msg' => 'Error de servidor'
            ],400);
        }

        $follow->delete();

        return response()->json([
            'ok' => true
        ]);

    }

    public function followers(){

        $user = auth()->user();

        $followers = $user->followers;

        foreach ($followers as $index => $follow) {
            $followers[$index] = $follow->followedBy;
        }


        return response()->json([
            'followers' => $followers
        ]);

    }

    public function following(){

        $user = auth()->user();

        $follows = $user->follows;

        foreach ($follows as $index => $follow) {
            $follows[$index] = $follow->userFollowed;
        }

        return response()->json([
            'following' => $follows
        ]);

    }

    public function search($query){

        $users = User::select()->where('username','LIKE',"%$query%")->orderBy('id','desc')->get();

        return response()->json($users);

    }

    public function getOne($username){

        $user = User::where('username','=',$username)->first();

        if(!$user){
            return response()->json([
                'ok' => false,
                'msg' => 'No se encontro el usuario'
            ]);
        }

        $images = Image::select('images.*','u.username as username','u.avatar_path as avatar_path',DB::raw('count(DISTINCT l.*) as likes,count(DISTINCT c.*) as comment_number'))
        ->join('users as u','images.user_id','=','u.id')
        ->join('likes as l','l.image_id','=','images.id','left')
        ->join('comments as c','c.image_id','=','images.id','left')
        ->having('u.id','=',$user->id)
        ->groupBy('images.id','u.id')
        ->orderBy('images.uploaded_at','DESC')->get();

        foreach($images as $image){
            $image->comments;
            foreach($image->comments as $comment){
               $comment->user;
            }
        }

        return response()->json([
            'ok' => true,
            'id' => $user->id,
            'name'=> $user->name,
            'images' => $images,
            'followers' => count($user->follows),
            'follows' => count($user->followers),
            'avatar_path' => $user->avatar_path,
        ]);

    }

}