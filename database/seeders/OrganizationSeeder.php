<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default organization
        Organization::create([
            'name' => 'Aozora Education Center',
            'short_name' => 'AEC',
            'description' => 'A comprehensive educational institution providing quality education and holistic development for students.',
            'email' => 'info@aozora.edu',
            'phone' => '+6281234567890',
            'website' => 'https://www.aozora.edu',
            'address' => 'Jl. Pendidikan No. 123',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '12345',
            'country' => 'Indonesia',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#1E40AF',
            'accent_color' => '#F59E0B',
            'mission' => 'To provide quality education that empowers students to become responsible, creative, and globally competitive individuals.',
            'vision' => 'To be a leading educational institution that shapes the future through innovative learning and character development.',
            'values' => 'Excellence, Integrity, Innovation, Respect, and Collaboration.',
            'founded_year' => '2020',
            'license_number' => 'SK-001/2020',
            'tax_id' => '123456789012345',
            'social_media' => [
                'facebook' => 'https://facebook.com/aozoraeducation',
                'instagram' => 'https://instagram.com/aozoraeducation',
                'twitter' => 'https://twitter.com/aozoraeducation',
                'youtube' => 'https://youtube.com/aozoraeducation',
            ],
            'contact_persons' => [
                [
                    'name' => 'Dr. Sarah Johnson',
                    'position' => 'Principal',
                    'email' => 'principal@aozora.edu',
                    'phone' => '+6281234567891',
                ],
                [
                    'name' => 'Mr. Ahmad Rahman',
                    'position' => 'Vice Principal',
                    'email' => 'vice.principal@aozora.edu',
                    'phone' => '+6281234567892',
                ],
            ],
            'is_active' => true,
        ]);

        $this->command->info('✅ Default organization created successfully!');
        $this->command->info('🏢 Organization: Aozora Education Center');
        $this->command->info('📧 Email: info@aozora.edu');
        $this->command->info('🌐 Website: https://www.aozora.edu');
    }
}
