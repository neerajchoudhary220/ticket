<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('web.auth.login');
    }

    public function login(UserLoginRequest $userLoginRequest)
    {
        $credentials = $userLoginRequest->only('email', 'password');
        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records. '])->withInput();
        }
        $userLoginRequest->session()->regenerate();

        return redirect()->intended('home');

    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
