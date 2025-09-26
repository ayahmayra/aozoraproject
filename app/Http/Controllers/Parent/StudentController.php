<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function edit(Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        $parents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->get();

        return view('admin.students.edit', compact('student', 'parents'));
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
            'school_origin' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
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
            'school_origin' => $request->school_origin,
            'medical_notes' => $request->medical_notes,
            'notes' => $request->notes,
        ]);

        return redirect()->route('parent.dashboard')->with('success', 'Student updated successfully!');
    }
}
