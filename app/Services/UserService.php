<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    /**
     * @param UserRepositoryInterface $users
     */
    public function __construct(
        private UserRepositoryInterface $users
    ) {}

    /**
     * @return Collection<string>
     */
    public function roles(): Collection
    {
        return $this->users->roles();
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters): LengthAwarePaginator
    {
        return $this->users->paginate($filters);
    }

    /**
     * @param int   $id
     * @param array $data
     * @return array{
     *   id:int,
     *   name:string,
     *   email:string,
     *   is_active:bool,
     *   role:?string
     * }
     */
    public function update(int $id, array $data): array
    {
        $u = $this->users->update($id, $data);

        return [
            'id'        => $u->id,
            'name'      => $u->name,
            'email'     => $u->email,
            'is_active' => $u->is_active,
            'role'      => $u->roles->pluck('name')->first(),
        ];
    }

    /**
     * @param array $data
     * @return array{
     *   id:int,
     *   name:string,
     *   email:string,
     *   is_active:bool,
     *   role:?string
     * }
     */
    public function create(array $data): array
    {
        $u = $this->users->create($data);

        return [
            'id'        => $u->id,
            'name'      => $u->name,
            'email'     => $u->email,
            'is_active' => $u->is_active,
            'role'      => $u->roles->pluck('name')->first(),
        ];
    }
}
