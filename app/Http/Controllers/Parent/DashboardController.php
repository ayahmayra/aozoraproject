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

        // Get parent's children with subjects
        $parent = auth()->user();
        $children = \App\Models\Student::where('parent_id', $parent->id)
                                    ->with(['user', 'subjects'])
                                    ->get();

        // Get parent's profile
        $parentProfile = $parent->parentProfile;

        return view('parent.dashboard', compact(
            'organization',
            'children',
            'parentProfile'
        ));
    }
}
