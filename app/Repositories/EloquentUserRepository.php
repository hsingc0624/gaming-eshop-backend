<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function roles(): Collection
    {
        return Role::orderBy('name')->pluck('name');
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $q = User::query()
            ->select('id','name','email','is_active')
            ->with('roles:id,name');

        if (!empty($filters['role'])) {
            $q->role($filters['role']);
        }
        if (!empty($filters['status'])) {
            $q->where('is_active', $filters['status'] === 'active');
        }

        $perPage = (int)($filters['per_page'] ?? 20);
        $users = $q->orderBy('name')->paginate($perPage);

        $users->getCollection()->transform(function ($u) {
            $u->role = $u->roles->pluck('name')->first();
            unset($u->roles);
            return $u;
        });

        return $users;
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (array_key_exists('is_active', $data)) {
            $user->is_active = (bool)$data['is_active'];
            $user->save();
        }

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->load('roles:id,name');
    }

    public function create(array $data): User
    {
        $password = $data['password'] ?? Str::password(12);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_active = array_key_exists('is_active', $data) ? (bool)$data['is_active'] : true;
        $user->password = Hash::make($password);
        $user->save();

        $user->syncRoles([$data['role']]);

        return $user->load('roles:id,name');
    }
}
