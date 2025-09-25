<x-layouts.app :title="__('My Children')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">My Children</flux:heading>
                        <flux:text class="mt-2">Manage your children's information</flux:text>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                            <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                            Back to Dashboard
                        </flux:button>
                        
                    </div>
                </div>
            </div>

            @if (session()->has('success'))
                <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
            @endif

            @if (session()->has('error'))
                <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
            @endif

            <!-- Children Table -->
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">My Children ({{ $children->count() }})</flux:heading>
                        <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Add Child
                        </flux:button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Student</flux:table.column>
                            <flux:table.column>Student ID</flux:table.column>
                            <flux:table.column>Date of Birth</flux:table.column>
                            <flux:table.column>Gender</flux:table.column>
                            <flux:table.column>Phone</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($children as $child)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <flux:avatar name="{{ $child->user->name }}" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium">{{ $child->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $child->user->email }}</div>
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($child->student_id)
                                            <flux:badge variant="primary">{{ $child->student_id }}</flux:badge>
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $child->date_of_birth->format('M d, Y') }}</flux:table.cell>
                                    <flux:table.cell>{{ ucfirst($child->gender) }}</flux:table.cell>
                                    <flux:table.cell>{{ $child->phone ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>
                                        @if($child->user->status === 'active')
                                            <flux:badge variant="success">Active</flux:badge>
                                        @elseif($child->user->status === 'pending')
                                            <flux:badge variant="blue">Pending</flux:badge>
                                        @else
                                            <flux:badge variant="danger">Inactive</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex space-x-2">
                                            <flux:button variant="ghost" size="sm" href="{{ route('student.profile.view') }}?student_id={{ $child->id }}" title="View Profile">
                                                <flux:icon.user class="h-4 w-4" />
                                            </flux:button>
                                            <flux:button variant="ghost" size="sm" href="{{ route('parent.children.edit', $child) }}" title="Edit Child">
                                                <flux:icon.pencil class="h-4 w-4" />
                                            </flux:button>
                                            <form method="POST" action="{{ route('parent.children.destroy', $child) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this child?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button variant="ghost" size="sm" type="submit" title="Delete Child">
                                                    <flux:icon.trash class="h-4 w-4" />
                                                </flux:button>
                                            </form>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="7" class="text-center py-8">
                                        <div class="text-gray-500">
                                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                            <p class="text-lg font-medium">No children registered yet</p>
                                            <p class="text-sm mb-4">Add your first child to get started</p>
                                            <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                                                <flux:icon.plus class="h-4 w-4 mr-2" />
                                                Add Your First Child
                                            </flux:button>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
