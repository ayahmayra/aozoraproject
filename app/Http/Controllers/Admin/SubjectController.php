<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }


        $subjects = $query->with(['teachers.user', 'students.user'])->orderBy('name')->paginate(10);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function show(Subject $subject)
    {
        $subject->load([
            'teachers.user',
            'students.user',
            'students' => function($query) {
                $query->wherePivot('enrollment_status', 'active');
            }
        ]);

        return view('admin.subjects.show', compact('subject'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string|max:1000',
        ]);

        Subject::create($request->all());

        return redirect()->route('admin.subjects')->with('success', 'Subject created successfully!');
    }

    public function edit(Subject $subject)
    {
        $teachers = Teacher::with('user')->get();
        $assignedTeachers = $subject->teachers()->pluck('teachers.id')->toArray();
        
        return view('admin.subjects.edit', compact('subject', 'teachers', 'assignedTeachers'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $subject->update($request->all());

        // Sync teachers
        if ($request->has('teachers') && !empty($request->teachers)) {
            $teacherIds = is_array($request->teachers) ? $request->teachers : explode(',', $request->teachers);
            $teacherIds = array_filter($teacherIds, function($id) {
                return !empty($id);
            });
            $subject->teachers()->sync($teacherIds);
        } else {
            $subject->teachers()->detach();
        }

        return redirect()->route('admin.subjects')->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects')->with('success', 'Subject deleted successfully!');
    }
}
