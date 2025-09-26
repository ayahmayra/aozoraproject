<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    /**
     * Display the user's profile
     */
    public function show(Request $request)
    {
        // Check if admin is viewing another user's profile
        if ($request->has('user') && $request->user()->hasRole('admin')) {
            $user = User::findOrFail($request->user);
        } 
        // Check if parent is viewing their child's profile
        elseif ($request->has('user') && $request->user()->hasRole('parent')) {
            $user = User::findOrFail($request->user);
            // Verify that this user is actually a child of the current parent
            $parent = $request->user();
            $isChild = \App\Models\Student::where('user_id', $user->id)
                                       ->where('parent_id', $parent->id)
                                       ->exists();
            if (!$isChild) {
                abort(403, 'Unauthorized access to user profile.');
            }
        } else {
            $user = $request->user();
        }
        
        $user->load(['teacherProfile', 'studentProfile', 'parentProfile']);
        
        // Load subjects and parent for students
        if ($user->hasRole('student') && $user->studentProfile) {
            $user->studentProfile->load(['subjects', 'parent']);
        }
        
        return view('profile.show', compact('user', 'request'));
    }

    /**
     * Show the form for editing the user's profile
     */
    public function edit(Request $request)
    {
        // Check if admin is viewing another user's profile
        if ($request->has('user') && $request->user()->hasRole('admin')) {
            $user = User::findOrFail($request->user);
        } 
        // Check if parent is viewing their child's profile
        elseif ($request->has('user') && $request->user()->hasRole('parent')) {
            $user = User::findOrFail($request->user);
            // Verify that this user is actually a child of the current parent
            $parent = $request->user();
            $isChild = \App\Models\Student::where('user_id', $user->id)
                                       ->where('parent_id', $parent->id)
                                       ->exists();
            if (!$isChild) {
                abort(403, 'Unauthorized access to user profile.');
            }
        } else {
            $user = $request->user();
        }
        
        $user->load(['teacherProfile', 'studentProfile', 'parentProfile']);
        
        // Load subjects and parent for students
        if ($user->hasRole('student') && $user->studentProfile) {
            $user->studentProfile->load(['subjects', 'parent']);
        }
        
        return view('profile.edit', compact('user', 'request'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        // Determine which user to update
        if ($request->has('user') && $request->user()->hasRole('admin')) {
            $user = User::findOrFail($request->user);
        } 
        // Check if parent is updating their child's profile
        elseif ($request->has('user') && $request->user()->hasRole('parent')) {
            $user = User::findOrFail($request->user);
            // Verify that this user is actually a child of the current parent
            $parent = $request->user();
            $isChild = \App\Models\Student::where('user_id', $user->id)
                                       ->where('parent_id', $parent->id)
                                       ->exists();
            if (!$isChild) {
                abort(403, 'Unauthorized access to user profile.');
            }
        } else {
            $user = $request->user();
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update role-specific profile data
        $this->updateRoleSpecificProfile($user, $request);

        // Redirect back to the profile with user parameter if it was provided
        if ($request->has('user')) {
            return redirect()->route('profile.show', ['user' => $user->id])->with('success', 'Profile updated successfully!');
        }
        
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update role-specific profile data
     */
    private function updateRoleSpecificProfile(User $user, Request $request)
    {
        if ($user->hasRole('teacher') && $user->teacherProfile) {
            $user->teacherProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        } elseif ($user->hasRole('student') && $user->studentProfile) {
            $user->studentProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        } elseif ($user->hasRole('parent') && $user->parentProfile) {
            $user->parentProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }
    }
}
