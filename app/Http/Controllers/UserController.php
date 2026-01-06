<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAdminUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreRegisterUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Mail\ForgotPasswordLink;
use App\Models\Post;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserPayment;
use App\Models\Vacancy;
use App\Rules\UserExistsWithOffset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()//for admin only
    {
        
        $users = User::with([
            'userDetails',
            'userEducationDetails',
            'userExperienceDetails',
            'userDocuments',
            'userPaymentDetails'
        ])->where('role', '!=', 'admin')->get();
        // $users = User::with('userDetails')->with('userEducationDetails')->get();


        if(!$users){
            return $this->errorResponse("User data not found!!");
        }

        return $this->successResponse($users, "List of users", 200);

    }

    /**
     * Store a newly created resource in storage.
     * User Registration
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create($request->validated());

            // Create the user details, assuming UserDetail has a user_id foreign key
            UserDetail::create([
                'user_id' => $user->id,
            ]);

            // Commit the transaction
            DB::commit();

            // return response()->json(['message' => 'User and user details created successfully.'], 201);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();

            // Handle the error (log it, return a response, etc.)
            return $this->errorResponse("Error creating User and User details!!");
        }

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        // $success['name'] = $user->name;
        $success['email'] = $user->email;
        $success['id'] = $this->getUserRegistrationNumber($user->id);

        //email data
        $data['email'] = $user->email;
        $data['password'] = $request->password;
        $data['user_id'] = $this->getUserRegistrationNumber($user->id);
        $data['vacancy'] = Vacancy::whereId($user->vacancy_id)->first();
        $data['post'] = Post::whereId($user->post_id)->where('vacancy_id', $user->vacancy_id)->first();
        // dd($data);

        Mail::to($user->email)->send(new UserRegistered($data));

        return $this->successResponse($success, "New user registered and email sent!!");
    }

    /**
     * Login user.
     */
    public function login(LoginUserRequest $request)
    {
        // $id = $request->id - 12453289;
        $id = $this->getUserIdFromRegistrationNumber($request->id);
        // Check if the user ID exists
        $user = User::whereId($id)->where('email', $request->email)->where('enabled', 1)->where('vacancy_id', $request->vacancy_id)->where('post_id', $request->post_id)->first();

        // Check if the user exists
        if (!$user) {
            return $this->errorResponse('User data does not exist. '.$id, 404);
        }

 
        // if ($user->email_verified_at === null) {
        //     return $this->errorResponse('User E-mail not verified.', 404);
        // }
        
        if (!Hash::check($request->password, $user->password)) {
            // Password does not match
            return $this->errorResponse("Invalid password!");
        }
        if(!Auth::attempt(['id' => $id, 'email' => $request->email, 'password' => $request->password, 'vacancy_id' => $request->vacancy_id, 'post_id' => $request->post_id, 'enabled' => 1])){
            return $this->errorResponse("Invalid login parameter!!");
        }


        $user = Auth::user();
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;
        $success['email_verified_at'] = $user->email_verified_at;
        $success['id'] = $this->getUserRegistrationNumber($user->id);
        $success['vacancy_id'] = $user->vacancy_id;
        $success['post_id'] = $user->post_id;
        $success['registration_status'] = $user->registration_status;

        return $this->successResponse($success, "User login success!!");
    }

    public function loginAdmin(LoginAdminUserRequest $request)
    {
        // $id = $request->id - 12453289;
        $id = $this->getUserIdFromRegistrationNumber($request->id);
        // Check if the user ID exists
        $user = User::whereId($id)->where('email', $request->email)->where('enabled', 1)->where('vacancy_id', $request->vacancy_id)->where('post_id', $request->post_id)->first();

        // Check if the user exists
        if (!$user) {
            return $this->errorResponse('User data does not exist.', 404);
        }

 
        // if ($user->email_verified_at === null) {
        //     return $this->errorResponse('User E-mail not verified.', 404);
        // }
        
        if (!Hash::check($request->password, $user->password)) {
            // Password does not match
            return $this->errorResponse("Invalid password!");
        }
        if(!Auth::attempt(['id' => $id, 'email' => $request->email, 'password' => $request->password, 'enabled' => 1])){
            return $this->errorResponse("Invalid login parameter!!");
        }


        $user = Auth::user();
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;
        $success['email_verified_at'] = $user->email_verified_at;
        // $success['id'] = $user->id+12453289;
        $success['id'] = $this->getUserRegistrationNumber($user->id);

        return $this->successResponse($success, "Admin login success!!");
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        // $request->user()->currentAccessToken()->delete();//current device log out

        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();//all devices log out

        return $this->successResponse(null, "Successfully logged out");
    }

    public function forgotPasswordSendResetLinkEmail(Request $request){
        $request->validate([
            'id' => ['required', 'numeric', new UserExistsWithOffset()],
            'email' => 'required|email|exists:users,email',
        ]);
        // $id = $request->id - 12453289;
        $id = $this->getUserIdFromRegistrationNumber($request->id);
        if (!User::find($id)) {
            return $this->errorResponse('User ID does not exist.', 404);
        }
        $user = User::whereId($id)->where('email', $request->email)->where('enabled', 1)->first();
        try{
            if($user){
                //user found send reset link email
                $data['user_id'] = $request->id;
                $data['email'] = $user->email;
                $data['vacancy'] = Vacancy::whereId($user->vacancy_id)->first();
                $data['post'] = Post::whereId($user->post_id)->where('vacancy_id', $user->vacancy_id)->first();
                // http://192.168.16.202:3000/VAC002/POST2B/resetPassword
                $resetToken = Str::random(12);
                //save reset token to user table
                $user->reset_token = $resetToken;
                $user->save();

                 $data['link_token'] = "http://192.168.16.202:3000/".$data['vacancy']['code']."/".$data['post']['code']."/resetPassword/".$request->id."/".$resetToken;

                Mail::to($user->email)->send(new ForgotPasswordLink($data));

                return $this->successResponse(null, "Forgot password link email sent!!");

            }
            return $this->errorResponse("Forgot password failed!!");

        } catch (\Exception $e) {
            // Handle the error (log it, return a response, etc.)
            return $this->errorResponse("Error in forgot password!!");
        }

    }

    public function passwordReset(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => ['required', 'numeric', new UserExistsWithOffset()],
            'email' => 'required|email|exists:users,email',
            'reset_token' => 'required|string|min:12|max:12',
            'password' => 'required|string|min:8|max:12',
        ]);

        // Calculate the actual user ID by subtracting the offset
        // $id = $request->id - 12453289;
        $id = $this->getUserIdFromRegistrationNumber($request->id);;

        // Check if the user exists with the calculated ID
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User ID does not exist.', 404);
        }

        // Ensure the user exists with the provided email and is enabled
        $user = User::whereId($id)->where('email', $request->email)->where('reset_token', $request->reset_token)->where('enabled', 1)->first();
        if (!$user) {
            return $this->errorResponse('Invalid user, Forgot password failed!!', 404);
        }


        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->reset_token = NULL;
        $user->save();

        // Prepare the response data
        $data = [
            'email' => $user->email,
            'user_id' => $request->id,
            'password' => $request->password,
            'vacancy' => Vacancy::whereId($user->vacancy_id)->first(),
            'post' => Post::whereId($user->post_id)->where('vacancy_id', $user->vacancy_id)->first(),
        ];


        Mail::to($user->email)->send(new UserRegistered($data));

        // Return a success response
        return $this->successResponse($data, "New password updated successfully!!");
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $id = $id - 12453289;
        $id = $this->getUserIdFromRegistrationNumber($id);

        //only admin user can see data of any user but a applicant user can see only your own data
        $id = Auth::user()->role === 'admin' ? $id : Auth::user()->id;
        // dd($id);
        // Check if the user exists with the calculated ID
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User ID does not exist. '.$id, 404);
        }

        $user = User::with([
            'userDetails',
            'userEducationDetails',
            'userExperienceDetails',
            'userDocuments',
            'userPaymentDetails',
            'vacancy',
            'post'
        ])->where('role', '!=', 'admin')->whereId($id)->first();

        if(!$user){
            return $this->errorResponse("User data not found!!");
        }
        $user->registration_number = $this->getUserRegistrationNumber($user->id);
        return $this->successResponse($user, "User displayed..");
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUserRequest $request, $id)
    {

        // return $this->successResponse(null, "User profile updated successfully!!");
    }

    public function updateUserPaymentStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'numeric', new UserExistsWithOffset()],
            'status' => 'required|string|in:unverified,verified',
        ]);
        $id = $this->getUserIdFromRegistrationNumber($request['id']);

        $userPayment = UserPayment::where('user_id', $id)->first();

        if (!$userPayment) {
            return $this->errorResponse("User payment data not found!!");
        }
        $isUpdated = $userPayment->update([
            'status' => $request['status'],
        ]);

        if(!$isUpdated){
            return $this->errorResponse("Payment status updation failed!!");
        }

        return $this->successResponse(null, "User's payment status updated successfully..");        
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::whereId($id)->first();
        if(!$user){
            return $this->errorResponse("User data not found!!");
        }
        //delete user_details data first
        // $userDetails = UserDetail::where('user_id', $user->id)->first();
        // $userDetails->delete();
        $user->delete();
        return $this->successResponse(null, "User and User details deleted!!");
    }

    private function getUserRegistrationNumber($id){
        return $id + 12453289;
    }
    private function getUserIdFromRegistrationNumber($registrationNumber){
        return $registrationNumber - 12453289;
    }
}
