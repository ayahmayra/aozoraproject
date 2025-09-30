<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Invoice;
use App\Models\TimeSchedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get organization info
        $organization = Organization::first();

        // Get parent's children with subjects
        $parent = auth()->user();
        $children = \App\Models\Student::where('parent_id', $parent->id)
                                    ->with(['user', 'subjects'])
                                    ->get();

        // Get parent's profile
        $parentProfile = $parent->parentProfile;

        // Get pending invoices for parent's children (only pending status)
        $childrenIds = $children->pluck('id');
        $pendingInvoices = Invoice::whereIn('student_id', $childrenIds)
            ->where('payment_status', 'pending')
            ->where('billing_period_start', '<=', now()->endOfMonth())
            ->with(['student.user', 'subject'])
            ->get();

        // Get recent invoices (current month and previous months only, not future months)
        $recentInvoices = Invoice::whereIn('student_id', $childrenIds)
            ->whereIn('payment_status', ['pending', 'paid', 'verified'])
            ->where('billing_period_start', '<=', now()->endOfMonth()) // Only current month or earlier
            ->where('billing_period_start', '>=', now()->subMonths(3)->startOfMonth()) // Last 3 months
            ->with(['student.user', 'subject'])
            ->orderBy('billing_period_start', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get today's schedules for all children's enrolled subjects
        $today = Carbon::now()->format('l'); // Get day name (Monday, Tuesday, etc.)
        
        // Map children to their enrolled subjects
        $childSubjectMap = [];
        foreach ($children as $child) {
            $activeSubjects = $child->subjects()->wherePivot('enrollment_status', 'active')->get();
            foreach ($activeSubjects as $subject) {
                if (!isset($childSubjectMap[$subject->id])) {
                    $childSubjectMap[$subject->id] = [];
                }
                $childSubjectMap[$subject->id][] = $child;
            }
        }

        $subjectIds = array_keys($childSubjectMap);

        $todaySchedules = TimeSchedule::whereIn('subject_id', $subjectIds)
            ->where('day_of_week', $today)
            ->with(['subject', 'teacher.user'])
            ->orderBy('start_time')
            ->get();

        // Attach enrolled children to each schedule
        foreach ($todaySchedules as $schedule) {
            $schedule->enrolledChildren = $childSubjectMap[$schedule->subject_id] ?? [];
        }

        // Get next schedule for each child
        $nextSchedules = [];
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $currentDayIndex = array_search($today, $daysOfWeek);

        foreach ($children as $child) {
            $activeSubjects = $child->subjects()->wherePivot('enrollment_status', 'active')->get();
            $childSubjectIds = $activeSubjects->pluck('id')->toArray();
            
            if (empty($childSubjectIds)) {
                continue;
            }

            $nextSchedule = null;

            // First, try to find remaining schedules today
            $upcomingToday = TimeSchedule::whereIn('subject_id', $childSubjectIds)
                ->where('day_of_week', $today)
                ->where('start_time', '>', $currentTime)
                ->with(['subject', 'teacher.user'])
                ->orderBy('start_time')
                ->first();

            if ($upcomingToday) {
                $nextSchedule = $upcomingToday;
                $nextSchedule->scheduleDate = $now->copy();
            } else {
                // If no more schedules today, find the next day with a schedule
                for ($i = 1; $i <= 7; $i++) {
                    $nextDayIndex = ($currentDayIndex + $i) % 7;
                    $nextDay = $daysOfWeek[$nextDayIndex];
                    
                    $nextDaySchedule = TimeSchedule::whereIn('subject_id', $childSubjectIds)
                        ->where('day_of_week', $nextDay)
                        ->with(['subject', 'teacher.user'])
                        ->orderBy('start_time')
                        ->first();
                    
                    if ($nextDaySchedule) {
                        $nextSchedule = $nextDaySchedule;
                        $nextSchedule->scheduleDate = $now->copy()->addDays($i);
                        break;
                    }
                }
            }

            if ($nextSchedule) {
                $nextSchedules[] = [
                    'child' => $child,
                    'schedule' => $nextSchedule
                ];
            }
        }

        return view('parent.dashboard', compact(
            'organization',
            'children',
            'parentProfile',
            'pendingInvoices',
            'recentInvoices',
            'todaySchedules',
            'today',
            'nextSchedules'
        ));
    }
}
