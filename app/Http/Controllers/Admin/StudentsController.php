<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'parent']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $students = $query->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $parents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->get();

        return view('admin.students.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'nullable|string|max:50|unique:students,student_id',
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
            'parent_id' => $request->parent_id,
            'student_id' => $request->student_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student created successfully!');
    }

    public function edit(Student $student)
    {
        $parents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->get();

        return view('admin.students.edit', compact('student', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'nullable|string|max:50|unique:students,student_id,' . $student->id,
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
            'parent_id' => $request->parent_id,
            'student_id' => $request->student_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        // Delete user account
        $student->user->delete();
        
        // Delete student profile
        $student->delete();

        return redirect()->route('admin.students')->with('success', 'Student deleted successfully!');
    }
}