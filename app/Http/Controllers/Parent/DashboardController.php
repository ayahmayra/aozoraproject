<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;

class DashboardController extends Controller
{
    public function index()
    {
        // Get organization info
        $organization = Organization::first();

        // Get parent's children count (if students are related to parents)
        $childrenCount = 0; // This will be implemented when student-parent relationship is created

        // Get parent's profile
        $parentProfile = auth()->user()->parentProfile;

        return view('parent.dashboard', compact(
            'organization',
            'childrenCount',
            'parentProfile'
        ));
    }
}
