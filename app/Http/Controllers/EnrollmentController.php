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
    public function index()
    {
        $students = Student::with(['user', 'subjects'])->get();
        $subjects = Subject::with('students')->get();
        
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
     * Update enrollment status
     */
    public function update(Request $request, Student $student, Subject $subject)
    {
        $request->validate([
            'status' => 'required|in:enrolled,completed,dropped',
            'notes' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        }

        $student->subjects()->updateExistingPivot($subject->id, $updateData);

        return redirect()->route('enrollment.show', $student)->with('success', 'Enrollment updated successfully!');
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
