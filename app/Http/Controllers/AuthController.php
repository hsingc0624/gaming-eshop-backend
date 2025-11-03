<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:255','unique:users'],
            'password' => ['required', Password::min(6)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Customer');
        }

        Auth::login($user);
        $r->session()->regenerate();

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $r)
    {
        $credentials = $r->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, true)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $r->session()->regenerate();
        return response()->json(['user' => Auth::user()]);
    }

    public function logout(Request $r)
    {
        Auth::guard('web')->logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return response()->noContent();
    }
}
