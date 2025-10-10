<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        // generate token
        $token = $request->user()->createToken('default')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
