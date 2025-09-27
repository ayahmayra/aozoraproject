<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Setting up school management system...');
        
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            OrganizationSeeder::class,
            InvoiceNumberingSeeder::class,
            TestDataSeeder::class,
        ]);
        
        $this->command->info('✅ System setup complete!');
        $this->command->info('📝 Note: New user registrations will automatically be assigned the "parent" role.');
        $this->command->info('👤 Admin user: admin@school.com / password');
    }
}
