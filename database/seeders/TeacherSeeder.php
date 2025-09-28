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
                'name' => 'Dr. Siti Nurhaliza, M.Pd',
                'email' => 'siti.nurhaliza@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Pendidikan Matematika',
                    'institution' => 'Universitas Negeri Jakarta',
                    'graduation_year' => 2015,
                    'hire_date' => '2016-08-15',
                    'employment_status' => 'full-time',
                    'phone' => '08123456789',
                    'address' => 'Jl. Pendidikan No. 123, Jakarta Selatan, DKI Jakarta',
                    'date_of_birth' => '1985-03-15',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Matematika, Instruktur Kalkulus Lanjutan, Microsoft Certified Educator',
                    'notes' => 'Ahli dalam matematika lanjutan dan statistika. Pengalaman 8 tahun mengajar.'
                ]
            ],
            [
                'name' => 'Prof. Dr. Ahmad Wijaya, M.Si',
                'email' => 'ahmad.wijaya@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Fisika',
                    'institution' => 'Institut Teknologi Bandung',
                    'graduation_year' => 2012,
                    'hire_date' => '2013-09-01',
                    'employment_status' => 'full-time',
                    'phone' => '08123456790',
                    'address' => 'Jl. Sains No. 456, Bandung, Jawa Barat',
                    'date_of_birth' => '1988-07-22',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru Fisika, Laboratorium Safety Certified, STEM Education Specialist',
                    'notes' => 'Spesialis fisika kuantum dan eksperimen laboratorium. Pengalaman 10 tahun.'
                ]
            ],
            [
                'name' => 'Sari Dewi, M.Pd',
                'email' => 'sari.dewi@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Sastra Inggris',
                    'institution' => 'Universitas Gadjah Mada',
                    'graduation_year' => 2018,
                    'hire_date' => '2019-01-15',
                    'employment_status' => 'full-time',
                    'phone' => '08123456791',
                    'address' => 'Jl. Sastra No. 789, Yogyakarta, DIY',
                    'date_of_birth' => '1990-11-08',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Bahasa Inggris, TOEFL Instructor, Creative Writing Specialist',
                    'notes' => 'Bersemangat dalam creative writing dan puisi. Pengalaman 5 tahun.'
                ]
            ],
            [
                'name' => 'Dr. Budi Santoso, M.Si',
                'email' => 'budi.santoso@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Kimia',
                    'institution' => 'Universitas Brawijaya',
                    'graduation_year' => 2010,
                    'hire_date' => '2011-08-20',
                    'employment_status' => 'full-time',
                    'phone' => '08123456792',
                    'address' => 'Jl. Kimia No. 321, Malang, Jawa Timur',
                    'date_of_birth' => '1982-05-12',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru Kimia, Laboratorium Safety Certified, Research Methodology Expert',
                    'notes' => 'Ahli kimia organik dan metodologi penelitian. Pengalaman 12 tahun.'
                ]
            ],
            [
                'name' => 'Maya Sari, M.Si',
                'email' => 'maya.sari@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Biologi',
                    'institution' => 'Universitas Airlangga',
                    'graduation_year' => 2020,
                    'hire_date' => '2024-01-10',
                    'employment_status' => 'part-time',
                    'phone' => '08123456793',
                    'address' => 'Jl. Biologi No. 654, Surabaya, Jawa Timur',
                    'date_of_birth' => '1992-09-25',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Biologi, Environmental Science Certified, Digital Learning Specialist',
                    'notes' => 'Guru baru yang spesialis biologi lingkungan. Fresh graduate dengan semangat tinggi.'
                ]
            ],
            [
                'name' => 'Rudi Hartono, M.Pd',
                'email' => 'rudi.hartono@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Pendidikan Sejarah',
                    'institution' => 'Universitas Negeri Yogyakarta',
                    'graduation_year' => 2017,
                    'hire_date' => '2018-07-01',
                    'employment_status' => 'full-time',
                    'phone' => '08123456794',
                    'address' => 'Jl. Sejarah No. 987, Yogyakarta, DIY',
                    'date_of_birth' => '1987-12-03',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru Sejarah, Museum Education Specialist, Cultural Heritage Expert',
                    'notes' => 'Ahli sejarah Indonesia dan budaya. Pengalaman 6 tahun mengajar.'
                ]
            ],
            [
                'name' => 'Dewi Kartika, M.Pd',
                'email' => 'dewi.kartika@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Pendidikan Bahasa Indonesia',
                    'institution' => 'Universitas Negeri Malang',
                    'graduation_year' => 2019,
                    'hire_date' => '2020-01-15',
                    'employment_status' => 'full-time',
                    'phone' => '08123456795',
                    'address' => 'Jl. Bahasa No. 147, Malang, Jawa Timur',
                    'date_of_birth' => '1991-04-18',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Bahasa Indonesia, Literature Specialist, Creative Writing Instructor',
                    'notes' => 'Spesialis sastra Indonesia dan creative writing. Pengalaman 4 tahun.'
                ]
            ],
            [
                'name' => 'Dr. Agus Prasetyo, M.Si',
                'email' => 'agus.prasetyo@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Teknik Informatika',
                    'institution' => 'Institut Teknologi Sepuluh Nopember',
                    'graduation_year' => 2016,
                    'hire_date' => '2017-08-01',
                    'employment_status' => 'full-time',
                    'phone' => '08123456796',
                    'address' => 'Jl. Teknologi No. 258, Surabaya, Jawa Timur',
                    'date_of_birth' => '1986-08-30',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru TIK, Microsoft Certified Educator, Programming Specialist',
                    'notes' => 'Ahli teknologi informasi dan programming. Pengalaman 7 tahun.'
                ]
            ],
            [
                'name' => 'Siti Rahayu, M.Pd',
                'email' => 'siti.rahayu@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Pendidikan Seni Rupa',
                    'institution' => 'Institut Seni Indonesia Yogyakarta',
                    'graduation_year' => 2021,
                    'hire_date' => '2022-01-10',
                    'employment_status' => 'contract',
                    'phone' => '08123456797',
                    'address' => 'Jl. Seni No. 369, Yogyakarta, DIY',
                    'date_of_birth' => '1993-06-14',
                    'gender' => 'female',
                    'certifications' => 'Sertifikasi Guru Seni, Digital Art Specialist, Creative Design Expert',
                    'notes' => 'Guru seni rupa dengan keahlian digital art. Pengalaman 2 tahun.'
                ]
            ],
            [
                'name' => 'Bambang Sutrisno, M.Pd',
                'email' => 'bambang.sutrisno@sekolahindonesia.sch.id',
                'password' => 'password123',
                'status' => 'active',
                'teacher_data' => [
                    'education_level' => 'S2 Pendidikan Olahraga',
                    'institution' => 'Universitas Negeri Semarang',
                    'graduation_year' => 2014,
                    'hire_date' => '2015-07-01',
                    'employment_status' => 'full-time',
                    'phone' => '08123456798',
                    'address' => 'Jl. Olahraga No. 741, Semarang, Jawa Tengah',
                    'date_of_birth' => '1984-01-20',
                    'gender' => 'male',
                    'certifications' => 'Sertifikasi Guru PJOK, Fitness Trainer, Sports Medicine Specialist',
                    'notes' => 'Guru olahraga dengan keahlian fitness dan sports medicine. Pengalaman 9 tahun.'
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

            // Create teacher profile (employee_number will be auto-generated)
            $teacher = $user->teacherProfile()->create($teacherData['teacher_data']);

            $this->command->info("Created teacher: {$teacherData['name']} with employee number: {$teacher->employee_number}");
        }

        $this->command->info('Teacher seeder completed successfully!');
    }
}