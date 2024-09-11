<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        return view('auth.user.users', [
            'users' => $users
        ]);
    }

    public function  getUsers()
    {

        $data = User::get();

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function store(Request $request)
    {
        //Validation
        $validator = \Validator::make($request->all(), [
            'name'     => 'required|max:255',
            'email'    => 'required',
            'role'     => 'required',
            'password' => 'required',
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }

        $exist = User::where('email', $request->email)->count();
        if ($exist > 0) {
            return (new ValidationCollection(['This email is already registered']))
            ->response()
            ->setStatusCode(400);

        }
        //Store Data to Database
        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'password'    => Hash::make($request->password),
            'allowed_ip_address' => '*'
        ]);

        //Redirect with success
        return redirect()->back()->with('success', 'Successfully Added !');
    }

    public function update(Request $request)
    {
        // Validation
        $validator = \Validator::make($request->all(), [
            'name'  => 'required|max:255',
            'email' => 'required|email',
            'role'  => 'required',
            'password' => 'nullable|min:4',
            'oldPassword' => 'required_with:password', // Custom validation for old password
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }
        $id = $request->id;
        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if the email is already taken by another user
        $emailExists = User::where('email', $request->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($emailExists) {
            return (new ValidationCollection(['This email is already registered!']))
            ->response()
            ->setStatusCode(400);
        }

        if ($request->password && !\Hash::check($request->oldPassword, $user->password)) {
            return (new ValidationCollection(['Incorrect old password!']))
                ->response()
                ->setStatusCode(400);
        }

        // Update user data
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        // Update password only if it is sent from the frontend
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Optionally update allowed_ip_address or keep existing value
        $user->allowed_ip_address = $request->input('allowed_ip_address', $user->allowed_ip_address);

        // Save the changes
        $user->save();

        // Redirect with success
        return redirect()->back()->with('success', 'Successfully Updated!');
    }

    private function validation($validator){

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
