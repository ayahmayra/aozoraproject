<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;

class DashboardController extends Controller
{
    public function index()
    {
        // Get pending parents count
        $pendingParentsCount = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->where('status', 'pending')->count();

        // Get organization info
        $organization = Organization::first();

        // Get stats
        $totalAdmins = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->count();

        $totalParents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->count();

        $activeParents = User::whereHas('roles', function($q) {
            $q->where('name', 'parent');
        })->where('status', 'active')->count();

        return view('admin.dashboard', compact(
            'pendingParentsCount',
            'organization',
            'totalAdmins',
            'totalParents',
            'activeParents'
        ));
    }
}
