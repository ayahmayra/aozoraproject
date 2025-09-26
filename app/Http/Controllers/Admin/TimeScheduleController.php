<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSchedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TimeScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TimeSchedule::with(['subject', 'teacher.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('subject', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('teacher.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('room', 'like', "%{$search}%");
        }

        // Filter by day
        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        // Define day order for proper sorting
        $dayOrder = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];

        $schedules = $query->get()->sortBy(function($schedule) use ($dayOrder) {
            return $dayOrder[$schedule->day_of_week] * 10000 + strtotime($schedule->start_time);
        })->values();

        $days = TimeSchedule::getDayOptions();

        return view('admin.time-schedules.index', compact('schedules', 'days'));
    }


    /**
     * Display time schedules in FullCalendar view.
     */
    public function calendarFullCalendar(Request $request)
    {
        $query = TimeSchedule::with(['subject', 'teacher.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('subject', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('teacher.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('room', 'like', "%{$search}%");
        }

        // Filter by day
        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        // Get all schedules
        $allSchedules = $query->get();

        // Define day order for proper sorting
        $dayOrder = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];

        // Sort schedules
        $schedules = $allSchedules->sortBy(function($schedule) use ($dayOrder) {
            return $dayOrder[$schedule->day_of_week] * 10000 + strtotime($schedule->start_time);
        })->values();

        // Get days for filter
        $days = TimeSchedule::getDayOptions();

        // Prepare events data for FullCalendar - Following official documentation
        $events = [];
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'];
        
        foreach ($schedules as $schedule) {
            $colorIndex = abs(crc32($schedule->subject->name)) % count($colors);
            
            // Create recurring events using FullCalendar's recurring pattern
            $events[] = [
                'id' => 'schedule_' . $schedule->id,
                'title' => $schedule->subject->name . ' - ' . ($schedule->teacher ? $schedule->teacher->user->name : 'No Teacher'),
                'daysOfWeek' => [$this->getDayNumber($schedule->day_of_week)],
                'startTime' => $schedule->start_time->format('H:i:s'),
                'endTime' => $schedule->end_time->format('H:i:s'),
                'backgroundColor' => $colors[$colorIndex],
                'borderColor' => $colors[$colorIndex],
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'subject' => $schedule->subject->name,
                    'subjectCode' => $schedule->subject->code,
                    'teacher' => $schedule->teacher ? $schedule->teacher->user->name : 'No Teacher',
                    'room' => $schedule->room,
                    'notes' => $schedule->notes,
                    'dayOfWeek' => $schedule->day_of_week,
                    'originalId' => $schedule->id
                ]
            ];
        }

        // Debug: Log events count and sample data
        \Log::info('FullCalendar Events Count: ' . count($events));
        \Log::info('Schedules Count: ' . $schedules->count());
        if (count($events) > 0) {
            \Log::info('Sample Event: ' . json_encode($events[0]));
        }
        
        return view('admin.time-schedules.calendar-fullcalendar', compact('schedules', 'days', 'events'));
    }


    /**
     * API endpoint for calendar data
     */
    public function apiData()
    {
        $schedules = TimeSchedule::with(['subject', 'teacher.user'])->get();
        
        $events = [];
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'];
        
        foreach ($schedules as $schedule) {
            $colorIndex = abs(crc32($schedule->subject->name)) % count($colors);
            
            // Create recurring events using FullCalendar's recurring pattern
            $events[] = [
                'id' => 'schedule_' . $schedule->id,
                'title' => $schedule->subject->name . ' - ' . ($schedule->teacher ? $schedule->teacher->user->name : 'No Teacher'),
                'daysOfWeek' => [$this->getDayNumber($schedule->day_of_week)],
                'startTime' => $schedule->start_time->format('H:i:s'),
                'endTime' => $schedule->end_time->format('H:i:s'),
                'backgroundColor' => $colors[$colorIndex],
                'borderColor' => $colors[$colorIndex],
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'subject' => $schedule->subject->name,
                    'subjectCode' => $schedule->subject->code,
                    'teacher' => $schedule->teacher ? $schedule->teacher->user->name : 'No Teacher',
                    'room' => $schedule->room,
                    'notes' => $schedule->notes,
                    'dayOfWeek' => $schedule->day_of_week,
                    'originalId' => $schedule->id
                ]
            ];
        }
        
        return response()->json($events);
    }

    /**
     * Convert day name to day number (0 = Sunday, 1 = Monday, etc.)
     */
    private function getDayNumber($dayName)
    {
        $days = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6
        ];
        
        return $days[$dayName] ?? 0;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::with('teachers.user')->orderBy('name')->get();
        $days = TimeSchedule::getDayOptions();

        // Create subject-teacher combinations for display
        $subjectTeacherOptions = [];
        foreach ($subjects as $subject) {
            if ($subject->teachers->count() > 0) {
                foreach ($subject->teachers as $teacher) {
                    $subjectTeacherOptions[] = [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'display_name' => $subject->name . ' (' . $subject->code . ') - ' . $teacher->user->name,
                        'subject' => $subject,
                        'teacher' => $teacher
                    ];
                }
            } else {
                $subjectTeacherOptions[] = [
                    'subject_id' => $subject->id,
                    'teacher_id' => null,
                    'display_name' => $subject->name . ' (' . $subject->code . ') - No teacher assigned',
                    'subject' => $subject,
                    'teacher' => null
                ];
            }
        }

        return view('admin.time-schedules.create', compact('subjectTeacherOptions', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_teacher' => 'required|string',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Parse subject_teacher input
        $subjectTeacherParts = explode('_', $request->subject_teacher);
        $subjectId = $subjectTeacherParts[0];
        $teacherId = $subjectTeacherParts[1] === 'null' ? null : $subjectTeacherParts[1];

        // Validate subject exists
        if (!Subject::where('id', $subjectId)->exists()) {
            return back()->withErrors(['subject_teacher' => 'Selected subject is invalid.']);
        }

        // Check for overlapping schedules for the same subject
        $overlapping = TimeSchedule::where('subject_id', $subjectId)
            ->where('day_of_week', $request->day_of_week)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['start_time' => 'This subject already has a schedule at this time on this day.']);
        }

        // Create the schedule
        TimeSchedule::create([
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.time-schedules.index')
            ->with('success', 'Time schedule created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeSchedule $timeSchedule)
    {
        $timeSchedule->load(['subject', 'teacher.user']);
        
        return view('admin.time-schedules.show', compact('timeSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeSchedule $timeSchedule)
    {
        $subjects = Subject::with('teachers.user')->orderBy('name')->get();
        $days = TimeSchedule::getDayOptions();

        // Create subject-teacher combinations for display
        $subjectTeacherOptions = [];
        foreach ($subjects as $subject) {
            if ($subject->teachers->count() > 0) {
                foreach ($subject->teachers as $teacher) {
                    $subjectTeacherOptions[] = [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'display_name' => $subject->name . ' (' . $subject->code . ') - ' . $teacher->user->name,
                        'subject' => $subject,
                        'teacher' => $teacher
                    ];
                }
            } else {
                $subjectTeacherOptions[] = [
                    'subject_id' => $subject->id,
                    'teacher_id' => null,
                    'display_name' => $subject->name . ' (' . $subject->code . ') - No teacher assigned',
                    'subject' => $subject,
                    'teacher' => null
                ];
            }
        }

        return view('admin.time-schedules.edit', compact('timeSchedule', 'subjectTeacherOptions', 'days'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeSchedule $timeSchedule)
    {
        $request->validate([
            'subject_teacher' => 'required|string',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Parse subject_teacher input
        $subjectTeacherParts = explode('_', $request->subject_teacher);
        $subjectId = $subjectTeacherParts[0];
        $teacherId = $subjectTeacherParts[1] === 'null' ? null : $subjectTeacherParts[1];

        // Validate subject exists
        if (!Subject::where('id', $subjectId)->exists()) {
            return back()->withErrors(['subject_teacher' => 'Selected subject is invalid.']);
        }

        // Check for overlapping schedules (excluding current schedule)
        $overlapping = TimeSchedule::where('subject_id', $subjectId)
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $timeSchedule->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['start_time' => 'This subject already has a schedule at this time on this day.']);
        }

        // Update the schedule
        $timeSchedule->update([
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.time-schedules.index')
            ->with('success', 'Time schedule updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSchedule $timeSchedule)
    {
        $timeSchedule->delete();

        return redirect()->route('admin.time-schedules.index')
            ->with('success', 'Time schedule deleted successfully!');
    }

}