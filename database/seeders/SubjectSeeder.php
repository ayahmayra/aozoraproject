<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Mathematics',
                'code' => 'MATH101',
                'description' => 'Basic mathematics including algebra, geometry, and trigonometry.',
            ],
            [
                'name' => 'English Language',
                'code' => 'ENG101',
                'description' => 'English language and literature studies.',
            ],
            [
                'name' => 'Science',
                'code' => 'SCI101',
                'description' => 'General science covering physics, chemistry, and biology.',
            ],
            [
                'name' => 'History',
                'code' => 'HIST101',
                'description' => 'World history and historical analysis.',
            ],
            [
                'name' => 'Geography',
                'code' => 'GEO101',
                'description' => 'Physical and human geography studies.',
            ],
            [
                'name' => 'Art',
                'code' => 'ART101',
                'description' => 'Visual arts and creative expression.',
            ],
            [
                'name' => 'Physical Education',
                'code' => 'PE101',
                'description' => 'Physical fitness and sports activities.',
            ],
            [
                'name' => 'Computer Science',
                'code' => 'CS101',
                'description' => 'Introduction to computer programming and technology.',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
