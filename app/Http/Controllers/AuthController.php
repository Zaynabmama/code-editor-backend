<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\UsersImport;



class AuthController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
           // 'user_role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'user_role' => $request->user_role,
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function import(Request $request)
        {
   
    $request->validate([
        'file' => 'required|file|mimes:xlsx,csv',
    ]);

    try {
        
        Excel::import(new UsersImport, $request->file('file'));

        return response()->
        json(['success' =>
         'Users imported successfully!']
         , 200);
        
        //return Redirect::back();
    } catch (ValidationException $e) {
        $failures = $e->failures();

        $messages = collect($failures)->map(function ($failure) {
            $row = $failure->row(); 
            $attribute = $failure->attribute();
            $errors = implode(', ', $failure->errors());
            return "$errors : at row $row and col $attribute";
        })->toArray();

        
        //return Redirect::back()->withErrors($messages);
        return response()->json(['errors' => $messages], 422);
    }  catch (\Exception $e) {
        Log::error('General Exception:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json(['errors' => ['Unexpected error occurred.']], 500);
    }
    }
    public function searchByName(Request $request)
   {
    $request->validate([
        'name' => 'required|string',
    ]);

    $name = $request->input('name');
    $users = User::where('name', 'LIKE', "%$name%")->get();

    return response()->json([
        'status' => 'success',
        'users' => $users,
    ]);
}
       public function listAllUsers()
       {
         $users = User::all();

           return response()->json([
           'status' => 'success',
          'users' => $users,
        ]);
}
public function updateUser(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
    ]);

    $user = User::find($id);

 
    $user->name = $request->input('name');
    $user->email = $request->input('email');

    $user->save();

    return response()->json([
        'status' => 'success',
        'message' => 'User updated successfully',
        'user' => $user,
    ]);
}
public function deleteUser(Request $request, $id)
{
   
    if (Auth::id() == $id) {
        return response()->json([
            'status' => 'error',
            'message' => 'You cannot delete your own account',
        ], 403);
    }

   
    $user = User::find($id);

   
    $user->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'User deleted successfully',
    ]);
}


}
