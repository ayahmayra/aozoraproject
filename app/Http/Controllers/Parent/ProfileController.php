<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $parentProfile = $user->parentProfile;
        $organization = Organization::first();

        return view('parent.profile', compact('user', 'parentProfile', 'organization'));
    }
}
