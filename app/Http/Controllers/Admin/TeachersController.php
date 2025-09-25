<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeachersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('teacher')->with('teacherProfile');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('teacherProfile', function($q) use ($search) {
                      $q->where('employee_number', 'like', "%{$search}%")
                        ->orWhere('education_level', 'like', "%{$search}%")
                        ->orWhere('institution', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('employment_status')) {
            $query->whereHas('teacherProfile', function($q) use ($request) {
                $q->where('employment_status', $request->employment_status);
            });
        }
        
        $teachers = $query->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'education_level' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'hire_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'certifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);
        $user->assignRole('teacher');

        // Create teacher profile if needed
        if ($request->subject_specialization || $request->education_level || $request->institution) {
            $user->teacherProfile()->create([
                'education_level' => $request->education_level,
                'institution' => $request->institution,
                'graduation_year' => $request->graduation_year,
                'hire_date' => $request->hire_date,
                'employment_status' => $request->employment_status,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'certifications' => $request->certifications,
                'notes' => $request->notes,
            ]);
        }

        return redirect()->route('admin.teachers')->with('success', 'Teacher created successfully!');
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'education_level' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'hire_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'certifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'active',
        ];

        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $teacher->update($userData);

        // Update teacher profile
        if ($teacher->teacherProfile) {
            $teacher->teacherProfile->update([
                'education_level' => $request->education_level,
                'institution' => $request->institution,
                'graduation_year' => $request->graduation_year,
                'hire_date' => $request->hire_date,
                'employment_status' => $request->employment_status,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'certifications' => $request->certifications,
                'notes' => $request->notes,
            ]);
        } else {
            $teacher->teacherProfile()->create([
                'education_level' => $request->education_level,
                'institution' => $request->institution,
                'graduation_year' => $request->graduation_year,
                'hire_date' => $request->hire_date,
                'employment_status' => $request->employment_status,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'certifications' => $request->certifications,
                'notes' => $request->notes,
            ]);
        }

        return redirect()->route('admin.teachers')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(User $teacher)
    {
        $teacher->teacherProfile()->delete();
        $teacher->delete();
        return redirect()->route('admin.teachers')->with('success', 'Teacher deleted successfully!');
    }
}