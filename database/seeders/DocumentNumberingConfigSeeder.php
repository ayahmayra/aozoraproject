<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentNumberingConfig;

class DocumentNumberingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'entity_type' => 'student',
                'prefix' => 'STU',
                'suffix' => null,
                'current_number' => 1,
                'number_length' => 4,
                'separator' => '',
                'include_year' => true,
                'include_month' => false,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'd',
                'reset_yearly' => true,
                'reset_monthly' => false,
                'reset_daily' => false,
                'description' => 'Student ID format: STU20240001',
                'is_active' => true,
            ],
            [
                'entity_type' => 'teacher',
                'prefix' => 'TCH',
                'suffix' => null,
                'current_number' => 1,
                'number_length' => 4,
                'separator' => '',
                'include_year' => true,
                'include_month' => false,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'd',
                'reset_yearly' => true,
                'reset_monthly' => false,
                'reset_daily' => false,
                'description' => 'Teacher ID format: TCH20240001',
                'is_active' => true,
            ],
            [
                'entity_type' => 'parent',
                'prefix' => 'PRT',
                'suffix' => null,
                'current_number' => 1,
                'number_length' => 4,
                'separator' => '',
                'include_year' => true,
                'include_month' => false,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'd',
                'reset_yearly' => true,
                'reset_monthly' => false,
                'reset_daily' => false,
                'description' => 'Parent ID format: PRT20240001',
                'is_active' => true,
            ],
            [
                'entity_type' => 'enrollment',
                'prefix' => 'ENR',
                'suffix' => null,
                'current_number' => 1,
                'number_length' => 6,
                'separator' => '',
                'include_year' => true,
                'include_month' => true,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'd',
                'reset_yearly' => false,
                'reset_monthly' => false,
                'reset_daily' => false,
                'description' => 'Enrollment ID format: ENR202401000001',
                'is_active' => true,
            ],
            [
                'entity_type' => 'subject',
                'prefix' => 'SUB',
                'suffix' => null,
                'current_number' => 1,
                'number_length' => 3,
                'separator' => '',
                'include_year' => false,
                'include_month' => false,
                'include_day' => false,
                'year_format' => 'Y',
                'month_format' => 'm',
                'day_format' => 'd',
                'reset_yearly' => false,
                'reset_monthly' => false,
                'reset_daily' => false,
                'description' => 'Subject ID format: SUB001',
                'is_active' => true,
            ],
        ];

        foreach ($configs as $config) {
            DocumentNumberingConfig::updateOrCreate(
                ['entity_type' => $config['entity_type']],
                $config
            );
        }
    }
}