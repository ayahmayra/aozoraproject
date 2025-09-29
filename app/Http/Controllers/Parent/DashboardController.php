<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Invoice;

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

        return view('parent.dashboard', compact(
            'organization',
            'children',
            'parentProfile',
            'pendingInvoices'
        ));
    }
}
