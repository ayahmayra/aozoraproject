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
        // Create test parent user
        $parentUser = User::create([
            'name' => 'John Parent',
            'email' => 'parent@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $parentUser->assignRole('parent');

        // Create parent profile
        ParentUser::create([
            'user_id' => $parentUser->id,
            'phone' => '+6281234567890',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'occupation' => 'Engineer',
            'workplace' => 'PT. Example',
            'date_of_birth' => '1980-05-15',
            'gender' => 'male',
            'emergency_contact_name' => 'Jane Parent',
            'emergency_contact_phone' => '+6281234567891',
            'notes' => 'Emergency contact for student',
        ]);

        // Create test student user
        $studentUser = User::create([
            'name' => 'Alice Student',
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $studentUser->assignRole('student');

        // Create student profile
        Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'STU001',
            'date_of_birth' => '2010-03-20',
            'gender' => 'female',
            'phone' => '+6281234567892',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'parent_name' => 'John Parent',
            'parent_phone' => '+6281234567890',
            'parent_address' => 'Jl. Contoh No. 123, Jakarta',
            'school_origin' => 'SD Example',
            'medical_notes' => 'No allergies',
            'notes' => 'Good student',
            'status' => 'active',
        ]);

        // Create test teacher user
        $teacherUser = User::create([
            'name' => 'Bob Teacher',
            'email' => 'teacher@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $teacherUser->assignRole('teacher');

        // Create teacher profile
        Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_number' => 'EMP001',
            'date_of_birth' => '1985-08-10',
            'gender' => 'male',
            'phone' => '+6281234567893',
            'address' => 'Jl. Teacher No. 456, Jakarta',
            'subject_specialization' => 'Mathematics',
            'education_level' => 'Bachelor',
            'institution' => 'University of Example',
            'graduation_year' => 2008,
            'hire_date' => '2010-01-15',
            'employment_status' => 'full-time',
            'certifications' => 'Teaching Certificate, Math Specialist',
            'notes' => 'Experienced teacher',
        ]);

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('👤 Parent: parent@test.com / password');
        $this->command->info('👤 Student: student@test.com / password');
        $this->command->info('👤 Teacher: teacher@test.com / password');
    }
}
