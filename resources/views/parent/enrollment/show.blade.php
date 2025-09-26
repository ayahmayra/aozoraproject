<x-layouts.app :title="__('Enrollment Details')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Enrollment Details</flux:heading>
                        <flux:text class="mt-2">{{ $student->user->name }} - {{ $subject->name }}</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Dashboard
                    </flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Enrollment Information -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Enrollment Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Subject</flux:text>
                                <flux:text class="text-sm font-medium">{{ $subject->name }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Subject Code</flux:text>
                                <flux:text class="text-sm font-medium">{{ $subject->code }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Enrollment Date</flux:text>
                                <flux:text class="text-sm font-medium">{{ $enrollment->pivot->enrollment_date ? $enrollment->pivot->enrollment_date->format('M j, Y') : 'Not set' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Status</flux:text>
                                <flux:badge variant="{{ $enrollment->pivot->enrollment_status === 'active' ? 'green' : ($enrollment->pivot->enrollment_status === 'pending' ? 'yellow' : 'red') }}" size="sm">
                                    {{ ucfirst($enrollment->pivot->enrollment_status) }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Payment Information -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Payment Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Payment Method</flux:text>
                                <flux:text class="text-sm font-medium">{{ ucfirst($enrollment->pivot->payment_method) }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Amount</flux:text>
                                <flux:text class="text-sm font-medium">
                                    {{ $enrollment->pivot->payment_amount ? 'Rp ' . number_format($enrollment->pivot->payment_amount, 0, ',', '.') : 'To be determined by admin' }}
                                </flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Payment Status</flux:text>
                                <flux:badge variant="{{ $enrollment->pivot->payment_status === 'paid' ? 'green' : ($enrollment->pivot->payment_status === 'pending' ? 'yellow' : 'red') }}" size="sm">
                                    {{ ucfirst($enrollment->pivot->payment_status) }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Schedule Information -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Schedule Information</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">Start Date</flux:text>
                                <flux:text class="text-sm font-medium">{{ $enrollment->pivot->start_date ? $enrollment->pivot->start_date->format('M j, Y') : 'To be determined by admin' }}</flux:text>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text class="text-sm text-gray-600">End Date</flux:text>
                                <flux:text class="text-sm font-medium">{{ $enrollment->pivot->end_date ? $enrollment->pivot->end_date->format('M j, Y') : 'To be determined by admin' }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Actions -->
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Actions</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($enrollment->pivot->enrollment_status === 'pending')
                                <flux:button variant="primary" href="#" class="w-full">
                                    <flux:icon.check class="w-4 h-4 mr-2" />
                                    Approve Enrollment
                                </flux:button>
                            @endif
                            
                            @if($enrollment->pivot->enrollment_status === 'active')
                                <flux:button variant="ghost" href="#" class="w-full">
                                    <flux:icon.pencil class="w-4 h-4 mr-2" />
                                    Update Enrollment
                                </flux:button>
                            @endif
                            
                            @if(in_array($enrollment->pivot->enrollment_status, ['pending', 'active']))
                                <form action="{{ route('parent.enrollment.destroy', [$student, $subject]) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button variant="danger" type="submit" class="w-full">
                                        <flux:icon.x-mark class="w-4 h-4 mr-2" />
                                        Cancel Enrollment
                                    </flux:button>
                                </form>
                            @endif
                        </div>
                    </div>
                </flux:card>
            </div>

            @if($enrollment->pivot->notes)
                <flux:card class="mt-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Notes</flux:heading>
                    </div>
                    <div class="p-6">
                        <flux:text>{{ $enrollment->pivot->notes }}</flux:text>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>
</x-layouts.app>
