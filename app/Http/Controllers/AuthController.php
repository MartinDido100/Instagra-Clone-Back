<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','verify']]);
    }

    public function login()
    {
        $credentials = request(['username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json([
                'ok' => false,
                'msg' => 'Credenciales incorrectas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function verify()
    {
        $user = auth()->user();

        return response()->json([
            'ok' => true,
            'token' => auth()->refresh(),
            'user' => $user,
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['ok' => true]);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function register(Request $request){

        $validate = Validator::make($request->all(),[
            'name' => 'string|required',
            'surname' => 'string|required',
            'username' => 'string|required|unique:users,username',
            'email' => 'email|required|unique:users,email',
            'password' => 'string|required|min:6'
        ],[
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener 6 caracteres'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'avatar_path' => null
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'ok' => true,
            'token' => $token
        ]);
    }

}
