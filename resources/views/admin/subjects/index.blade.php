<x-layouts.app :title="__('Subjects Management')">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Subjects Management</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Manage subjects and their assignments</flux:text>
            </div>
            <flux:button variant="primary" href="{{ route('admin.subjects.create') }}">
                <flux:icon.plus class="h-4 w-4 mr-2" />
                Add Subject
            </flux:button>
        </div>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Subjects</flux:heading>
                <flux:text class="text-sm text-gray-500">{{ $subjects->total() }} subjects found</flux:text>
            </div>
        </div>
        
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Teachers</flux:table.column>
                <flux:table.column>Students</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse($subjects as $subject)
                    <flux:table.row>
                        <flux:table.cell>
                            <div>
                                <div class="font-medium">{{ $subject->name }}</div>
                                @if($subject->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 50) }}</div>
                                @endif
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <flux:badge variant="blue" size="sm">
                                {{ $subject->code }}
                            </flux:badge>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @forelse($subject->teachers as $teacher)
                                    <flux:badge size="sm" color="green">
                                        {{ $teacher->user->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-gray-400 text-sm">No teachers assigned</span>
                                @endforelse
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="text-sm">
                                <span class="font-medium">{{ $subject->students_count }}</span>
                                <span class="text-gray-500">active students</span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.subjects.show', $subject) }}" title="View Subject">
                                    <flux:icon.eye class="h-4 w-4" />
                                </flux:button>
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.subjects.edit', $subject) }}" title="Edit Subject">
                                    <flux:icon.pencil class="h-4 w-4" />
                                </flux:button>
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.subjects.destroy', $subject) }}" 
                                    onclick="return confirm('Are you sure you want to delete this subject?')" 
                                    title="Delete Subject">
                                    <flux:icon.trash class="h-4 w-4" />
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No subjects found</p>
                            <p class="text-sm text-gray-400">Subjects will appear here once they are created</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        
        @if($subjects->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subjects->links() }}
            </div>
        @endif
    </flux:card>
</x-layouts.app>