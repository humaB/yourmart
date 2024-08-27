<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        
        if ( Auth::check() ) {
            return view('dashboard');
        }

        return view('auth.login');
    }

    public function login( Request $request ){
        //Signing in user
        if( !auth()->attempt( $request->only('email','password') ) ) {
            return back()->with('status' , 'Invalid login credential');
        } 

        session()->put('email' , $request->email);
        session()->put('password' , $request->password);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request){        
        Auth::logout();

        return redirect()->route('dashboard');
    }

}

