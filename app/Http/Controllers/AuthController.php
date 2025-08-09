<?php
namespace App\Http\Controllers;

use App\Enums\GeneralStatusEnum;
use App\Http\Requests\Admin\Auth\LoginAdminRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'loginAdmin']]);
    }

    public function loginAdmin(LoginAdminRequest $request)
    {
        $admin = User::where('email', $request->email)->where('status', GeneralStatusEnum::getStatusActive())->first();
        if (!$admin) {
            return response()->json(['success' => false, 'error' => 'Some Error Message'], 401);
        }
        $credentials = [
            'email' => $admin->email,
            'password' => $request->password,
            'status' => $admin->status
        ];
        try {
            if (!$token = auth()->guard('adminApi')->setTTL(config('jwt.admin_ttl', 60))->attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'Some Error Message'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 400);
        }

        return $this->respondWithToken($token, auth()->guard('api')->user());
    }

    protected function respondWithToken($token, $user)
    {
        $user->load('roles','permissions');
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
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


    public function profile()
    {
        try {
            $user = Auth::user();
            if ($user)
                return response()->json(['status' => 'success', 'message' => 'User Profile', 'data' => $user]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something wrong Please Try Again',
            ], 400);
        }
    }


}
