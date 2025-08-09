<?php

namespace App\Http\Controllers\Admin\Web\Auth;

use App\Enums\GeneralStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = User::where('email', $request->email)->where('status', GeneralStatusEnum::getStatusActive())->first();

        if (!$admin || !$admin->hasRole('Super Admin')) {
            return back()->withErrors(['email' => 'Invalid credentials provided.']);
        }

        $attempt = [
            'email' => $admin->email,
            'password' => $request->password,
            'status' => $admin->status
        ];

        if (Auth::guard('admin')->attempt($attempt)) {
            // Authentication passed, redirect to the route
            // return view('admin.dashboard');
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided.',
            'password' => 'Invalid credentials provided.',
        ]);
    }

    // Handle the logout request
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
