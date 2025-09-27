<?php

namespace App\Console\Commands;

use App\Services\InvoiceGenerationService;
use Illuminate\Console\Command;

class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update overdue invoices status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue invoices...');
        
        $service = new InvoiceGenerationService();
        $overdueCount = $service->checkAndUpdateOverdueInvoices();
        
        $this->info("Updated {$overdueCount} invoices to overdue status");
        
        return Command::SUCCESS;
    }
}