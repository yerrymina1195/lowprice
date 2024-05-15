<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\UserLoginHistory;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyAccount']]);
    }

    public function login(Request $request)
    {
        $validator = $this->validateLogin($request->all());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->recordLoginHistory($request);

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = User::validateRegistration($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $random = Str::random(40);
        $url = URL::to('/verify-email/') . '/' . $random;


        try {
            $dateOfBirth = Carbon::createFromFormat('d/m/Y', $request->input('date_of_birth'))->format('Y-m-d');
            $user =  User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)],
                ['tokenemail' => $random],
                ['date_of_birth' => $dateOfBirth]
            ));
            $user->notify(new UserNotification($url));
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage(), 'message' => 'User registration failed'], 500);
        }

        return response()->json([
            'message' => 'User successfully registered. Email verification sent.',
            'data' => $user
        ], 201);
    }

    public function verifyAccount($token)
    {
        try {
            $user = User::where('tokenemail', $token)->firstOrFail();

            if ($user->isverified) {
                return view('alreadyVerified');
            }

            $user->isverified = true;
            $user->email_verified_at = Carbon::now();
            $user->save();

            return view('mailsuccess');
        } catch (ModelNotFoundException $e) {
            return view('404')->with('message', 'Sorry, your email cannot be identified.');
        }
    }

    public function logout()
    {

        $userLoginHistory = UserLoginHistory::where('user_id', auth()->id())
        ->latest()
        ->first();

    if ($userLoginHistory) {
        $userLoginHistory->update(['logout_at' => now()]);
    }

        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
     try {
        $user = auth()->user();
        $userData = $user->makeHidden(['role']);
    
        return response()->json($userData);
     } catch (Exception $e) {
        return response()->json($e);
     }

    }

    public function updateUserProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'telephone' => ['required','string',Rule::unique('users')->ignore($user->id)],
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            $data = $validator->validated();
            $data['date_of_birth'] = Carbon::createFromFormat('d-m-Y', $request->input('date_of_birth'))->format('Y-m-d');

            $user->update($data);

            return response()->json(["message"=>'modification reussite',"data"=>$user->makeHidden(['role'])],200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    protected function validateLogin($data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }



    protected function recordLoginHistory(Request $request)
    {
        $user = Auth::user();

        UserLoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at'=>  now(),
        ]);
    }
}
