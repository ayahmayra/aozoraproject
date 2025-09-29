<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StudentSubject;
use App\Services\InvoiceGenerationService;
use App\Exports\InvoiceTableExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
    public function table(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $subjectFilter = $request->get('subject');
        
        // Get all active enrollments (students with subjects)
        $enrollmentsQuery = StudentSubject::with(['student.user', 'subject'])
            ->where('enrollment_status', 'active');
            
        // Apply subject filter if provided
        if ($subjectFilter) {
            $enrollmentsQuery->where('subject_id', $subjectFilter);
        }
        
        $enrollments = $enrollmentsQuery->get();
        
        // Get all invoices for the selected year
        $invoicesQuery = Invoice::with(['student.user', 'subject'])
            ->whereYear('billing_period_start', $year);
            
        // Apply subject filter if provided
        if ($subjectFilter) {
            $invoicesQuery->whereHas('subject', function ($q) use ($subjectFilter) {
                $q->where('id', $subjectFilter);
            });
        }
        
        $invoices = $invoicesQuery->get();
        
        // Create a comprehensive list of student-subject combinations
        $studentSubjectCombinations = $enrollments->map(function ($enrollment) {
            return [
                'student_name' => $enrollment->student->user->name,
                'subject_name' => $enrollment->subject->name,
                'student_id' => $enrollment->student->id,
                'subject_id' => $enrollment->subject->id,
                'enrollment' => $enrollment
            ];
        });
        
        // Group invoices by student and subject
        $invoicesByStudentSubject = $invoices->groupBy(function ($invoice) {
            return $invoice->student->user->name . '|' . $invoice->subject->name;
        });
        
        // Create grouped data that includes all student-subject combinations
        $groupedInvoices = collect();
        
        foreach ($studentSubjectCombinations as $combination) {
            $key = $combination['student_name'] . '|' . $combination['subject_name'];
            $invoices = $invoicesByStudentSubject->get($key, collect());
            
            $groupedInvoices->put($key, $invoices);
        }
        
        // Sort by subject name
        $groupedInvoices = $groupedInvoices->sortBy(function ($group, $key) {
            return explode('|', $key)[1];
        });
        
        // Get all months
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Calculate statistics for current month (static, not affected by filter)
        $currentMonth = date('n'); // Current month (1-12)
        $currentYear = date('Y');
        
        // Get all invoices for the selected year (not filtered by month)
        $allInvoicesForYear = Invoice::whereYear('billing_period_start', $year);
        if ($subjectFilter) {
            $allInvoicesForYear->whereHas('subject', function ($q) use ($subjectFilter) {
                $q->where('id', $subjectFilter);
            });
        }
        $allInvoicesForYear = $allInvoicesForYear->get();
        
        // Total verified payments for current month (static)
        $currentMonthTotal = Invoice::where('payment_status', 'verified')
            ->whereYear('billing_period_start', $currentYear)
            ->whereMonth('billing_period_start', $currentMonth)
            ->sum('paid_amount');
        
        // Total verified payments for the filtered year
        $currentYearTotal = $allInvoicesForYear
            ->where('payment_status', 'verified')
            ->sum('paid_amount');
        
        // Overall total verified payments (all time)
        $overallTotal = Invoice::where('payment_status', 'verified')
            ->sum('paid_amount');
        
        // Statistics for current month (static, not affected by filter)
        $currentMonthInvoices = Invoice::whereYear('billing_period_start', $currentYear)
            ->whereMonth('billing_period_start', $currentMonth)
            ->get();
            
        $totalInvoices = $currentMonthInvoices->count();
        $pendingInvoices = $currentMonthInvoices->where('payment_status', 'pending')->count();
        $paidInvoices = $currentMonthInvoices->where('payment_status', 'paid')->count();
        $verifiedInvoices = $currentMonthInvoices->where('payment_status', 'verified')->count();
        
        // Calculate monthly totals for verified payments (based on all invoices for the year)
        $monthlyTotals = [];
        foreach ($months as $monthNum => $monthName) {
            $monthlyTotals[$monthNum] = $allInvoicesForYear
                ->where('payment_status', 'verified')
                ->filter(function ($invoice) use ($monthNum) {
                    return $invoice->billing_period_start->month == $monthNum;
                })
                ->sum('paid_amount');
        }
        
        // Get all subjects for filter dropdown
        $subjects = \App\Models\Subject::orderBy('name')->get();
        
        return view('admin.invoices.table', compact(
            'groupedInvoices', 'months', 'year', 'subjectFilter',
            'currentMonthTotal', 'currentYearTotal', 'overallTotal',
            'totalInvoices', 'verifiedInvoices', 'pendingInvoices', 'paidInvoices',
            'currentMonth', 'currentYear', 'monthlyTotals', 'subjects'
        ));
    }

    /**
     * Export invoice table to Excel.
     */
    public function exportTable(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $subjectFilter = $request->get('subject');
        
        // Get all invoices for the selected year
        $query = Invoice::with(['student.user', 'subject'])
            ->whereYear('billing_period_start', $year);
            
        // Apply subject filter if provided
        if ($subjectFilter) {
            $query->whereHas('subject', function ($q) use ($subjectFilter) {
                $q->where('id', $subjectFilter);
            });
        }
        
        $invoices = $query->get();
        
        // Group invoices by student and subject, then sort by subject name
        $groupedInvoices = $invoices->groupBy(function ($invoice) {
            return $invoice->student->user->name . '|' . $invoice->subject->name;
        })->sortBy(function ($group, $key) {
            // Sort by subject name (second part after |)
            return explode('|', $key)[1];
        });
        
        // Get all months
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Calculate monthly totals for verified payments
        $monthlyTotals = [];
        foreach ($months as $monthNum => $monthName) {
            $monthlyTotals[$monthNum] = $invoices
                ->where('payment_status', 'verified')
                ->filter(function ($invoice) use ($monthNum) {
                    return $invoice->billing_period_start->month == $monthNum;
                })
                ->sum('paid_amount');
        }
        
        $subjectName = $subjectFilter ? '_' . \App\Models\Subject::find($subjectFilter)->name : '';
        $fileName = "Invoice_Table_{$year}{$subjectName}_" . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(
            new InvoiceTableExport($year, $groupedInvoices, $months, $monthlyTotals),
            $fileName
        );
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
            // Filter by billing period - invoice must start in the specified month/year
            $query->where(function($q) use ($month, $year) {
                $q->where(function($subQ) use ($month, $year) {
                    // For monthly invoices: billing period starts in the specified month/year
                    $subQ->where(function($monthlyQ) use ($month, $year) {
                        $monthlyQ->where('payment_method', 'monthly')
                                ->whereMonth('billing_period_start', $month)
                                ->whereYear('billing_period_start', $year);
                    })
                    // For semester invoices: billing period starts in the specified month/year
                    ->orWhere(function($semesterQ) use ($month, $year) {
                        $semesterQ->where('payment_method', 'semester')
                                 ->whereMonth('billing_period_start', $month)
                                 ->whereYear('billing_period_start', $year);
                    })
                    // For yearly invoices: billing period starts in the specified month/year
                    ->orWhere(function($yearlyQ) use ($month, $year) {
                        $yearlyQ->where('payment_method', 'yearly')
                               ->whereMonth('billing_period_start', $month)
                               ->whereYear('billing_period_start', $year);
                    });
                });
            });
        } elseif ($request->filled('filter_year') && !$request->filled('filter_month')) {
            // Only year filter - show invoices that start in the specified year
            $year = $request->filter_year;
            $query->whereYear('billing_period_start', $year);
        } elseif ($request->filled('filter_month') && !$request->filled('filter_year')) {
            // Only month filter (use current year) - show invoices that start in the specified month
            $month = $request->filter_month;
            $year = now()->year;
            $query->where(function($q) use ($month, $year) {
                $q->whereMonth('billing_period_start', $month)
                  ->whereYear('billing_period_start', $year);
            });
        } elseif (!$request->hasAny(['filter_month', 'filter_year']) && !$request->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to'])) {
            // Default to current month if no filters are applied - show invoices that start in current month
            $month = now()->month;
            $year = now()->year;
            $query->where(function($q) use ($month, $year) {
                $q->whereMonth('billing_period_start', $month)
                  ->whereYear('billing_period_start', $year);
            });
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
            'start_year' => 'required|integer|min:2020|max:2030',
            'end_year' => 'required|integer|min:2020|max:2030',
            'payment_method' => 'nullable|in:monthly,semester,yearly',
            'enrollment_ids' => 'nullable|array',
            'enrollment_ids.*' => 'exists:student_subject,id',
        ]);

        $startMonth = (int)$request->start_month;
        $endMonth = (int)$request->end_month;
        $startYear = (int)$request->start_year;
        $endYear = (int)$request->end_year;
        $paymentMethod = $request->payment_method;
        $enrollmentIds = $request->enrollment_ids;

        // Calculate actual period dates
        $periodStart = \Carbon\Carbon::createFromDate($startYear, $startMonth, 1)->startOfMonth();
        $periodEnd = \Carbon\Carbon::createFromDate($endYear, $endMonth, 1)->endOfMonth();

        $generatedCount = 0;

        // Check if it's cross-year
        $isCrossYear = $startYear !== $endYear;
        
        if ($enrollmentIds && count($enrollmentIds) > 0) {
            // Generate for specific enrollments
            foreach ($enrollmentIds as $enrollmentId) {
                $enrollment = StudentSubject::with(['student', 'subject'])->find($enrollmentId);
                if ($enrollment && $enrollment->isActive()) {
                    // Use enrollment's payment method for automatic generation
                    $enrollmentPaymentMethod = $enrollment->payment_method;
                    
                    if ($isCrossYear) {
                        $invoices = $this->invoiceService->generateInvoicesForCrossYearRange(
                            $enrollment, 
                            $startMonth,
                            $endMonth,
                            $startYear,
                            $endYear,
                            $enrollmentPaymentMethod,
                            $paymentMethod
                        );
                    } else {
                        $invoices = $this->invoiceService->generateInvoicesForMonthRange(
                            $enrollment, 
                            $startMonth,
                            $endMonth,
                            $startYear,
                            $enrollmentPaymentMethod,
                            $paymentMethod
                        );
                    }
                    $generatedCount += count($invoices);
                }
            }
        } else {
            // Generate for all active enrollments
            if ($isCrossYear) {
                $invoices = $this->invoiceService->generateInvoicesForCrossYearRangeAll(
                    $startMonth,
                    $endMonth,
                    $startYear,
                    $endYear,
                    null, // No specific generation mode - will use each enrollment's payment method
                    $paymentMethod
                );
            } else {
                $invoices = $this->invoiceService->generateInvoicesForMonthRangeAll(
                    $startMonth,
                    $endMonth,
                    $startYear,
                    null, // No specific generation mode - will use each enrollment's payment method
                    $paymentMethod
                );
            }
            $generatedCount = count($invoices);
        }

        $startMonthName = \DateTime::createFromFormat('!m', $startMonth)->format('F');
        $endMonthName = \DateTime::createFromFormat('!m', $endMonth)->format('F');
        
        // Handle cross-year period text
        if ($startYear === $endYear) {
            $periodText = $startMonth === $endMonth ? 
                "{$startMonthName} {$startYear}" : 
                "{$startMonthName} - {$endMonthName} {$startYear}";
        } else {
            $periodText = "{$startMonthName} {$startYear} - {$endMonthName} {$endYear}";
        }
            
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
        // Get non-active invoices (only pending and overdue, not paid or verified)
        $nonActiveInvoices = Invoice::whereIn('payment_status', ['pending', 'overdue'])->get();
        
        if ($nonActiveInvoices->count() === 0) {
            return redirect()->route('admin.invoices')->with('info', 'No non-active invoices found to delete.');
        }
        
        $deletedCount = 0;
        
        // Use database transaction to ensure data consistency
        DB::transaction(function () use ($nonActiveInvoices, &$deletedCount) {
            foreach ($nonActiveInvoices as $invoice) {
                // Delete all related records first
                $invoice->payments()->delete();
                $invoice->items()->delete();
                
                // Delete the invoice
                $invoice->delete();
                $deletedCount++;
            }
        });
        
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