<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;

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

        $auth = new AuthController();
        $auth->login();

        return redirect()->route('dashboard');
    }

    public function profileSettingIndex(){
        return view('auth.profile_setting');
    }

    public function getProfile(){

        $user = User::where('id', auth()->user()->id ?? 0)->first();

        return response()->json([
            "user" => $user
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }
        
        User::where("id",$request->id)->update([
            'name'     => $request->name,
            'email'    => $request->email,
        ]);
        
        if($request->new_password)
        {
            User::where("id",$request->id)->update([
                'password'    => Hash::make($request->new_password),
            ]);
        }

        return (new ResponseCollection( [] ))
            ->response()
            ->setStatusCode(200);
    }

    public function logout(Request $request){

        $auth = new AuthController();
        $auth->logout( $request );
        Auth::logout();

        return redirect()->route('dashboard');
    }

    private function validation($validator)
    {

        if ($validator->fails()) {

            $validationErrors = [];
            $errors = $validator->errors()->all();

            foreach ($errors as $error) {
                array_push($validationErrors, $error);
            }

            return (new ValidationCollection($validationErrors))
                ->response()
                ->setStatusCode(400);
        }
    }

}

