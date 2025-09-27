<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\StudentSubject;
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
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
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
}
