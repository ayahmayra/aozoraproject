<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Organization::first()->name ?? 'Aozora Education' }} - School Management System</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    @php
                        $organization = \App\Models\Organization::first();
                    @endphp
                    @if($organization && $organization->logo)
                        <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-10 w-10 rounded-lg mr-3">
                    @else
                        <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 1 1 0 00-.2-.38 1 1 0 00-.59-.29H9a1 1 0 100-2H4.72a1 1 0 00-.38.08l-1.04.317zM9 11a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $organization->name ?? 'Aozora Education' }}
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $organization->short_name ?? 'School Management System' }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Admin Dashboard
                                </a>
                            @elseif(auth()->user()->hasRole('parent'))
                                <a href="{{ route('parent.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Parent Dashboard
                                </a>
                            @elseif(auth()->user()->hasRole('teacher'))
                                <a href="{{ route('teacher.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Teacher Dashboard
                                </a>
                            @elseif(auth()->user()->hasRole('student'))
                                <a href="{{ route('student.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Student Dashboard
                                </a>
                            @else
                                <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Register as Parent
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Welcome to {{ $organization->name ?? 'Aozora Education' }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                    {{ $organization->description ?? 'Empowering students through innovative education and comprehensive learning experiences. Our school management system provides seamless communication between teachers, parents, and students.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if (Route::has('login') && !auth()->check())
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Get Started
                        </a>
                    @endif
                    <a href="#about" class="border border-blue-600 text-blue-600 dark:text-blue-400 px-8 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors font-semibold">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    About Our Institution
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    We are committed to providing quality education and fostering academic excellence in a supportive learning environment.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="bg-blue-100 dark:bg-blue-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Academic Excellence</h3>
                    <p class="text-gray-600 dark:text-gray-300">Comprehensive curriculum designed to challenge and inspire students to reach their full potential.</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-green-100 dark:bg-green-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Experienced Teachers</h3>
                    <p class="text-gray-600 dark:text-gray-300">Our dedicated faculty brings years of experience and passion for teaching to every classroom.</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-purple-100 dark:bg-purple-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Modern Technology</h3>
                    <p class="text-gray-600 dark:text-gray-300">State-of-the-art facilities and digital tools to enhance the learning experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Teachers Section -->
    <section id="teachers" class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Meet Our Teachers
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Our dedicated educators are committed to nurturing young minds and fostering a love for learning.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $teachers = \App\Models\Teacher::with('user')->take(6)->get();
                @endphp
                
                @forelse($teachers as $teacher)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ substr($teacher->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $teacher->user->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $teacher->specialization ?? 'Teacher' }}</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                {{ $teacher->bio ?? 'Experienced educator dedicated to student success and academic excellence.' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <!-- Placeholder teachers -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">JD</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">John Doe</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Mathematics Teacher</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Experienced mathematics teacher with 10+ years of experience in helping students excel in mathematics.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-green-600 dark:text-green-400">JS</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jane Smith</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Science Teacher</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Passionate science educator dedicated to making complex scientific concepts accessible and engaging.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-purple-600 dark:text-purple-400">MJ</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mike Johnson</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">English Teacher</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Literature enthusiast and English teacher focused on developing strong communication skills in students.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Latest News & Articles
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Stay updated with our latest news, educational insights, and school announcements.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Blog Post 1 -->
                <article class="bg-gray-50 dark:bg-gray-900 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">Academic Excellence</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            New Academic Year Begins with Exciting Programs
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            We're thrilled to announce the start of our new academic year with enhanced programs and innovative teaching methods.
                        </p>
                        <div class="text-sm text-gray-500 dark:text-gray-400">September 15, 2024</div>
                    </div>
                </article>
                
                <!-- Blog Post 2 -->
                <article class="bg-gray-50 dark:bg-gray-900 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-green-600 dark:text-green-400 font-semibold mb-2">Student Life</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Student Achievements and Recognition
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Celebrating our students' outstanding achievements in academics, sports, and extracurricular activities.
                        </p>
                        <div class="text-sm text-gray-500 dark:text-gray-400">September 10, 2024</div>
                    </div>
                </article>
                
                <!-- Blog Post 3 -->
                <article class="bg-gray-50 dark:bg-gray-900 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-purple-600 dark:text-purple-400 font-semibold mb-2">Technology</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Digital Learning Platform Updates
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Our school management system has been updated with new features to enhance communication and learning.
                        </p>
                        <div class="text-sm text-gray-500 dark:text-gray-400">September 5, 2024</div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Get in Touch
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    We'd love to hear from you. Contact us for more information about our programs and enrollment.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Contact Information</h3>
                    @if($organization)
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">{{ $organization->address ?? '123 Education Street, Learning City' }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">{{ $organization->email ?? 'info@school.edu' }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">{{ $organization->phone ?? '+1 (555) 123-4567' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">123 Education Street, Learning City</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">info@school.edu</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">+1 (555) 123-4567</span>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Quick Links</h3>
                    <div class="space-y-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                    → Access Your Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                    → Login to Your Account
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                        → Register as Parent
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <a href="#about" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                            → Learn About Our Programs
                        </a>
                        <a href="#teachers" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                            → Meet Our Teachers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">{{ $organization->name ?? 'Aozora Education' }}</h3>
                    <p class="text-gray-400 mb-4">
                        {{ $organization->description ?? 'Empowering students through innovative education and comprehensive learning experiences.' }}
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#teachers" class="text-gray-400 hover:text-white transition-colors">Our Teachers</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>{{ $organization->address ?? '123 Education Street' }}</p>
                        <p>{{ $organization->email ?? 'info@school.edu' }}</p>
                        <p>{{ $organization->phone ?? '+1 (555) 123-4567' }}</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $organization->name ?? 'Aozora Education' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="fixed bottom-4 right-4 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 p-3 rounded-full shadow-lg hover:bg-gray-700 dark:hover:bg-gray-300 transition-colors">
        <svg id="sunIcon" class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
        </svg>
        <svg id="moonIcon" class="w-5 h-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </button>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }

        darkModeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
