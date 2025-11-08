<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserAdminController extends Controller
{
    /**
     * @param UserService $service
     */
    public function __construct(
        private UserService $service
    ) {}

    /**
     * @return Collection
     */
    public function roles(): Collection
    {
        return $this->service->roles();
    }

    /**
     * @param IndexUserRequest $r
     * @return LengthAwarePaginator
     */
    public function index(IndexUserRequest $r): LengthAwarePaginator
    {
        return $this->service->list(
            $r->validated() + $r->query()
        );
    }

    /**
     * @param int               $id
     * @param UpdateUserRequest $r
     * @return array
     */
    public function update(int $id, UpdateUserRequest $r): array
    {
        return $this->service->update(
            $id,
            $r->validated()
        );
    }

    /**
     * @param StoreUserRequest $r
     * @return JsonResponse
     */
    public function store(StoreUserRequest $r): JsonResponse
    {
        $payload = $this->service->create(
            $r->validated()
        );

        return response()->json(
            $payload,
            201
        );
    }
}
