<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtToken 
{
    
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next

    * @return mixed
    */
    public function handle($request, Closure $next)
    
    {
    
    try {
    
    $user = JWTAuth::parseToken()->authenticate();
    
    if( !$user ) throw new Exception('User Not Found');
    
    } catch (Exception $e) {
    
    if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
    
    return response()->json([
        'status'        => 'error',
        'statusMessage' => 'Invalid Token',
        'httpCode'      => '401',
        'errorCode'     => '1003',
        'response'      => ''
    ],401);
    
    }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
    
    return response()->json([
        'status'        => 'error',
        'statusMessage' => 'Token has expired',
        'httpCode'      => '401',
        'errorCode'     => '1002',
        'response'      => ''
    ],401);
    
    }
    
    else{
    
    if( $e->getMessage() === 'User Not Found') {
    
    return response()->json([
        'status'        => 'error',
        'statusMessage' => 'User Not Found',
        'httpCode'      => '401',
        'errorCode'     => '0',
        'response'      => ''
    ],401);
    }
    
    return response()->json([
        'status'        => 'error',
        'statusMessage' => 'Authorization Token required',
        'httpCode'      => '401',
        'errorCode'     => '1004',
        'response'      => ''
    ],401);
    
    }
    
    }
        return $next($request);
    }
}
