<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index()
    {
        $organization = Organization::first();
        return view('admin.organization.index', compact('organization'));
    }

    public function edit()
    {
        $organization = Organization::first();
        return view('admin.organization.edit', compact('organization'));
    }

    public function update(Request $request)
    {
        $organization = Organization::first();
        
        if (!$organization) {
            return redirect()->route('admin.organization')->with('error', 'Organization not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'mission' => 'nullable|string|max:1000',
            'vision' => 'nullable|string|max:1000',
            'values' => 'nullable|string|max:1000',
            'founded_year' => 'nullable|string|max:4',
            'license_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['logo', 'favicon']);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $data['logo'] = $request->file('logo')->store('organizations', 'public');
        }

        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($organization->favicon) {
                Storage::disk('public')->delete($organization->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('organizations', 'public');
        }

        $organization->update($data);

        return redirect()->route('admin.organization')->with('success', 'Organization updated successfully!');
    }
}
