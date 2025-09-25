<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->whereHas('roles', function($q) {
            $q->where('name', 'admin');
        });

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        $user->assignRole('admin');

        return redirect()->route('admin.users')->with('success', 'Admin user created successfully!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the last admin
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    public function verify(User $user)
    {
        $user->update(['status' => 'active']);
        return redirect()->route('admin.users')->with('success', 'User verified successfully!');
    }

    public function deactivate(User $user)
    {
        $user->update(['status' => 'inactive']);
        return redirect()->route('admin.users')->with('success', 'User deactivated successfully!');
    }
}
