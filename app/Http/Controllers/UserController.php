<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // User Registration
    function UserRegistration(Request $request)
    {
      try{
        User::create([
         'firstName'=>$request->input('firstName'),
         'lastName'=>$request->input('lastName'),
         'email'=>$request->input('email'), 
         'mobile'=>$request->input('mobile'),
         'password'=>$request->input('password'),
          
      ]);
      return response()->json([
        'status'=> 'success',
        'message' => 'User Registered Successfully',
      ],200);
      }
      catch(\Exception $e){
        return response()->json([
          'status'=> 'error',
          'message' => 'User Registration Failed',
        ],500); 
      }
    }

    // User Login
    function UserLogin(Request $request){
   $count =  User::where('email', '=',$request->input('email'))
      ->where('password', '=',$request->input('password'))->count();

      if($count==1){
        // User Login -> JWT Token Issue
        $token = JWTToken::CreateToken($request->input('email'));
        return response()->json([
          'status'=> 'success',
          'message' => 'User Login Successfully',
          'token' => $token,
        ],200);

      }

      else{
        return response()->json([
          'status'=> 'error',
          'message' => 'User Login Failed',
        ],401);
      }
    }

    // User Recover
    
    
}
