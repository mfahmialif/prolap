<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login api
     *
     * 
     */
    public function login(Request $request)
    {
        $expirate = Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration'));
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('access_token')->plainTextToken;

            return [
                'status' => true,
                'user' => $user->load('role', 'dpl', 'peserta', 'pengawas', 'pamong'),
                'token' => $token
            ];
        } else {
            return [
                'status' => false,
                'user' => null,
                'token' => null
            ];
        }
    }

    /**
     * Logout Api
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        return [
            'status' => true,
            'message' => 'Berhasil menghapus token',
            'data' => $user,
            'req' => $request->all(),
        ];
    }
}
