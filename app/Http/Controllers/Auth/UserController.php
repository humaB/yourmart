<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::get();

        return view('auth.user.users',[
            'users' => $users
        ]);
    }

    public function  getUsers(){
        
        $data = User::get();
        
        return ( new ResponseCollection ( $data ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){
        
        //Validation
        $this->validate($request , [
            'name'     => 'required|max:255',
            'email'    => 'required',
            'role'     => 'required',
            'password' => 'required',
            'ip'       => 'required'
        ]);
 
        $exist = User::where( 'email' , $request->email )->count();
        if($exist > 0){
            //Redirect with error
            return redirect()->back()->with('danger', 'Email Already registered !');
         }
         //Store Data to Database
         User::create([
          'name'        => $request->name,
          'email'       => $request->email,
          'role'        => $request->role,
          'password'    => Hash::make($request->password),
          'branch_id'      => $request->branch
         ]);
 
        //Redirect with success
        return redirect()->back()->with('success', 'Successfully Added !');
        
    }
}
