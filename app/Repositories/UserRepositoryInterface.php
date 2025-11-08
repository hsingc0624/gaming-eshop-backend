<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @return Collection<string>
     */
    public function roles(): Collection;

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator;

    /**
     * @param int   $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User;

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User;
}
