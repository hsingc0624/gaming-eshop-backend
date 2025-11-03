<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function roles()
    {
        return Role::orderBy('name')->pluck('name');
    }

    public function index(Request $r)
    {
        $q = User::query()
            ->select('id','name','email','is_active')
            ->with('roles:id,name');

        if ($role = $r->query('role')) {
            $q->role($role);
        }
        if ($status = $r->query('status')) {
            $q->where('is_active', $status === 'active');
        }

        $users = $q->orderBy('name')->paginate(20);

        $users->getCollection()->transform(function ($u) {
            $u->role = $u->roles->pluck('name')->first(); 
            unset($u->roles);
            return $u;
        });

        return $users;
    }

    public function update($id, Request $r)
    {
        $data = $r->validate([
            'role' => 'sometimes|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $user = User::findOrFail($id);

        if (array_key_exists('is_active', $data)) {
            $user->is_active = $data['is_active'];
            $user->save();
        }

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]); 
        }

        $user->load('roles:id,name');
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'role' => $user->roles->pluck('name')->first(),
        ];
    }
    
     public function store(Request $r)
    {
        $data = $r->validate([
            'name'      => 'required|string|min:2|max:80',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
            'password'  => 'sometimes|string|min:8',
        ]);

        $password = $data['password'] ?? Str::password(12);
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_active = array_key_exists('is_active', $data) ? (bool)$data['is_active'] : true;
        $user->password = Hash::make($password);
        $user->save();

        $user->syncRoles([$data['role']]);

        $user->load('roles:id,name');


        return response()->json([
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'is_active' => $user->is_active,
            'role'      => $user->roles->pluck('name')->first(),
        ], 201);
    }
}
