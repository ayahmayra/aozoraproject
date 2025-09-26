<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display enrollment management page
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'subjects', 'parent'])
            ->whereHas('subjects');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by enrollment status
        if ($request->filled('status')) {
            $query->whereHas('subjects', function($q) use ($request) {
                $q->where('enrollment_status', $request->status);
            });
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        $students = $query->get();
        $subjects = Subject::all();
        
        return view('enrollment.index', compact('students', 'subjects'));
    }

    /**
     * Show enrollment form for a specific student
     */
    public function create(Student $student)
    {
        $availableSubjects = Subject::whereNotIn('id', $student->subjects()->pluck('subjects.id'))->get();
        
        return view('enrollment.create', compact('student', 'availableSubjects'));
    }

    /**
     * Enroll student in subjects
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $enrollmentData = [];
        foreach ($request->subjects as $subjectId) {
            $enrollmentData[$subjectId] = [
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'notes' => $request->notes,
            ];
        }

        $student->subjects()->attach($enrollmentData);

        return redirect()->route('enrollment.index')->with('success', 'Student enrolled successfully!');
    }

    /**
     * Show enrollment details for a student
     */
    public function show(Student $student)
    {
        $student->load(['user', 'subjects']);
        
        return view('enrollment.show', compact('student'));
    }

    /**
     * Show the form for editing enrollment
     */
    public function edit(Student $student, Subject $subject)
    {
        // Load student with user and subjects relationship
        $student->load(['user', 'subjects']);
        
        // Get the specific subject with pivot data
        $enrollment = $student->subjects()->where('subject_id', $subject->id)->first();
        
        if (!$enrollment) {
            return redirect()->route('enrollment.index')
                ->with('error', 'Enrollment not found.');
        }
        
        return view('enrollment.edit', compact('student', 'subject', 'enrollment'));
    }

    /**
     * Update enrollment status
     */
    public function update(Request $request, Student $student, Subject $subject)
    {
        $request->validate([
            'status' => 'required|in:pending,active,cancelled',
            'payment_method' => 'nullable|in:monthly,semester,yearly',
            'payment_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'enrollment_status' => $request->status,
            'payment_method' => $request->payment_method,
            'payment_amount' => $request->payment_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ];

        // Remove null values
        $updateData = array_filter($updateData, function($value) {
            return $value !== null && $value !== '';
        });

        $student->subjects()->updateExistingPivot($subject->id, $updateData);

        return redirect()->route('enrollment.index')->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove student from subject
     */
    public function destroy(Student $student, Subject $subject)
    {
        $student->subjects()->detach($subject->id);

        return redirect()->route('enrollment.show', $student)->with('success', 'Student removed from subject successfully!');
    }
}
