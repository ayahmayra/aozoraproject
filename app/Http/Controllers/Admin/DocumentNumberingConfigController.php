<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentNumberingConfig;
use Illuminate\Http\Request;

class DocumentNumberingConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DocumentNumberingConfig::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('entity_type', 'like', "%{$search}%")
                  ->orWhere('prefix', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $configs = $query->orderBy('entity_type')->paginate(15);
        
        return view('admin.document-numbering.index', compact('configs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entityTypes = [
            'user' => 'User',
            'teacher' => 'Teacher',
            'parent' => 'Parent',
            'student' => 'Student',
            'enrollment' => 'Enrollment',
            'subject' => 'Subject',
            'organization' => 'Organization',
            'invoice' => 'Invoice',
        ];

        return view('admin.document-numbering.create', compact('entityTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string|max:50|unique:document_numbering_configs,entity_type',
            'prefix' => 'nullable|string|max:20',
            'suffix' => 'nullable|string|max:20',
            'current_number' => 'required|integer|min:1',
            'number_length' => 'required|integer|min:1|max:10',
            'separator' => 'nullable|string|max:5',
            'include_year' => 'boolean',
            'include_month' => 'boolean',
            'include_day' => 'boolean',
            'year_format' => 'required|string|max:10',
            'month_format' => 'required|string|max:10',
            'day_format' => 'required|string|max:10',
            'reset_yearly' => 'boolean',
            'reset_monthly' => 'boolean',
            'reset_daily' => 'boolean',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = $this->cleanData($request->all());
        DocumentNumberingConfig::create($data);

        return redirect()->route('admin.document-numbering.index')
            ->with('success', 'Document numbering configuration created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentNumberingConfig $documentNumberingConfig)
    {
        return view('admin.document-numbering.show', compact('documentNumberingConfig'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentNumberingConfig $documentNumberingConfig)
    {
        $entityTypes = [
            'user' => 'User',
            'teacher' => 'Teacher',
            'parent' => 'Parent',
            'student' => 'Student',
            'enrollment' => 'Enrollment',
            'subject' => 'Subject',
            'organization' => 'Organization',
            'invoice' => 'Invoice',
        ];

        return view('admin.document-numbering.edit', compact('documentNumberingConfig', 'entityTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentNumberingConfig $documentNumberingConfig)
    {
        $request->validate([
            'entity_type' => 'required|string|max:50|unique:document_numbering_configs,entity_type,' . $documentNumberingConfig->id,
            'prefix' => 'nullable|string|max:20',
            'suffix' => 'nullable|string|max:20',
            'current_number' => 'required|integer|min:1',
            'number_length' => 'required|integer|min:1|max:10',
            'separator' => 'nullable|string|max:5',
            'include_year' => 'boolean',
            'include_month' => 'boolean',
            'include_day' => 'boolean',
            'year_format' => 'required|string|max:10',
            'month_format' => 'required|string|max:10',
            'day_format' => 'required|string|max:10',
            'reset_yearly' => 'boolean',
            'reset_monthly' => 'boolean',
            'reset_daily' => 'boolean',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = $this->cleanData($request->all());
        $documentNumberingConfig->update($data);

        return redirect()->route('admin.document-numbering.index')
            ->with('success', 'Document numbering configuration updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentNumberingConfig $documentNumberingConfig)
    {
        $documentNumberingConfig->delete();

        return redirect()->route('admin.document-numbering.index')
            ->with('success', 'Document numbering configuration deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(DocumentNumberingConfig $documentNumberingConfig)
    {
        $documentNumberingConfig->update(['is_active' => !$documentNumberingConfig->is_active]);

        $status = $documentNumberingConfig->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.document-numbering.index')
            ->with('success', "Document numbering configuration {$status} successfully!");
    }

    /**
     * Reset current number
     */
    public function resetNumber(DocumentNumberingConfig $documentNumberingConfig)
    {
        $documentNumberingConfig->resetNumber();

        return redirect()->route('admin.document-numbering.index')
            ->with('success', 'Current number reset successfully!');
    }

    /**
     * Clean data by converting empty strings to null
     */
    private function cleanData(array $data): array
    {
        $nullableFields = ['prefix', 'suffix', 'separator', 'description'];
        
        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        
        return $data;
    }
}