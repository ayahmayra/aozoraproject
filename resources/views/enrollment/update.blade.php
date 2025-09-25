<x-layouts.app :title="__('Update Enrollment Status')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('enrollment.show', $student) }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Enrollments
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Update Enrollment Status</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Update enrollment status for {{ $student->user->name }} in {{ $subject->name }}</flux:text>
    </div>

    @if ($errors->any())
        <flux:callout class="mb-6" variant="danger" icon="x-mark" heading="Please correct the following errors:">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:callout>
    @endif

    <div class="max-w-2xl">
        <flux:card>
            <div class="p-6">
                <form method="POST" action="{{ route('enrollment.update', [$student, $subject]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Status Display -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <flux:heading size="md" class="mb-2">Current Enrollment</flux:heading>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="font-medium">{{ $subject->name }}</flux:text>
                                <flux:text class="text-sm text-gray-500">{{ $subject->code }}</flux:text>
                            </div>
                            <flux:badge 
                                size="sm" 
                                variant="{{ $subject->pivot->status === 'enrolled' ? 'blue' : ($subject->pivot->status === 'completed' ? 'green' : 'red') }}"
                            >
                                {{ ucfirst($subject->pivot->status) }}
                            </flux:badge>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Enrolled: 
                            @if($subject->pivot->enrolled_at)
                                @if(is_string($subject->pivot->enrolled_at))
                                    {{ \Carbon\Carbon::parse($subject->pivot->enrolled_at)->format('M d, Y') }}
                                @else
                                    {{ $subject->pivot->enrolled_at->format('M d, Y') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <flux:field>
                        <flux:label>New Status</flux:label>
                        <flux:select name="status" required>
                            <option value="enrolled" {{ old('status', $subject->pivot->status) === 'enrolled' ? 'selected' : '' }}>
                                Enrolled - Student is currently taking this subject
                            </option>
                            <option value="completed" {{ old('status', $subject->pivot->status) === 'completed' ? 'selected' : '' }}>
                                Completed - Student has finished this subject
                            </option>
                            <option value="dropped" {{ old('status', $subject->pivot->status) === 'dropped' ? 'selected' : '' }}>
                                Dropped - Student has withdrawn from this subject
                            </option>
                        </flux:select>
                    </flux:field>

                    <!-- Notes -->
                    <flux:field>
                        <flux:label>Notes (Optional)</flux:label>
                        <flux:textarea
                            name="notes"
                            placeholder="Add any notes about this enrollment status change..."
                            rows="3"
                        >{{ old('notes', $subject->pivot->notes) }}</flux:textarea>
                    </flux:field>

                    <div class="flex justify-end space-x-3">
                        <flux:button variant="ghost" href="{{ route('enrollment.show', $student) }}">
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            <flux:icon.check class="h-4 w-4 mr-2" />
                            Update Status
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
