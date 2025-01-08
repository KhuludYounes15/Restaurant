<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use App\Models\PasswordResett;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetNotification;
class UserController extends Controller
{
    use GeneralTrait;

    /**
     * Register a new user.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
       {

            try {
                // Create a new user with validated data
                $user = User::create([
                    'name' => $request->validated('name'),
                    'phone' => $request->validated('phone'),
                    'password' => Hash::make($request->validated('password')),
                    'email' => $request->validated('email'),
                    'uuid' => Str::uuid(),
                ]);

                // Optionally, you could use a resource to format the user response
                return $this->apiResponse(new UserResource($user), true, 'Account successfully created', 200);
            } catch (\Exception $e) {
                return $this->apiResponse(null, false, 'An error occurred while creating the account. Please try again later.', 500);
            }
        }

    

    public function login(Request $request)
      {  

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email', // Ensure email is required and valid
                'password' => 'required|min:5', // Ensure password is required and has a minimum length
            ]);

            // If validation fails, return the first error message
            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            try {
                // Attempt to find a user with the provided email
                $user = User::where('email', $request->email)->first();

                // If no user is found, return an error response
                if (!$user) {
                    return $this->apiResponse(null, false, 'Try again..Invalid Email or Password!!', 401);
                }

                // Check if the provided password matches the user's hashed password
                if (!Hash::check($request->password, $user->password)) {
                    return $this->apiResponse(null, false, 'Try again..Invalid Email or Password!!', 401);
                }

                // Create a new token for the authenticated user
                $token = $user->createToken('MyApp')->plainTextToken;

                // Return a successful response with the generated token
                return $this->apiResponse('token:' . $token, true, null, 200);
                
            } catch (\Exception $e) {
                // If an exception occurs, return an error response with the exception message
                return $this->apiResponse(null, false, $e->getMessage(), 500);
            }
      }

    

    public function logout()
      {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return $this->unAuthorizeResponse();
            }
            
            $user->currentAccessToken()->delete();
            
            return $this->apiResponse(null, true,'You have successfully logged out', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    public function forget(ForgetPasswordRequest $request)
      {
    
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->apiResponse(null, false, 'No record found. Incorrect email address provided.', 404);
            }

            // Generate a secure token
            $resetPasswordToken=str_pad(random_int(1,9999),4,'0',STR_PAD_LEFT);
            if(!$userPassReset=PasswordResett::where('email',$user->email)->first())
            {
                PasswordResett::create([
                    'email' => $user->email,
                    'token' => $resetPasswordToken
                ]);
            }
            else{
            // Update or create password reset token
            PasswordResett::update([
                'email' => $user->email,
                'token' => $resetPasswordToken]
            );
        }
            // Notify the user with the generated token
            $user->notify(new PasswordResetNotification($user, $resetPasswordToken));

            return $this->apiResponse(null, true, 'A password reset link has been sent to your email address.', 200);
    }

    

    public function reset( ResetPasswordRequest $request)
     {
        
        $attributes = $request->validated();
        
        // Find the user by email
        $user = User::where('email', $attributes['email'])->first();
        if (!$user) {
            return $this->apiResponse(null, false, 'No record found with the provided email address.', 404);
        }

        // Check for the password reset request
        $resetRequest = PasswordResett::where('email', $user->email)->first();
        if (!$resetRequest || $resetRequest->token !== $request->token) {
            return $this->apiResponse(null, false, 'Invalid token or expired request.', 400);
        }
            // Update the user's password
            $user->update([
                'password' => Hash::make($attributes['password']),
            ]);

            // Delete the reset request and any existing tokens
            $user->tokens()->delete();
            $resetRequest->delete();

        // Create a new token for the user
        $token = $user->createToken('MyApp')->plainTextToken;
        $loginResponse = [
            'user' => $user,
            'token' => $token,
        ];

        return $this->apiResponse($loginResponse, true, null, 200);
    }

        }

     


    
