<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StudentSubject;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for parent's children.
     */
    public function index(Request $request)
    {
        // Get the authenticated parent
        $parent = auth()->user()->parentProfile;
        
        if (!$parent) {
            abort(403, 'Parent profile not found');
        }

        // Get all children of the parent
        $children = $parent->children()->with('user')->get();
        
        // Get invoices for the parent's children with specific criteria:
        // - Status: pending or paid (not verified)
        // - Date: current month or previous months (not future)
        $query = Invoice::with(['student.user', 'subject'])
            ->whereIn('student_id', $children->pluck('id'))
            ->whereIn('payment_status', ['pending', 'paid'])
            ->where('billing_period_start', '<=', now()->endOfMonth());

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment status (additional filter)
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);

        return view('parent.invoices.index', compact('invoices', 'children'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        // Get the authenticated parent
        $parent = auth()->user()->parentProfile;
        
        if (!$parent) {
            abort(403, 'Parent profile not found');
        }

        // Check if the invoice belongs to parent's child
        $children = $parent->children()->pluck('id');
        if (!$children->contains($invoice->student_id)) {
            abort(403, 'Unauthorized access to this invoice');
        }

        // Only show invoices that are pending or paid (not verified)
        if (!in_array($invoice->payment_status, ['pending', 'paid'])) {
            abort(403, 'This invoice is already verified and cannot be viewed here');
        }

        $invoice->load(['student.user', 'subject']);

        return view('parent.invoices.show', compact('invoice'));
    }

    /**
     * Update payment information for an invoice.
     */
    public function updatePayment(Request $request, Invoice $invoice)
    {
        // Validate authorization
        $parent = auth()->user()->parentProfile;
        
        if (!$parent) {
            abort(403, 'Parent profile not found');
        }

        $children = $parent->children()->pluck('id');
        if (!$children->contains($invoice->student_id)) {
            abort(403, 'Unauthorized access to this invoice');
        }

        // Only allow updating pending invoices
        if ($invoice->payment_status !== 'pending') {
            abort(403, 'Only pending invoices can be updated');
        }

        // Validate request
        $request->validate([
            'payment_reference' => 'required|string|max:255',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Handle file upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $fileName = 'payment_proof_' . $invoice->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $paymentProofPath = $file->storeAs('payment_proofs', $fileName, 'public');
        }

        // Update invoice
        $invoice->update([
            'payment_status' => 'paid',
            'payment_reference' => $request->payment_reference,
            'payment_proof' => $paymentProofPath,
            'payment_date' => $request->payment_date,
            'paid_amount' => $invoice->amount,
            'notes' => $request->notes
        ]);

        return redirect()->route('parent.invoice.show', $invoice)
            ->with('success', 'Payment information has been updated successfully. Your payment is now pending verification from admin.');
    }
}
