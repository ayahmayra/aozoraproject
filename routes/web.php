<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');


// Role-based dashboard redirect
Route::get('dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'role.redirect'])->name('dashboard');

// General dashboard redirect with role-based verification
Route::get('dashboard-redirect', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified', 'role.redirect'])->name('dashboard.redirect');

// Parent Dashboard
Route::get('parent/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:parent', 'user.verified'])
    ->name('parent.dashboard');

// Parent Routes
Route::middleware(['auth', 'verified', 'role:parent', 'user.verified'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Parent\ProfileController::class, 'index'])->name('profile');
    
    Route::get('/children/create', [\App\Http\Controllers\Parent\ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [\App\Http\Controllers\Parent\ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{student}/edit', [\App\Http\Controllers\Parent\ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{student}', [\App\Http\Controllers\Parent\ChildController::class, 'update'])->name('children.update');
    Route::delete('/children/{student}', [\App\Http\Controllers\Parent\ChildController::class, 'destroy'])->name('children.destroy');
    
    // Parent student management routes
    Route::get('/students/{student}/edit', [\App\Http\Controllers\Parent\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [\App\Http\Controllers\Parent\StudentController::class, 'update'])->name('students.update');
    
    // Enrollment routes
    Route::get('/enrollment/{student}/create', [\App\Http\Controllers\Parent\EnrollmentController::class, 'create'])->name('enrollment.create');
    Route::post('/enrollment/{student}', [\App\Http\Controllers\Parent\EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::get('/enrollment/{student}/{subject}', [\App\Http\Controllers\Parent\EnrollmentController::class, 'show'])->name('enrollment.show');
    Route::put('/enrollment/{student}/{subject}', [\App\Http\Controllers\Parent\EnrollmentController::class, 'update'])->name('enrollment.update');
    Route::delete('/enrollment/{student}/{subject}', [\App\Http\Controllers\Parent\EnrollmentController::class, 'destroy'])->name('enrollment.destroy');
    
    Route::get('/schedule', function () {
        return view('parent.schedule');
    })->name('schedule');
    
    Route::get('/grades', function () {
        return view('parent.grades');
    })->name('grades');
});

// Teacher Dashboard  
Route::get('teacher/dashboard', \App\Livewire\Teacher\Dashboard::class)
    ->middleware(['auth', 'verified', 'role:teacher', 'user.verified'])
    ->name('teacher.dashboard');

// Student Dashboard
Route::get('student/dashboard', \App\Livewire\Student\Dashboard::class)
    ->middleware(['auth', 'verified', 'role:student', 'user.verified'])
    ->name('student.dashboard');

// Student Routes
Route::middleware(['auth', 'verified', 'role:student', 'user.verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
    // Student cannot edit their own profile - only parent can edit
});

// Student Profile Routes for Parent and Admin
Route::middleware(['auth', 'verified', 'user.verified'])->group(function () {
    Route::get('/student/profile/view', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile.view');
    Route::get('/student/profile/edit-form', [\App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit.view');
    Route::put('/student/profile/update', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update.view');
});

// Registration Route
Route::get('register', \App\Livewire\Auth\Register::class)
    ->middleware('guest')
    ->name('register');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    
    // Verification pending page
    Route::get('/verification-pending', function () {
        return view('verification-pending');
    })->name('verification.pending');

    // Universal Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Enrollment Routes
    Route::get('/enrollment', [\App\Http\Controllers\EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::get('/enrollment/{student}/create', [\App\Http\Controllers\EnrollmentController::class, 'create'])->name('enrollment.create');
    Route::post('/enrollment/{student}', [\App\Http\Controllers\EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::get('/enrollment/{student}', [\App\Http\Controllers\EnrollmentController::class, 'show'])->name('enrollment.show');
    Route::get('/enrollment/{student}/{subject}/edit', [\App\Http\Controllers\EnrollmentController::class, 'edit'])->name('enrollment.edit');
    Route::put('/enrollment/{student}/{subject}', [\App\Http\Controllers\EnrollmentController::class, 'update'])->name('enrollment.update');
    Route::delete('/enrollment/{student}/{subject}', [\App\Http\Controllers\EnrollmentController::class, 'destroy'])->name('enrollment.destroy');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Admin Routes - Data Master Management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');
        Route::post('/users/{user}/deactivate', [\App\Http\Controllers\Admin\UserController::class, 'deactivate'])->name('users.deactivate');
    
    // Student Management
    Route::get('/students', function () {
        return view('admin.students.index');
    })->name('students');
    
    // Teacher Management
    Route::get('/teachers', [\App\Http\Controllers\Admin\TeachersController::class, 'index'])->name('teachers');
    Route::get('/teachers/create', [\App\Http\Controllers\Admin\TeachersController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [\App\Http\Controllers\Admin\TeachersController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit', [\App\Http\Controllers\Admin\TeachersController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [\App\Http\Controllers\Admin\TeachersController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [\App\Http\Controllers\Admin\TeachersController::class, 'destroy'])->name('teachers.destroy');
    
    // Parent Management
    Route::get('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'index'])->name('parents');
    Route::get('/parents/create', [\App\Http\Controllers\Admin\ParentController::class, 'create'])->name('parents.create');
    Route::post('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'store'])->name('parents.store');
    Route::get('/parents/{parent}/edit', [\App\Http\Controllers\Admin\ParentController::class, 'edit'])->name('parents.edit');
    Route::put('/parents/{parent}', [\App\Http\Controllers\Admin\ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{parent}', [\App\Http\Controllers\Admin\ParentController::class, 'destroy'])->name('parents.destroy');
    Route::post('/parents/{parent}/verify', [\App\Http\Controllers\Admin\ParentController::class, 'verify'])->name('parents.verify');
    Route::post('/parents/{parent}/deactivate', [\App\Http\Controllers\Admin\ParentController::class, 'deactivate'])->name('parents.deactivate');

    // Student Management
    Route::get('/students', [\App\Http\Controllers\Admin\StudentsController::class, 'index'])->name('students');
    Route::get('/students/create', [\App\Http\Controllers\Admin\StudentsController::class, 'create'])->name('students.create');
    Route::post('/students', [\App\Http\Controllers\Admin\StudentsController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [\App\Http\Controllers\Admin\StudentsController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [\App\Http\Controllers\Admin\StudentsController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [\App\Http\Controllers\Admin\StudentsController::class, 'destroy'])->name('students.destroy');
    
    // Subject Management
    Route::get('/subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('subjects');
    Route::get('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('subjects.show');
    Route::get('/subjects/create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('subjects.destroy');
    
    // Time Schedule Management
    Route::resource('time-schedules', \App\Http\Controllers\Admin\TimeScheduleController::class)->parameters(['time-schedules' => 'timeSchedule']);
    Route::get('/time-schedules-calendar-fullcalendar', [\App\Http\Controllers\Admin\TimeScheduleController::class, 'calendarFullCalendar'])->name('time-schedules.calendar-fullcalendar');
    
    // API for calendar data
    Route::get('/api/time-schedules', [\App\Http\Controllers\Admin\TimeScheduleController::class, 'apiData'])->name('api.time-schedules');
    
    // Roles Management
    Route::get('/roles', function () {
        return view('admin.roles.index');
    })->name('roles');
    
        // Organization Management
        Route::get('/organization', [\App\Http\Controllers\Admin\OrganizationController::class, 'index'])->name('organization');
        Route::get('/organization/edit', [\App\Http\Controllers\Admin\OrganizationController::class, 'edit'])->name('organization.edit');
        Route::put('/organization/update', [\App\Http\Controllers\Admin\OrganizationController::class, 'update'])->name('organization.update');
    
    // Document Numbering Configuration
    Route::resource('document-numbering', \App\Http\Controllers\Admin\DocumentNumberingConfigController::class)->parameters(['document-numbering' => 'documentNumberingConfig']);
    Route::post('/document-numbering/{documentNumberingConfig}/toggle-status', [\App\Http\Controllers\Admin\DocumentNumberingConfigController::class, 'toggleStatus'])->name('document-numbering.toggle-status');
    Route::post('/document-numbering/{documentNumberingConfig}/reset-number', [\App\Http\Controllers\Admin\DocumentNumberingConfigController::class, 'resetNumber'])->name('document-numbering.reset-number');
});

// Academic Routes - Accessible by Teacher, Parent, and Student
Route::middleware(['auth', 'role:teacher,parent,student'])->prefix('academic')->name('academic.')->group(function () {
    // Subjects (read-only for non-admin users)
    Route::get('/subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('subjects');
    Route::get('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('subjects.show');
    
    // Schedule Calendar (read-only for non-admin users)
    Route::get('/schedule-calendar', [\App\Http\Controllers\Admin\TimeScheduleController::class, 'calendarFullCalendar'])->name('schedule-calendar');
});

require __DIR__.'/auth.php';
