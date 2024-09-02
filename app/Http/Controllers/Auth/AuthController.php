<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login()
    {
        try {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token creation failed'], 500);
        }
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'message' => 'Valid Request',
            'user'    => Auth::user(),
            'token'   => $request->bearerToken()
        ], 200);
    }

    public function logout($request)
    {

    }
}
