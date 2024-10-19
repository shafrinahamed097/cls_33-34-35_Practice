<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\OTPMail;
use Exception;  
use Illuminate\Support\Facades\Mail;


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

    // OTP Send
    function SendOTPCode(Request $request){
      $email=$request->input('email');
      $otp= rand(1000,9999);
      $count = User::where('email', '=',$email)->count();
      
      if($count==1){
        // OTP Email Address
        Mail::to($email)->send(new OTPMail($otp));
        


        //OTP Code Table Update
        User::where('email', '=',$email)->update(['otp' => $otp]);
        return response()->json([
          'status'=> 'success',
          'message' => '4 Digits OTP Send Successfully',
        ]);

      }else{
        return response()->json([
          'status'=> 'unauthorized',
        ]);
      }
    }

    // Verify OTP
    function VerifyOTP(Request $request){
      $email=$request->input('email');
      $otp=$request->input('otp');
      $count = User::where('email', '=',$email)->where('otp','=',$otp)->count();
      if($count==1){
        // Database Update
        User::where('email', '=',$email)->update(['otp' => '0']);

        // Pass Reset Token Issue
        $token=JWTToken::CreateTokenForSetPassword($request->input('email'));
        return response()->json([
          'status'=> 'success',
          'message' => 'OTP Verify Successfully',
          'token' => $token,
        ]);


      }else{
        return response()->json([
          'status'=> 'unauthorized',
        ]);
      }

    }

    // Password Reset
    function ResetPassword(Request $request){
     try{ $email=$request->headers('email');
      $password=$request->input('password');
      User::where('email', '=',$email)->update(['password' => $password]);
      return response()->json([
        'status'=> 'success',
        'message' => 'Password Reset Successfully',
      ],200);
    }
    catch(Exception $e){
      return response()->json([
        'status'=> 'error',
        'message' => 'Password Reset Failed',
      ],500);
    }
  
  }
      
    
}
