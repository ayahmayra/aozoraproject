<x-layouts.app :title="__('Admin Teacher Management')">
    <flux:heading size="xl" level="1">Admin Teacher Management</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">Manage all teachers in the system</flux:text>
    
    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <div class="space-y-6">
        <!-- Filters -->
        <flux:card>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.teachers') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <flux:field>
                            <flux:label>Search Teachers</flux:label>
                            <flux:input 
                                name="search" 
                                placeholder="Search by name or email..." 
                                value="{{ request('search') }}"
                            />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Filter by Status</flux:label>
                            <flux:select name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Filter by Employment</flux:label>
                            <flux:select name="employment_status">
                                <option value="">All Employment</option>
                                <option value="Full-time" {{ request('employment_status') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="Part-time" {{ request('employment_status') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Contract" {{ request('employment_status') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                <option value="Intern" {{ request('employment_status') == 'Intern' ? 'selected' : '' }}>Intern</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div class="flex items-end space-x-2">
                        <flux:button variant="outline" type="submit" class="flex-1">
                            <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                            Search
                        </flux:button>
                        <flux:button variant="ghost" href="{{ route('admin.teachers') }}" class="flex-1">
                            <flux:icon.x-mark class="h-4 w-4 mr-2" />
                            Clear
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>

        <!-- Teachers Table -->
        <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">Teachers ({{ $teachers->total() }})</flux:heading>
                <flux:button variant="primary" href="{{ route('admin.teachers.create') }}">
                    <flux:icon.plus class="h-4 w-4 mr-2" />
                    Add Teacher
                </flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Teacher</flux:table.column>
                    <flux:table.column>Education</flux:table.column>
                    <flux:table.column>Employee Number</flux:table.column>
                    <flux:table.column>Employment Status</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($teachers as $teacher)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <flux:avatar name="{{ $teacher->name }}" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium">{{ $teacher->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($teacher->teacherProfile && $teacher->teacherProfile->education_level)
                                    <div class="text-sm">{{ $teacher->teacherProfile->education_level }}</div>
                                    @if($teacher->teacherProfile->institution)
                                        <div class="text-sm text-gray-500">{{ $teacher->teacherProfile->institution }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($teacher->teacherProfile && $teacher->teacherProfile->employee_number)
                                    <flux:badge variant="primary">{{ $teacher->teacherProfile->employee_number }}</flux:badge>
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($teacher->teacherProfile && $teacher->teacherProfile->employment_status)
                                    <flux:badge variant="primary">{{ $teacher->teacherProfile->employment_status }}</flux:badge>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $teacher->created_at->format('M d, Y') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex space-x-2">
                                    <flux:button variant="ghost" size="sm" href="{{ route('admin.teachers.edit', $teacher) }}" title="Edit Teacher">
                                        <flux:icon.pencil class="h-4 w-4" />
                                    </flux:button>
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button variant="ghost" size="sm" type="submit" title="Delete Teacher">
                                            <flux:icon.trash class="h-4 w-4" />
                                        </flux:button>
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8">
                                <div class="text-gray-500">
                                    <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p class="text-lg font-medium">No teachers found</p>
                                    <p class="text-sm">Get started by adding your first teacher</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        @if($teachers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </flux:card>
</x-layouts.app>