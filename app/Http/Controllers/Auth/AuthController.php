<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(){
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(Request $request){
        $credentials = ['email' => $request->session()->get('email') , 'password' => $request->session()->get('password')];

        //Forget Session
        $request->session()->forget('email');
        $request->session()->forget('password');
    
        
        if ( !$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status'        => 'success',
            'statusMessage' => 'Token Created',
            'httpCode'      => '200',
            'errorCode'     => '0',
            'response'  =>[
                'access_token'     => $token,
                'token_type'       => 'bearer',
                'expires_in'       => auth('api')->factory()->getTTL() * 60
            ]
        ]);
    }
}