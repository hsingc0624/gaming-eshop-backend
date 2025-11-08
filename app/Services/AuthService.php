<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Customer');
        }

        Auth::login($user);
        session()->regenerate();

        return $user;
    }

    /**
     * @param array $credentials
     * @return User|null
     */
    public function login(array $credentials): ?User
    {
        if (!Auth::attempt($credentials, true)) {
            return null;
        }

        session()->regenerate();

        return Auth::user();
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
