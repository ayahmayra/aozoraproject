<?php

namespace App\Console\Commands;

use App\Services\InvoiceGenerationService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly {--date= : Specific date to generate for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly invoices for all active enrollments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ?? now()->format('Y-m-d');
        
        $this->info("Generating invoices for date: {$date}");
        
        $service = new InvoiceGenerationService();
        
        // Generate invoices for monthly payments
        $monthlyInvoices = $service->generateInvoicesForDate($date, 'monthly');
        
        // Check and update overdue invoices
        $overdueCount = $service->checkAndUpdateOverdueInvoices();
        
        $this->info("Generated " . count($monthlyInvoices) . " monthly invoices");
        $this->info("Updated {$overdueCount} overdue invoices");
        
        if (count($monthlyInvoices) > 0) {
            // Load relationships for display
            $invoicesWithRelations = collect($monthlyInvoices)->load(['student.user', 'subject']);
            
            $this->table(
                ['Invoice Number', 'Student', 'Subject', 'Amount', 'Due Date'],
                $invoicesWithRelations->map(function ($invoice) {
                    return [
                        $invoice->invoice_number,
                        $invoice->student->user->name ?? 'Unknown',
                        $invoice->subject->name ?? 'Unknown',
                        'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
                        $invoice->due_date->format('Y-m-d'),
                    ];
                })
            );
        }
        
        return Command::SUCCESS;
    }
}