<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(!$token = Auth::guard('api')->attempt($credentials)){
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => Auth::guard('api')->user()
        ]);
    }

    public function userId(){
        return response()->json([
            'success' => true,
            'user' => Auth::guard('api')->user()
        ]);
    }

    public function logout(){
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil'
        ]);
    }

    
}
