<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StudentSubject;
use App\Services\InvoiceGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceGenerationService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display the invoice table page.
     */
    public function table()
    {
        return view('admin.invoices.table');
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student.user', 'subject', 'enrollment']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment status
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

        // Filter by billing period (month and year)
        // If no billing period filter is set, default to current month
        if ($request->filled('filter_month') && $request->filled('filter_year')) {
            $month = $request->filter_month;
            $year = $request->filter_year;
        } elseif (!$request->hasAny(['filter_month', 'filter_year']) && !$request->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to'])) {
            // Default to current month if no filters are applied
            $month = now()->month;
            $year = now()->year;
        }
        
        if (isset($month) && isset($year)) {
            // Filter by billing period start date
            $query->whereMonth('billing_period_start', $month)
                  ->whereYear('billing_period_start', $year);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for generating invoices.
     */
    public function generateForm()
    {
        $enrollments = StudentSubject::active()
            ->with(['student', 'subject'])
            ->get();

        return view('admin.invoices.generate', compact('enrollments'));
    }

    /**
     * Generate invoices manually.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_month' => 'required|integer|min:1|max:12',
            'end_month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'payment_method' => 'nullable|in:monthly,semester,yearly',
            'generation_mode' => 'required|in:monthly,semester,yearly',
            'enrollment_ids' => 'nullable|array',
            'enrollment_ids.*' => 'exists:student_subject,id',
        ]);

        $startMonth = (int)$request->start_month;
        $endMonth = (int)$request->end_month;
        $year = (int)$request->year;
        $paymentMethod = $request->payment_method;
        $generationMode = $request->generation_mode;
        $enrollmentIds = $request->enrollment_ids;

        // Calculate actual period dates
        $periodStart = \Carbon\Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
        $periodEnd = \Carbon\Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        $generatedCount = 0;

        if ($enrollmentIds && count($enrollmentIds) > 0) {
            // Generate for specific enrollments
            foreach ($enrollmentIds as $enrollmentId) {
                $enrollment = StudentSubject::with(['student', 'subject'])->find($enrollmentId);
                if ($enrollment && $enrollment->isActive()) {
                    $invoices = $this->invoiceService->generateInvoicesForMonthRange(
                        $enrollment, 
                        $startMonth,
                        $endMonth,
                        $year,
                        $generationMode,
                        $paymentMethod
                    );
                    $generatedCount += count($invoices);
                }
            }
        } else {
            // Generate for all active enrollments
            $invoices = $this->invoiceService->generateInvoicesForMonthRangeAll(
                $startMonth,
                $endMonth,
                $year,
                $generationMode,
                $paymentMethod
            );
            $generatedCount = count($invoices);
        }

        $startMonthName = \DateTime::createFromFormat('!m', $startMonth)->format('F');
        $endMonthName = \DateTime::createFromFormat('!m', $endMonth)->format('F');
        $periodText = $startMonth === $endMonth ? 
            "{$startMonthName} {$year}" : 
            "{$startMonthName} - {$endMonthName} {$year}";
            
        return redirect()->route('admin.invoices')->with('success', "Successfully generated {$generatedCount} invoices for period {$periodText}.");
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'subject', 'enrollment', 'items', 'payments.verifier']);
        
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Mark invoice as paid.
     */
    public function markPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,credit_card,debit_card',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $invoice->update([
            'payment_status' => 'paid',
            'paid_amount' => $request->paid_amount,
            'paid_at' => $request->payment_date,
        ]);

        // Create payment record
        $invoice->payments()->create([
            'payment_date' => $request->payment_date,
            'payment_amount' => $request->paid_amount,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'payment_notes' => $request->notes,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice marked as paid successfully.');
    }

    /**
     * Cancel payment for an invoice.
     */
    public function cancelPayment(Invoice $invoice)
    {
        // Check if invoice is actually paid or verified
        if (!in_array($invoice->payment_status, ['paid', 'verified'])) {
            return redirect()->route('admin.invoices')->with('error', 'Invoice is not marked as paid or verified.');
        }

        // Use database transaction to ensure data consistency
        DB::transaction(function () use ($invoice) {
            // Delete all payment records for this invoice
            $invoice->payments()->delete();
            
            // Reset invoice payment status and amounts
            $invoice->update([
                'payment_status' => 'pending',
                'paid_amount' => 0,
                'paid_at' => null,
            ]);
        });

        return redirect()->route('admin.invoices')->with('success', "Payment for invoice {$invoice->invoice_number} has been cancelled successfully.");
    }

    /**
     * Verify payment for an invoice.
     */
    public function verifyPayment(Invoice $invoice)
    {
        // Check if invoice is actually paid
        if ($invoice->payment_status !== 'paid') {
            return redirect()->route('admin.invoices')->with('error', 'Invoice is not marked as paid.');
        }

        // Update invoice status to verified
        $invoice->update([
            'payment_status' => 'verified',
        ]);

        return redirect()->route('admin.invoices')->with('success', "Payment for invoice {$invoice->invoice_number} has been verified successfully.");
    }

    /**
     * Delete all non-active invoices.
     */
    public function deleteNonActive()
    {
        // Get count of non-active invoices before deletion
        $nonActiveCount = Invoice::where('payment_status', '!=', 'paid')->count();
        
        if ($nonActiveCount === 0) {
            return redirect()->route('admin.invoices')->with('info', 'No non-active invoices found to delete.');
        }
        
        // Delete all invoices that are not paid (active)
        $deletedCount = Invoice::where('payment_status', '!=', 'paid')->delete();
        
        return redirect()->route('admin.invoices')->with('success', "Successfully deleted {$deletedCount} non-active invoices.");
    }

    /**
     * Get invoice statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'pending_invoices' => Invoice::pending()->count(),
            'paid_invoices' => Invoice::paid()->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::paid()->sum('paid_amount'),
            'pending_amount' => Invoice::pending()->sum('total_amount'),
            'overdue_amount' => Invoice::overdue()->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}