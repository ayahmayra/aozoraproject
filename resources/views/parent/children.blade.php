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
                        <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Add Child
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

            <div class="space-y-6">
                @if($children->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($children as $child)
                            <flux:card>
                                <div class="p-6">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <flux:icon.academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">{{ $child->user->name }}</flux:heading>
                                            <flux:text class="text-sm text-gray-500 dark:text-gray-400">Student ID: {{ $child->student_id }}</flux:text>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.envelope class="w-4 h-4 text-gray-400" />
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $child->user->email }}</flux:text>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.calendar class="w-4 h-4 text-gray-400" />
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $child->date_of_birth->format('M d, Y') }}</flux:text>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.user class="w-4 h-4 text-gray-400" />
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ ucfirst($child->gender) }}</flux:text>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.academic-cap class="w-4 h-4 text-gray-400" />
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-300">Class: {{ $child->class }}</flux:text>
                                        </div>
                                        @if($child->phone)
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.phone class="w-4 h-4 text-gray-400" />
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ $child->phone }}</flux:text>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" href="{{ route('parent.children.edit', $child) }}">
                                            <flux:icon.pencil class="h-4 w-4 mr-1" />
                                            Edit
                                        </flux:button>
                                        <form method="POST" action="{{ route('parent.children.destroy', $child) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button variant="ghost" size="sm" type="submit">
                                                <flux:icon.trash class="h-4 w-4 mr-1" />
                                                Delete
                                            </flux:button>
                                        </form>
                                    </div>
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                @else
                    <flux:card>
                        <div class="p-12 text-center">
                            <flux:icon.academic-cap class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                            <flux:heading size="lg" class="text-gray-500 mb-2">No children registered yet</flux:heading>
                            <flux:text class="text-gray-400 mb-6">Add your first child to get started</flux:text>
                            <flux:button variant="primary" href="{{ route('parent.children.create') }}">
                                <flux:icon.plus class="h-4 w-4 mr-2" />
                                Add Your First Child
                            </flux:button>
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
