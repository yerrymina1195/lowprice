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

    /**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Login User",
 *     tags={"Authentication user"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="User credentials",
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", description="Token expiration time in seconds"),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="message", type="string", example="Login successful")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unauthorized")
 *         )
 *     ),
 *     security={}
 * )
 */

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

    /**
 * @OA\Post(
 *     path="/api/auth/register",
 *     summary="Register a new user",
 *     tags={"Authentication user"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="User details",
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "email", "password", "date_of_birth", "telephone"},
 *             @OA\Property(property="first_name", type="string", example="test"),
 *             @OA\Property(property="last_name", type="string", example="test"),
 *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="telephone", type="string", example="123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="message", type="string", example="User successfully registered. Email verification sent.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */

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

 /**
 * @OA\Get(
 *     path="/api/auth/user-profile",
 *     summary="Get user profile",
 *     tags={"Authentication user"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User profile retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="first_name", type="string", example="test"),
 *             @OA\Property(property="last_name", type="string", example="test"),
 *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
 *             @OA\Property(property="telephone", type="string", example="123456789"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */

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

    /**
 * @OA\Put(
 *     path="/api/auth/updateUserProfile",
 *     summary="Update user profile",
 *     tags={"Authentication user"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="User profile details",
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "email", "telephone", "date_of_birth"},
 *             @OA\Property(property="first_name", type="string", example="test"),
 *             @OA\Property(property="last_name", type="string", example="test"),
 *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
 *             @OA\Property(property="telephone", type="string", example="123456789"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="modification reussite"),
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */

 

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
