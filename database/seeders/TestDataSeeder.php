<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ParentUser;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get test parent user
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'John Parent',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$parentUser->hasRole('parent')) {
            $parentUser->assignRole('parent');
        }

        // Create or update parent profile
        ParentUser::updateOrCreate(
            ['user_id' => $parentUser->id],
            [
                'phone' => '+6281234567890',
                'address' => 'Jl. Contoh No. 123, Jakarta',
                'occupation' => 'Engineer',
                'workplace' => 'PT. Example',
                'date_of_birth' => '1980-05-15',
                'gender' => 'male',
                'emergency_contact_name' => 'Jane Parent',
                'emergency_contact_phone' => '+6281234567891',
                'notes' => 'Emergency contact for student',
            ]
        );

        // Create or get test student user
        $studentUser = User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Alice Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$studentUser->hasRole('student')) {
            $studentUser->assignRole('student');
        }

        // Create or update student profile
        Student::updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'parent_id' => $parentUser->id,
                'student_id' => 'STU001',
                'date_of_birth' => '2010-03-20',
                'gender' => 'female',
                'phone' => '+6281234567892',
                'address' => 'Jl. Contoh No. 123, Jakarta',
                'school_origin' => 'SD Example',
                'medical_notes' => 'No allergies',
                'notes' => 'Good student',
                'status' => 'active',
            ]
        );

        // Create or get test teacher user
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Bob Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$teacherUser->hasRole('teacher')) {
            $teacherUser->assignRole('teacher');
        }

        // Create or update teacher profile
        Teacher::updateOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'employee_number' => 'EMP001',
                'date_of_birth' => '1985-08-10',
                'gender' => 'male',
                'phone' => '+6281234567893',
                'address' => 'Jl. Teacher No. 456, Jakarta',
                'education_level' => 'Bachelor',
                'institution' => 'University of Example',
                'graduation_year' => 2008,
                'hire_date' => '2010-01-15',
                'employment_status' => 'full-time',
                'certifications' => 'Teaching Certificate, Math Specialist',
                'notes' => 'Experienced teacher',
            ]
        );

        $this->command->info('✅ Test data created/updated successfully!');
        $this->command->info('👤 Parent: parent@test.com / password');
        $this->command->info('👤 Student: student@test.com / password');
        $this->command->info('👤 Teacher: teacher@test.com / password');
    }
}
