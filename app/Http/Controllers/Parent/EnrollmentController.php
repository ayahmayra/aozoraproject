<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Display enrollment form for a specific student
     */
    public function create(Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        // Get available subjects (not already enrolled)
        $enrolledSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();
        $availableSubjects = Subject::whereNotIn('id', $enrolledSubjectIds)->get();

        return view('parent.enrollment.create', compact('student', 'availableSubjects'));
    }

    /**
     * Store enrollment data
     */
    public function store(Request $request, Student $student)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'payment_method' => 'required|in:monthly,semester,yearly',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if student is already enrolled in this subject
        if ($student->subjects()->where('subject_id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Student is already enrolled in this subject.']);
        }

        // Generate enrollment number
        $enrollmentNumber = \App\Services\DocumentNumberingService::generateNextNumber('enrollment');

        // Create enrollment
        $student->subjects()->attach($request->subject_id, [
            'enrollment_date' => now(),
            'payment_method' => $request->payment_method,
            'enrollment_status' => 'pending',
            'parent_id' => auth()->id(),
            'enrollment_number' => $enrollmentNumber,
            'notes' => $request->notes,
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Student enrolled successfully! Payment is pending approval.');
    }

    /**
     * Display enrollment details
     */
    public function show(Student $student, Subject $subject)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        // Get enrollment details
        $enrollment = $student->subjects()
            ->where('subject_id', $subject->id)
            ->first();

        if (!$enrollment) {
            abort(404, 'Enrollment not found.');
        }

        return view('parent.enrollment.show', compact('student', 'subject', 'enrollment'));
    }

    /**
     * Update enrollment status (for parent to cancel)
     */
    public function update(Request $request, Student $student, Subject $subject)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        $request->validate([
            'enrollment_status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update enrollment
        $student->subjects()->updateExistingPivot($subject->id, [
            'enrollment_status' => $request->enrollment_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Delete enrollment (only for pending status)
     */
    public function destroy(Student $student, Subject $subject)
    {
        // Check if student belongs to current parent
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized access to student data.');
        }

        // Get enrollment details
        $enrollment = $student->subjects()
            ->where('subject_id', $subject->id)
            ->first();

        if (!$enrollment) {
            abort(404, 'Enrollment not found.');
        }

        // Only allow deletion if enrollment is pending
        if ($enrollment->pivot->enrollment_status !== 'pending') {
            return redirect()->route('parent.dashboard')
                ->withErrors(['enrollment' => 'Only pending enrollments can be deleted.']);
        }

        // Delete enrollment from database
        $student->subjects()->detach($subject->id);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Enrollment deleted successfully!');
    }
}