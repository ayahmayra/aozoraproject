<x-layouts.app :title="__('Enroll Student')">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="xl">Enroll Student</flux:heading>
                        <flux:text class="mt-2">Enroll {{ $student->user->name }} in a subject</flux:text>
                    </div>
                    <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Dashboard
                    </flux:button>
                </div>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Enrollment Information</flux:heading>
                </div>
                <div class="p-6">
                    <form action="{{ route('parent.enrollment.store', $student) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Student Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Student Information</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Student Name</flux:text>
                                    <flux:text class="text-lg">{{ $student->user->name }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-sm font-medium text-gray-500 mb-1">Student ID</flux:text>
                                    <flux:text class="text-lg">{{ $student->student_id ?? 'Not assigned' }}</flux:text>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Selection -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Subject Selection</flux:heading>
                            <flux:field>
                                <flux:label>Select Subject</flux:label>
                                <flux:select name="subject_id" required>
                                    <option value="">Choose a subject</option>
                                    @foreach($availableSubjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }} ({{ $subject->code }})
                                        </option>
                                    @endforeach
                                </flux:select>
                                @error('subject_id')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                            </flux:field>
                        </div>

                        <!-- Payment Information -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Payment Information</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                <flux:field>
                                    <flux:label>Payment Method</flux:label>
                                    <flux:select name="payment_method" required>
                                        <option value="">Select payment method</option>
                                        <option value="monthly" {{ old('payment_method') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="semester" {{ old('payment_method') == 'semester' ? 'selected' : '' }}>Semester</option>
                                        <option value="yearly" {{ old('payment_method') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </flux:select>
                                    @error('payment_method')
                                        <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                            </div>
                            <flux:text class="text-sm text-gray-500 mt-2">
                                Payment amount and schedule will be determined by admin after enrollment approval.
                            </flux:text>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-8">
                            <flux:heading size="md" class="mb-4">Additional Information</flux:heading>
                            <flux:field>
                                <flux:label>Notes (Optional)</flux:label>
                                <flux:textarea
                                    name="notes"
                                    placeholder="Enter any additional notes or special requirements"
                                    rows="4"
                                >{{ old('notes') }}</flux:textarea>
                                @error('notes')
                                    <flux:text class="text-red-600 text-sm mt-1">{{ $message }}</flux:text>
                                @enderror
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <flux:button variant="ghost" href="{{ route('parent.dashboard') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                Enroll Student
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
