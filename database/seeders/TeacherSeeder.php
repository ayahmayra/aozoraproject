<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Dr. Siti Nurhaliza',
                'email' => 'siti.nurhaliza@school.com',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'employee_number' => 'T001',
                    'education_level' => 'S3 Matematika',
                    'institution' => 'Universitas Indonesia',
                    'graduation_year' => 2015,
                    'hire_date' => '2016-08-15',
                    'employment_status' => 'Full-time',
                    'phone' => '08123456789',
                    'address' => 'Jl. Pendidikan No. 123, Jakarta Selatan',
                    'date_of_birth' => '1985-03-15',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Matematika, Instruktur Kalkulus Lanjutan',
                    'notes' => 'Ahli dalam matematika lanjutan dan statistika'
                ]
            ],
            [
                'name' => 'Prof. Dr. Ahmad Wijaya',
                'email' => 'ahmad.wijaya@school.com',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'employee_number' => 'T002',
                    'education_level' => 'S2 Fisika',
                    'institution' => 'Institut Teknologi Bandung',
                    'graduation_year' => 2012,
                    'hire_date' => '2013-09-01',
                    'employment_status' => 'Full-time',
                    'phone' => '08123456790',
                    'address' => 'Jl. Sains No. 456, Bandung',
                    'date_of_birth' => '1988-07-22',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru Fisika, Laboratorium Safety Certified',
                    'notes' => 'Spesialis fisika kuantum dan eksperimen laboratorium'
                ]
            ],
            [
                'name' => 'Sari Dewi, M.Pd',
                'email' => 'sari.dewi@school.com',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'employee_number' => 'T003',
                    'education_level' => 'S2 Sastra Inggris',
                    'institution' => 'Universitas Gadjah Mada',
                    'graduation_year' => 2018,
                    'hire_date' => '2019-01-15',
                    'employment_status' => 'Full-time',
                    'phone' => '08123456791',
                    'address' => 'Jl. Sastra No. 789, Yogyakarta',
                    'date_of_birth' => '1990-11-08',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Bahasa Inggris, Instruktur Creative Writing',
                    'notes' => 'Bersemangat dalam creative writing dan puisi'
                ]
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'email' => 'budi.santoso@school.com',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'employee_number' => 'T004',
                    'education_level' => 'S3 Kimia',
                    'institution' => 'Universitas Brawijaya',
                    'graduation_year' => 2010,
                    'hire_date' => '2011-08-20',
                    'employment_status' => 'Full-time',
                    'phone' => '08123456792',
                    'address' => 'Jl. Kimia No. 321, Malang',
                    'date_of_birth' => '1982-05-12',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru Kimia, Laboratorium Safety Certified',
                    'notes' => 'Ahli kimia organik dan metodologi penelitian'
                ]
            ],
            [
                'name' => 'Maya Sari, M.Si',
                'email' => 'maya.sari@school.com',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'employee_number' => 'T005',
                    'education_level' => 'S2 Biologi',
                    'institution' => 'Universitas Airlangga',
                    'graduation_year' => 2020,
                    'hire_date' => '2024-01-10',
                    'employment_status' => 'Part-time',
                    'phone' => '08123456793',
                    'address' => 'Jl. Biologi No. 654, Surabaya',
                    'date_of_birth' => '1992-09-25',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Biologi, Environmental Science Certified',
                    'notes' => 'Guru baru yang spesialis biologi lingkungan'
                ]
            ]
        ];

        foreach ($teachers as $teacherData) {
            // Create user
            $user = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make($teacherData['password']),
                'status' => $teacherData['status'],
            ]);

            // Assign teacher role
            $user->assignRole('teacher');

            // Create teacher profile
            $user->teacherProfile()->create($teacherData['teacher_data']);

            $this->command->info("Created teacher: {$teacherData['name']}");
        }

        $this->command->info('Teacher seeder completed successfully!');
    }
}