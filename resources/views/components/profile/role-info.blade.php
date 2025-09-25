@props(['user'])

@if($user->hasRole('teacher') && $user->teacherProfile)
    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <flux:field>
                    <flux:label>Employee Number</flux:label>
                    <flux:input value="{{ $user->teacherProfile->employee_number ?? 'Not assigned' }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Education Level</flux:label>
                    <flux:input value="{{ $user->teacherProfile->education_level ?? 'Not specified' }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Institution</flux:label>
                    <flux:input value="{{ $user->teacherProfile->institution ?? 'Not specified' }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Employment Status</flux:label>
                    <flux:input value="{{ ucfirst($user->teacherProfile->employment_status ?? 'Not specified') }}" readonly />
                </flux:field>
            </div>
        </div>
    </div>
@elseif($user->hasRole('student') && $user->studentProfile)
    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <flux:field>
                    <flux:label>Student ID</flux:label>
                    <flux:input value="{{ $user->studentProfile->student_id ?? 'Not assigned' }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Date of Birth</flux:label>
                    <flux:input value="{{ $user->studentProfile->date_of_birth ? $user->studentProfile->date_of_birth->format('M d, Y') : 'Not specified' }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Gender</flux:label>
                    <flux:input value="{{ ucfirst($user->studentProfile->gender ?? 'Not specified') }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Parent</flux:label>
                    <flux:input value="{{ $user->studentProfile->parent ? $user->studentProfile->parent->name : 'Not assigned' }}" readonly />
                </flux:field>
            </div>
        </div>
    </div>
@elseif($user->hasRole('parent') && $user->parentProfile)
    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <flux:field>
                    <flux:label>Children Count</flux:label>
                    <flux:input value="{{ $user->children->count() }} children" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input value="{{ $user->parentProfile->phone ?? 'Not provided' }}" readonly />
                </flux:field>
            </div>
        </div>
    </div>
@endif
