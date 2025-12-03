<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    // List all users (for User Manager table)
    public function index()
    {
        return User::select('id', 'name', 'email', 'is_admin')
            ->orderBy('id')
            ->get();
    }

    // Create a new user
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $data['is_admin'] ?? false,
        ]);

        return response()->json($user, 201);
    }

    // Toggle admin flag for a user
    public function toggleAdmin(User $user)
    {
        $user->is_admin = ! $user->is_admin;
        $user->save();

        return $user;
    }
}
