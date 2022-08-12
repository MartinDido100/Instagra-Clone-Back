<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Auth routes(Done)
Route::group([

    'middleware' => 'api'

], function () {

    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::get('verify', [AuthController::class,'verify'])->middleware('refresh');

});


//User routes (Done)
Route::middleware('auth:api')->controller(UserController::class)->prefix('user')->group(function(){

    Route::post('update','update');
    Route::post('follow/{userId}','follow');
    Route::post('unfollow/{userId}','unfollow');
    Route::get('followers','followers');
    Route::get('following','following');
    Route::get('search/{query}','search');
    Route::get('getOne/{username}','getOne');


});


//Like routes (Done)
Route::middleware('auth:api')->controller(LikeController::class)->group(function(){

    Route::post('like/{imageId}','like');
    Route::post('dislike/{imageId}','dislike');
    Route::get('mylikes','myLikes');

});

//Image routes (Done)
Route::middleware('auth:api')->controller(ImageController::class)->prefix('image')->group(function(){

    Route::post('upload','upload');
    Route::get('dashboard','dashboard');
    Route::get('all/{userId}','images');
    Route::get('/','myImages');
    Route::get('likes/{imageId}','getLikes');
    Route::get('comments/{imageId}','getComments');

});

//Comment routes (Done)
Route::middleware('auth:api')->controller(CommentController::class)->group(function(){

    Route::post('comment/{imageId}','comment');
    Route::post('uncomment/{commentId}','uncomment');

});