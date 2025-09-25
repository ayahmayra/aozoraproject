<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->whereHas('roles', function($q) {
            $q->where('name', 'parent');
        });

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $parents = $query->paginate(10);
        $roles = Role::all();

        return view('admin.parents.index', compact('parents', 'roles'));
    }

    public function create()
    {
        return view('admin.parents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('parent');

        return redirect()->route('admin.parents')->with('success', 'Parent user created successfully!');
    }

    public function edit(User $parent)
    {
        $parentData = $parent->parentProfile;
        return view('admin.parents.edit', compact('parent', 'parentData'));
    }

    public function update(Request $request, User $parent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $parent->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,pending,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:100',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $parent->update($data);

        // Update or create parent data
        if ($parent->parentProfile) {
            $parent->parentProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'emergency_contact_phone' => $request->emergency_contact,
                'emergency_contact_name' => $request->emergency_contact_name,
            ]);
        } else {
            $parent->parentProfile()->create([
                'phone' => $request->phone,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'emergency_contact_phone' => $request->emergency_contact,
                'emergency_contact_name' => $request->emergency_contact_name,
            ]);
        }

        return redirect()->route('admin.parents')->with('success', 'Parent user updated successfully!');
    }

    public function destroy(User $parent)
    {
        $parent->delete();
        return redirect()->route('admin.parents')->with('success', 'Parent user deleted successfully!');
    }

    public function verify(User $parent)
    {
        $parent->update(['status' => 'active']);
        return redirect()->route('admin.parents')->with('success', 'Parent user verified successfully!');
    }

    public function deactivate(User $parent)
    {
        $parent->update(['status' => 'inactive']);
        return redirect()->route('admin.parents')->with('success', 'Parent user deactivated successfully!');
    }
}
