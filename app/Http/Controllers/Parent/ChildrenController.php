<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChildrenController extends Controller
{
    public function index()
    {
        $parent = auth()->user();
        $children = Student::where('parent_id', $parent->id)->get();
        
        return view('parent.children.index', compact('children'));
    }

    public function create()
    {
        return view('parent.children.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create user account for student
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Assign student role
        $user->assignRole('student');

        // Create student profile
        $student = Student::create([
            'user_id' => $user->id,
            'parent_id' => auth()->id(),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect()->route('parent.children')->with('success', 'Student added successfully!');
    }

    public function edit(Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        return view('parent.children.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        // Update user account
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $student->user->update($userData);

        // Update student profile
        $student->update([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect()->route('parent.children')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        // Delete user account
        $student->user->delete();
        
        // Delete student profile
        $student->delete();

        return redirect()->route('parent.children')->with('success', 'Student deleted successfully!');
    }
}
