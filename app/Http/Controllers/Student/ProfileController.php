<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        // Check if parent or admin is viewing a student's profile
        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
            
            // Admin can view any student profile
            if (auth()->user()->hasRole('admin')) {
                // Admin can view any student profile
            }
            // Parent can only view their own child's profile
            elseif (auth()->user()->hasRole('parent')) {
                if ($student->parent_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to student profile.');
                }
            }
            // Student can only view their own profile
            elseif (auth()->user()->hasRole('student')) {
                if ($student->user_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to student profile.');
                }
            }
            else {
                abort(403, 'Unauthorized access for your role.');
            }
        } else {
            // Student viewing their own profile
            $student = auth()->user()->student;
            
            if (!$student) {
                abort(404, 'Student profile not found.');
            }
        }

        return view('student.profile.index', compact('student'));
    }

    public function edit(Request $request)
    {
        // Only parent and admin can edit student profiles
        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
            
            // Admin can edit any student profile
            if (auth()->user()->hasRole('admin')) {
                // Admin can edit any student profile
            }
            // Parent can only edit their own child's profile
            elseif (auth()->user()->hasRole('parent')) {
                if ($student->parent_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to student profile.');
                }
            }
            else {
                abort(403, 'Unauthorized access for your role.');
            }
        } else {
            // Students cannot edit their own profile
            abort(403, 'Students cannot edit their own profile. Please contact your parent.');
        }

        return view('student.profile.edit', compact('student'));
    }

    public function update(Request $request)
    {
        // Only parent and admin can update student profiles
        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
            
            // Admin can update any student profile
            if (auth()->user()->hasRole('admin')) {
                // Admin can update any student profile
            }
            // Parent can only update their own child's profile
            elseif (auth()->user()->hasRole('parent')) {
                if ($student->parent_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to student profile.');
                }
            }
            else {
                abort(403, 'Unauthorized access for your role.');
            }
        } else {
            // Students cannot update their own profile
            abort(403, 'Students cannot update their own profile. Please contact your parent.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $userData['password'] = \Hash::make($request->password);
        }

        $student->user->update($userData);

        $student->update([
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Redirect based on user role
        if (auth()->user()->hasRole('parent')) {
            return redirect()->route('parent.children')->with('success', 'Student profile updated successfully!');
        } elseif (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.students')->with('success', 'Student profile updated successfully!');
        } else {
            return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
        }
    }
}