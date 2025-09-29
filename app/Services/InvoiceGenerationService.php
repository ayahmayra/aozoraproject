<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\StudentSubject;
use App\Services\DocumentNumberingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceGenerationService
{
    /**
     * Generate invoices for a specific enrollment.
     */
    public function generateInvoicesForEnrollment(StudentSubject $enrollment): array
    {
        $invoices = [];
        
        switch ($enrollment->payment_method) {
            case 'monthly':
                $invoices = $this->generateMonthlyInvoices($enrollment);
                break;
            case 'semester':
                $invoices = $this->generateSemesterInvoices($enrollment);
                break;
            case 'yearly':
                $invoices = $this->generateYearlyInvoices($enrollment);
                break;
        }
        
        return $invoices;
    }

    /**
     * Generate monthly invoices for an enrollment.
     */
    public function generateMonthlyInvoices(StudentSubject $enrollment): array
    {
        $invoices = [];
        $startDate = Carbon::parse($enrollment->start_date ?? now());
        $endDate = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        $amount = $enrollment->payment_amount;
        
        $currentDate = $startDate->copy()->startOfMonth();
        
        while ($currentDate->lt($endDate)) {
            $billingPeriodEnd = $currentDate->copy()->endOfMonth();
            
            // Check if invoice already exists for this period
            if (!$this->hasInvoiceForPeriod($enrollment, $currentDate, $billingPeriodEnd)) {
                $invoice = $this->createInvoice($enrollment, $currentDate, $billingPeriodEnd, (float)$amount, 'monthly');
                $invoices[] = $invoice;
            }
            
            $currentDate->addMonth();
        }
        
        return $invoices;
    }

    /**
     * Generate semester invoices for an enrollment.
     */
    public function generateSemesterInvoices(StudentSubject $enrollment): array
    {
        $invoices = [];
        $startDate = Carbon::parse($enrollment->start_date ?? now());
        $endDate = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        $amount = $enrollment->payment_amount;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lt($endDate)) {
            $billingPeriodEnd = $currentDate->copy()->addMonths(6)->subDay();
            
            // Check if invoice already exists for this period
            if (!$this->hasInvoiceForPeriod($enrollment, $currentDate, $billingPeriodEnd)) {
                $invoice = $this->createInvoice($enrollment, $currentDate, $billingPeriodEnd, (float)$amount, 'semester');
                $invoices[] = $invoice;
            }
            
            $currentDate->addMonths(6);
        }
        
        return $invoices;
    }

    /**
     * Generate yearly invoices for an enrollment.
     */
    public function generateYearlyInvoices(StudentSubject $enrollment): array
    {
        $invoices = [];
        $startDate = Carbon::parse($enrollment->start_date ?? now());
        $endDate = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        $amount = $enrollment->payment_amount;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lt($endDate)) {
            $billingPeriodEnd = $currentDate->copy()->addYear()->subDay();
            
            // Check if invoice already exists for this period
            if (!$this->hasInvoiceForPeriod($enrollment, $currentDate, $billingPeriodEnd)) {
                $invoice = $this->createInvoice($enrollment, $currentDate, $billingPeriodEnd, (float)$amount, 'yearly');
                $invoices[] = $invoice;
            }
            
            $currentDate->addYear();
        }
        
        return $invoices;
    }

    /**
     * Create a single invoice.
     */
    public function createInvoice(
        StudentSubject $enrollment,
        Carbon $billingPeriodStart,
        Carbon $billingPeriodEnd,
        float $amount,
        string $paymentMethod
    ): Invoice {
        return DB::transaction(function () use ($enrollment, $billingPeriodStart, $billingPeriodEnd, $amount, $paymentMethod) {
            // Generate invoice number using DocumentNumberingService
            $invoiceNumber = DocumentNumberingService::generateNextNumber('invoice');
            
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'student_id' => $enrollment->student_id, // This refers to students.id, not users.id
                'subject_id' => $enrollment->subject_id,
                'enrollment_id' => $enrollment->id,
                'invoice_date' => now(),
                'due_date' => $this->calculateDueDate($paymentMethod),
                'billing_period_start' => $billingPeriodStart,
                'billing_period_end' => $billingPeriodEnd,
                'amount' => $amount,
                'tax_amount' => 0, // No tax for now
                'total_amount' => $amount,
                'currency' => 'IDR',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Create invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_name' => $enrollment->subject->name . ' - ' . ucfirst($paymentMethod) . ' Fee',
                'item_description' => 'Tuition fee for ' . $enrollment->subject->name . ' (' . $billingPeriodStart->format('M Y') . ')',
                'item_type' => 'tuition',
                'quantity' => 1,
                'unit_price' => $amount,
                'total_price' => $amount,
            ]);

            return $invoice;
        });
    }

    /**
     * Check if invoice already exists for a period.
     */
    public function hasInvoiceForPeriod(
        StudentSubject $enrollment,
        Carbon $billingPeriodStart,
        Carbon $billingPeriodEnd
    ): bool {
        return Invoice::where('enrollment_id', $enrollment->id)
            ->where('billing_period_start', $billingPeriodStart->format('Y-m-d'))
            ->where('billing_period_end', $billingPeriodEnd->format('Y-m-d'))
            ->exists();
    }

    /**
     * Calculate due date based on payment method.
     */
    private function calculateDueDate(string $paymentMethod): Carbon
    {
        $dueDays = match ($paymentMethod) {
            'monthly' => 7,
            'semester' => 14,
            'yearly' => 30,
            default => 7,
        };

        return now()->addDays($dueDays);
    }

    /**
     * Generate invoices for all active enrollments.
     */
    public function generateInvoicesForAllActiveEnrollments(): array
    {
        $enrollments = StudentSubject::active()
            ->with(['student', 'subject'])
            ->get();

        $generatedInvoices = [];

        foreach ($enrollments as $enrollment) {
            $invoices = $this->generateInvoicesForEnrollment($enrollment);
            $generatedInvoices = array_merge($generatedInvoices, $invoices);
        }

        return $generatedInvoices;
    }

    /**
     * Generate invoices for specific date and payment method.
     */
    public function generateInvoicesForDate(string $date, ?string $paymentMethod = null): array
    {
        $query = StudentSubject::active()
            ->with(['student', 'subject']);

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $enrollments = $query->get();
        $generatedInvoices = [];

        foreach ($enrollments as $enrollment) {
            // Only generate if enrollment started before or on the specified date
            $enrollmentStart = Carbon::parse($enrollment->start_date ?? $enrollment->enrollment_date);
            if ($enrollmentStart->lte(Carbon::parse($date))) {
                $invoices = $this->generateInvoicesForEnrollment($enrollment);
                $generatedInvoices = array_merge($generatedInvoices, $invoices);
            }
        }

        return $generatedInvoices;
    }

    /**
     * Check for overdue invoices and update status.
     */
    public function checkAndUpdateOverdueInvoices(): int
    {
        $overdueCount = Invoice::where('due_date', '<', now())
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'overdue']);

        return $overdueCount;
    }

    /**
     * Generate invoices for a specific enrollment within a custom period.
     */
    public function generateInvoicesForPeriod(
        StudentSubject $enrollment, 
        string $periodStart, 
        string $periodEnd, 
        string $generationMode,
        ?string $paymentMethodFilter = null
    ): array {
        // Filter by payment method if specified
        if ($paymentMethodFilter && $enrollment->payment_method !== $paymentMethodFilter) {
            return [];
        }

        $periodStartDate = Carbon::parse($periodStart);
        $periodEndDate = Carbon::parse($periodEnd);
        $invoices = [];

        switch ($generationMode) {
            case 'period':
                // Generate single invoice for the entire period
                if ($this->shouldGenerateInvoiceForPeriod($enrollment, $periodStartDate, $periodEndDate)) {
                    $invoice = $this->createInvoice(
                        $enrollment,
                        $periodStartDate,
                        $periodEndDate,
                        (float)$enrollment->payment_amount,
                        $enrollment->payment_method
                    );
                    $invoices[] = $invoice;
                }
                break;

            case 'monthly':
                // Generate monthly invoices within the period
                $invoices = $this->generateMonthlyInvoicesForPeriod($enrollment, $periodStartDate, $periodEndDate);
                break;

            case 'semester':
                // Generate semester invoices within the period
                $invoices = $this->generateSemesterInvoicesForPeriod($enrollment, $periodStartDate, $periodEndDate);
                break;

            case 'yearly':
                // Generate yearly invoices within the period
                $invoices = $this->generateYearlyInvoicesForPeriod($enrollment, $periodStartDate, $periodEndDate);
                break;
        }

        return $invoices;
    }

    /**
     * Generate invoices for all enrollments within a custom period range.
     */
    public function generateInvoicesForPeriodRange(
        string $periodStart, 
        string $periodEnd, 
        string $generationMode,
        ?string $paymentMethodFilter = null
    ): array {
        $periodStartDate = Carbon::parse($periodStart);
        $periodEndDate = Carbon::parse($periodEnd);
        $allInvoices = [];

        // Get all active enrollments
        $query = StudentSubject::active()->with(['student', 'subject']);
        
        if ($paymentMethodFilter) {
            $query->where('payment_method', $paymentMethodFilter);
        }

        $enrollments = $query->get();

        foreach ($enrollments as $enrollment) {
            $invoices = $this->generateInvoicesForPeriod(
                $enrollment, 
                $periodStart, 
                $periodEnd, 
                $generationMode,
                $paymentMethodFilter
            );
            $allInvoices = array_merge($allInvoices, $invoices);
        }

        return $allInvoices;
    }

    /**
     * Check if invoice should be generated for the given period.
     */
    private function shouldGenerateInvoiceForPeriod(StudentSubject $enrollment, Carbon $periodStart, Carbon $periodEnd): bool
    {
        // Check if enrollment is active during this period
        $enrollmentStart = $enrollment->start_date ? Carbon::parse($enrollment->start_date) : now();
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();

        if ($periodStart->gt($enrollmentEnd) || $periodEnd->lt($enrollmentStart)) {
            return false;
        }

        // Check if invoice already exists for this period
        return !$this->hasInvoiceForPeriod($enrollment, $periodStart, $periodEnd);
    }

    /**
     * Generate monthly invoices for a specific period.
     */
    private function generateMonthlyInvoicesForPeriod(StudentSubject $enrollment, Carbon $periodStart, Carbon $periodEnd): array
    {
        $invoices = [];
        $currentDate = $periodStart->copy()->startOfMonth();
        
        while ($currentDate->lte($periodEnd)) {
            $billingPeriodEnd = $currentDate->copy()->endOfMonth();
            
            // Only generate if the month overlaps with the enrollment period
            if ($this->shouldGenerateInvoiceForPeriod($enrollment, $currentDate, $billingPeriodEnd)) {
                $invoice = $this->createInvoice(
                    $enrollment,
                    $currentDate,
                    $billingPeriodEnd,
                    (float)$enrollment->payment_amount,
                    $enrollment->payment_method
                );
                $invoices[] = $invoice;
            }
            
            $currentDate->addMonth();
        }
        
        return $invoices;
    }

    /**
     * Generate semester invoices for a specific period.
     */
    private function generateSemesterInvoicesForPeriod(StudentSubject $enrollment, Carbon $periodStart, Carbon $periodEnd): array
    {
        $invoices = [];
        $currentDate = $periodStart->copy();
        
        // Generate semester invoices (every 6 months)
        while ($currentDate->lte($periodEnd)) {
            $semesterEnd = $currentDate->copy()->addMonths(6)->subDay();
            
            if ($this->shouldGenerateInvoiceForPeriod($enrollment, $currentDate, $semesterEnd)) {
                $invoice = $this->createInvoice(
                    $enrollment,
                    $currentDate,
                    $semesterEnd,
                    (float)$enrollment->payment_amount * 6, // 6 months worth
                    $enrollment->payment_method
                );
                $invoices[] = $invoice;
            }
            
            $currentDate->addMonths(6);
        }
        
        return $invoices;
    }

    /**
     * Generate yearly invoices for a specific period.
     */
    private function generateYearlyInvoicesForPeriod(StudentSubject $enrollment, Carbon $periodStart, Carbon $periodEnd): array
    {
        $invoices = [];
        $currentDate = $periodStart->copy()->startOfYear();
        
        while ($currentDate->lte($periodEnd)) {
            $yearEnd = $currentDate->copy()->endOfYear();
            
            if ($this->shouldGenerateInvoiceForPeriod($enrollment, $currentDate, $yearEnd)) {
                $invoice = $this->createInvoice(
                    $enrollment,
                    $currentDate,
                    $yearEnd,
                    (float)$enrollment->payment_amount * 12, // 12 months worth
                    $enrollment->payment_method
                );
                $invoices[] = $invoice;
            }
            
            $currentDate->addYear();
        }
        
        return $invoices;
    }

    /**
     * Generate invoices for a specific enrollment within a month range.
     */
    public function generateInvoicesForMonthRange(
        StudentSubject $enrollment, 
        int $startMonth,
        int $endMonth,
        int $year,
        ?string $generationMode = null,
        ?string $paymentMethodFilter = null
    ): array {
        // Filter by payment method if specified
        if ($paymentMethodFilter && $enrollment->payment_method !== $paymentMethodFilter) {
            return [];
        }
        
        // Use enrollment's payment method if no generation mode specified
        if (!$generationMode) {
            $generationMode = $enrollment->payment_method;
        }

        $invoices = [];

        switch ($generationMode) {
            case 'monthly':
                // Generate monthly invoices for each month in range
                $invoices = $this->generateMonthlyInvoicesForMonthRange($enrollment, $startMonth, $endMonth, $year);
                break;

            case 'semester':
                // Generate semester invoices for the range
                $invoices = $this->generateSemesterInvoicesForMonthRange($enrollment, $startMonth, $endMonth, $year);
                break;

            case 'yearly':
                // Generate yearly invoice for the range
                $invoices = $this->generateYearlyInvoicesForMonthRange($enrollment, $startMonth, $endMonth, $year);
                break;
        }

        return $invoices;
    }

    /**
     * Generate invoices for a specific enrollment within a cross-year month range.
     */
    public function generateInvoicesForCrossYearRange(
        StudentSubject $enrollment, 
        int $startMonth,
        int $endMonth,
        int $startYear,
        int $endYear,
        ?string $generationMode = null,
        ?string $paymentMethodFilter = null
    ): array {
        // Filter by payment method if specified
        if ($paymentMethodFilter && $enrollment->payment_method !== $paymentMethodFilter) {
            return [];
        }
        
        // Use enrollment's payment method if no generation mode specified
        if (!$generationMode) {
            $generationMode = $enrollment->payment_method;
        }

        $invoices = [];

        switch ($generationMode) {
            case 'monthly':
                // Generate monthly invoices for cross-year range
                $invoices = $this->generateMonthlyInvoicesForCrossYearRange($enrollment, $startMonth, $endMonth, $startYear, $endYear);
                break;

            case 'semester':
                // Generate semester invoices for cross-year range
                $invoices = $this->generateSemesterInvoicesForCrossYearRange($enrollment, $startMonth, $endMonth, $startYear, $endYear);
                break;

            case 'yearly':
                // Generate yearly invoice for cross-year range
                $invoices = $this->generateYearlyInvoicesForCrossYearRange($enrollment, $startMonth, $endMonth, $startYear, $endYear);
                break;
        }

        return $invoices;
    }

    /**
     * Generate semester invoices for a cross-year range.
     */
    private function generateSemesterInvoicesForCrossYearRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $startYear, int $endYear): array
    {
        $invoices = [];
        
        // Calculate the period start and end dates
        $periodStart = Carbon::createFromDate($startYear, $startMonth, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($endYear, $endMonth, 1)->endOfMonth();
        
        // Get enrollment start date
        $enrollmentStart = Carbon::parse($enrollment->start_date ?? now());
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        
        // Find the first semester period that starts on or before the requested period
        $currentSemesterStart = $enrollmentStart->copy();
        
        // Align to semester boundaries (every 6 months from enrollment start)
        while ($currentSemesterStart->lt($periodStart)) {
            $currentSemesterStart->addMonths(6);
        }
        
        // Go back to the previous semester if we overshot
        if ($currentSemesterStart->gt($periodStart)) {
            $currentSemesterStart->subMonths(6);
        }
        
        // Make sure we don't start before enrollment start
        if ($currentSemesterStart->lt($enrollmentStart)) {
            $currentSemesterStart = $enrollmentStart->copy();
        }
        
        // Generate semester invoices within the requested period
        while ($currentSemesterStart->lte($periodEnd)) {
            $semesterEnd = $currentSemesterStart->copy()->addMonths(6)->subDay();
            
            // Only generate if the semester period overlaps with the requested period
            if ($currentSemesterStart->lte($periodEnd) && 
                $semesterEnd->gte($periodStart) &&
                $currentSemesterStart->gte($enrollmentStart) &&
                $currentSemesterStart->lt($enrollmentEnd)) {
                
                if ($this->shouldGenerateInvoiceForPeriod($enrollment, $currentSemesterStart, $semesterEnd)) {
                    $invoice = $this->createInvoice(
                        $enrollment,
                        $currentSemesterStart,
                        $semesterEnd,
                        (float)$enrollment->payment_amount, // Single semester amount
                        $enrollment->payment_method
                    );
                    $invoices[] = $invoice;
                }
            }
            
            $currentSemesterStart->addMonths(6);
        }
        
        return $invoices;
    }

    /**
     * Generate monthly invoices for a cross-year range.
     */
    private function generateMonthlyInvoicesForCrossYearRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $startYear, int $endYear): array
    {
        $invoices = [];
        
        // Calculate the period start and end dates
        $periodStart = Carbon::createFromDate($startYear, $startMonth, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($endYear, $endMonth, 1)->endOfMonth();
        
        // Get enrollment start date
        $enrollmentStart = Carbon::parse($enrollment->start_date ?? now());
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        
        // Generate monthly invoices within the requested period
        $currentMonth = $periodStart->copy();
        
        while ($currentMonth->lte($periodEnd)) {
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            
            // Only generate if the month overlaps with the enrollment period
            if ($this->shouldGenerateInvoiceForPeriod($enrollment, $monthStart, $monthEnd)) {
                $invoice = $this->createInvoice(
                    $enrollment,
                    $monthStart,
                    $monthEnd,
                    (float)$enrollment->payment_amount,
                    $enrollment->payment_method
                );
                $invoices[] = $invoice;
            }
            
            $currentMonth->addMonth();
        }
        
        return $invoices;
    }

    /**
     * Generate yearly invoices for a cross-year range.
     */
    private function generateYearlyInvoicesForCrossYearRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $startYear, int $endYear): array
    {
        $invoices = [];
        
        // Calculate the period start and end dates
        $periodStart = Carbon::createFromDate($startYear, $startMonth, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($endYear, $endMonth, 1)->endOfMonth();
        
        // Get enrollment start date
        $enrollmentStart = Carbon::parse($enrollment->start_date ?? now());
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        
        // For yearly payments, generate one invoice per year within the period
        $currentYear = $periodStart->year;
        $endYear = $periodEnd->year;
        
        while ($currentYear <= $endYear) {
            $yearStart = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $yearEnd = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();
            
            // Only generate if the year overlaps with the requested period and enrollment
            if ($yearStart->lte($periodEnd) && 
                $yearEnd->gte($periodStart) &&
                $yearStart->gte($enrollmentStart) &&
                $yearStart->lt($enrollmentEnd)) {
                
                if ($this->shouldGenerateInvoiceForPeriod($enrollment, $yearStart, $yearEnd)) {
                    $invoice = $this->createInvoice(
                        $enrollment,
                        $yearStart,
                        $yearEnd,
                        (float)$enrollment->payment_amount,
                        $enrollment->payment_method
                    );
                    $invoices[] = $invoice;
                }
            }
            
            $currentYear++;
        }
        
        return $invoices;
    }

    /**
     * Generate invoices for all enrollments within a month range.
     */
    public function generateInvoicesForMonthRangeAll(
        int $startMonth,
        int $endMonth,
        int $year,
        ?string $generationMode = null,
        ?string $paymentMethodFilter = null
    ): array {
        $allInvoices = [];

        // Get all active enrollments
        $query = StudentSubject::active()->with(['student', 'subject']);
        
        if ($paymentMethodFilter) {
            $query->where('payment_method', $paymentMethodFilter);
        }

        $enrollments = $query->get();

        foreach ($enrollments as $enrollment) {
            $invoices = $this->generateInvoicesForMonthRange(
                $enrollment, 
                $startMonth,
                $endMonth,
                $year,
                $generationMode,
                $paymentMethodFilter
            );
            $allInvoices = array_merge($allInvoices, $invoices);
        }

        return $allInvoices;
    }

    /**
     * Generate invoices for all enrollments within a cross-year month range.
     */
    public function generateInvoicesForCrossYearRangeAll(
        int $startMonth,
        int $endMonth,
        int $startYear,
        int $endYear,
        ?string $generationMode = null,
        ?string $paymentMethodFilter = null
    ): array {
        $allInvoices = [];

        // Get all active enrollments
        $query = StudentSubject::active()->with(['student', 'subject']);
        
        if ($paymentMethodFilter) {
            $query->where('payment_method', $paymentMethodFilter);
        }

        $enrollments = $query->get();

        foreach ($enrollments as $enrollment) {
            $invoices = $this->generateInvoicesForCrossYearRange(
                $enrollment, 
                $startMonth,
                $endMonth,
                $startYear,
                $endYear,
                $generationMode,
                $paymentMethodFilter
            );
            $allInvoices = array_merge($allInvoices, $invoices);
        }

        return $allInvoices;
    }

    /**
     * Generate monthly invoices for a specific month range.
     */
    private function generateMonthlyInvoicesForMonthRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $year): array
    {
        $invoices = [];
        
        // Handle month range (including cross-year scenarios)
        $months = $this->getMonthRange($startMonth, $endMonth);
        
        foreach ($months as $month) {
            $currentYear = $year;
            
            // Handle cross-year scenarios (e.g., Nov to Feb)
            if ($startMonth > $endMonth && $month <= $endMonth) {
                $currentYear = $year + 1;
            }
            
            $periodStart = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $periodEnd = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();
            
            // Only generate if the month overlaps with the enrollment period
            if ($this->shouldGenerateInvoiceForPeriod($enrollment, $periodStart, $periodEnd)) {
                $invoice = $this->createInvoice(
                    $enrollment,
                    $periodStart,
                    $periodEnd,
                    (float)$enrollment->payment_amount,
                    $enrollment->payment_method
                );
                $invoices[] = $invoice;
            }
        }
        
        return $invoices;
    }

    /**
     * Generate semester invoices for a specific month range.
     */
    private function generateSemesterInvoicesForMonthRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $year): array
    {
        $invoices = [];
        
        // For semester payments, we need to find the correct semester periods
        // that overlap with the requested month range
        
        // Calculate the period start and end dates
        $periodStart = Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();
        
        // Get enrollment start date
        $enrollmentStart = Carbon::parse($enrollment->start_date ?? now());
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        
        // Find the first semester period that starts on or before the requested period
        // For semester payments, we need to align to 6-month periods from enrollment start
        $currentSemesterStart = $enrollmentStart->copy();
        
        // Align to semester boundaries (every 6 months from enrollment start)
        while ($currentSemesterStart->lt($periodStart)) {
            $currentSemesterStart->addMonths(6);
        }
        
        // Go back to the previous semester if we overshot
        if ($currentSemesterStart->gt($periodStart)) {
            $currentSemesterStart->subMonths(6);
        }
        
        // Make sure we don't start before enrollment start
        if ($currentSemesterStart->lt($enrollmentStart)) {
            $currentSemesterStart = $enrollmentStart->copy();
        }
        
        // Generate semester invoices within the requested period
        while ($currentSemesterStart->lte($periodEnd)) {
            $semesterEnd = $currentSemesterStart->copy()->addMonths(6)->subDay();
            
            // Only generate if the semester period overlaps with the requested period
            // and the enrollment is active during this period
            if ($currentSemesterStart->lte($periodEnd) && 
                $semesterEnd->gte($periodStart) &&
                $currentSemesterStart->gte($enrollmentStart) &&
                $currentSemesterStart->lt($enrollmentEnd)) {
                
                if ($this->shouldGenerateInvoiceForPeriod($enrollment, $currentSemesterStart, $semesterEnd)) {
                    $invoice = $this->createInvoice(
                        $enrollment,
                        $currentSemesterStart,
                        $semesterEnd,
                        (float)$enrollment->payment_amount, // Single semester amount
                        $enrollment->payment_method
                    );
                    $invoices[] = $invoice;
                }
            }
            
            $currentSemesterStart->addMonths(6);
        }
        
        return $invoices;
    }

    /**
     * Generate yearly invoices for a specific month range.
     */
    private function generateYearlyInvoicesForMonthRange(StudentSubject $enrollment, int $startMonth, int $endMonth, int $year): array
    {
        $invoices = [];
        
        // Calculate the period start and end dates
        $periodStart = Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();
        
        // Get enrollment start date
        $enrollmentStart = Carbon::parse($enrollment->start_date ?? now());
        $enrollmentEnd = $enrollment->end_date ? Carbon::parse($enrollment->end_date) : now()->addYear();
        
        // For yearly payments, generate one invoice per year within the period
        $currentYear = $periodStart->year;
        $endYear = $periodEnd->year;
        
        while ($currentYear <= $endYear) {
            $yearStart = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $yearEnd = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();
            
            // Only generate if the year overlaps with the requested period and enrollment
            if ($yearStart->lte($periodEnd) && 
                $yearEnd->gte($periodStart) &&
                $yearStart->gte($enrollmentStart) &&
                $yearStart->lt($enrollmentEnd)) {
                
                if ($this->shouldGenerateInvoiceForPeriod($enrollment, $yearStart, $yearEnd)) {
                    $invoice = $this->createInvoice(
                        $enrollment,
                        $yearStart,
                        $yearEnd,
                        (float)$enrollment->payment_amount, // Single year amount
                        $enrollment->payment_method
                    );
                    $invoices[] = $invoice;
                }
            }
            
            $currentYear++;
        }
        
        return $invoices;
    }

    /**
     * Get array of months in range, handling cross-year scenarios.
     */
    private function getMonthRange(int $startMonth, int $endMonth): array
    {
        $months = [];
        
        if ($startMonth <= $endMonth) {
            // Same year (e.g., 3 to 7 = March, April, May, June, July)
            for ($i = $startMonth; $i <= $endMonth; $i++) {
                $months[] = $i;
            }
        } else {
            // Cross year (e.g., 11 to 2 = Nov, Dec, Jan, Feb)
            for ($i = $startMonth; $i <= 12; $i++) {
                $months[] = $i;
            }
            for ($i = 1; $i <= $endMonth; $i++) {
                $months[] = $i;
            }
        }
        
        return $months;
    }
}
