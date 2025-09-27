<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentNumberingConfig;

class InvoiceNumberingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentNumberingConfig::updateOrCreate(
            [
                'entity_type' => 'invoice'
            ],
            [
                'entity_type' => 'invoice',
                'prefix' => 'INV',
                'suffix' => null,
                'separator' => null,
                'number_length' => 4,
                'current_number' => 0,
                'include_year' => true,
                'include_month' => true,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'DD',
                'reset_yearly' => true,
                'reset_monthly' => false,
                'reset_daily' => false,
                'is_active' => true,
                'description' => 'Invoice numbering configuration - format: INV{YYYY}{MM}{####}'
            ]
        );
    }
}