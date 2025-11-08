<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * @param AuthService $service
     */
    public function __construct(private AuthService $service) {}

    /**
     * @param RegisterRequest $r
     * @return JsonResponse
     */
    public function register(RegisterRequest $r): JsonResponse
    {
        $user = $this->service->register($r->validated());

        return response()->json(['user' => $user], 201);
    }

    /**
     * @param LoginRequest $r
     * @return JsonResponse
     */
    public function login(LoginRequest $r): JsonResponse
    {
        $user = $this->service->login($r->validated());

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['user' => $user]);
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        $this->service->logout();

        return response()->noContent();
    }
}
