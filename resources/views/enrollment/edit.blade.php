<x-layouts.app :title="__('Edit Enrollment')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('enrollment.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Enrollments
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Edit Enrollment</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Update enrollment details for {{ $student->user->name }} in {{ $subject->name }}</flux:text>
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

    <div class="">
        <flux:card>
            <div class="p-6">
                <form method="POST" action="{{ route('enrollment.update', [$student, $subject]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Enrollment Info -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <flux:heading size="md" class="mb-2">Enrollment Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm text-gray-500">Student</flux:text>
                                <flux:text class="font-medium">{{ $student->user->name }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-gray-500">Subject</flux:text>
                                <flux:text class="font-medium">{{ $subject->name }} ({{ $subject->code }})</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-gray-500">Enrollment Number</flux:text>
                                <flux:text class="font-medium font-mono">{{ $enrollment->pivot->enrollment_number ?? 'Not assigned' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-gray-500">Enrollment Date</flux:text>
                                <flux:text class="font-medium">
                                    @if($enrollment->pivot->enrollment_date)
                                        @if(is_string($enrollment->pivot->enrollment_date))
                                            {{ \Carbon\Carbon::parse($enrollment->pivot->enrollment_date)->format('M d, Y') }}
                                        @else
                                            {{ $enrollment->pivot->enrollment_date->format('M d, Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </flux:text>
                            </div>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <flux:field>
                        <flux:label>Enrollment Status</flux:label>
                        <flux:select name="status" required>
                            <option value="pending" {{ old('status', $enrollment->pivot->enrollment_status) === 'pending' ? 'selected' : '' }}>
                                Pending - Waiting for admin approval
                            </option>
                            <option value="active" {{ old('status', $enrollment->pivot->enrollment_status) === 'active' ? 'selected' : '' }}>
                                Active - Enrollment approved and active
                            </option>
                            <option value="cancelled" {{ old('status', $enrollment->pivot->enrollment_status) === 'cancelled' ? 'selected' : '' }}>
                                Cancelled - Enrollment cancelled
                            </option>
                        </flux:select>
                    </flux:field>


                    <!-- Payment Method -->
                    <flux:field>
                        <flux:label>Payment Method</flux:label>
                        <flux:select name="payment_method">
                            <option value="">Select payment method</option>
                            <option value="monthly" {{ old('payment_method', $enrollment->pivot->payment_method) === 'monthly' ? 'selected' : '' }}>
                                Monthly Payment
                            </option>
                            <option value="semester" {{ old('payment_method', $enrollment->pivot->payment_method) === 'semester' ? 'selected' : '' }}>
                                Semester Payment
                            </option>
                            <option value="yearly" {{ old('payment_method', $enrollment->pivot->payment_method) === 'yearly' ? 'selected' : '' }}>
                                Yearly Payment
                            </option>
                        </flux:select>
                    </flux:field>

                    <!-- Payment Amount -->
                    <flux:field>
                        <flux:label>Payment Amount</flux:label>
                        <flux:input
                            name="payment_amount"
                            type="number"
                            step="0.01"
                            placeholder="Enter payment amount"
                            value="{{ old('payment_amount', $enrollment->pivot->payment_amount) }}"
                        />
                    </flux:field>

                    <!-- Start Date -->
                    <flux:field>
                        <flux:label>Start Date <span class="text-red-500">*</span></flux:label>
                        <flux:input
                            name="start_date"
                            type="date"
                            required
                            value="{{ old('start_date', $enrollment->pivot->start_date ? \Carbon\Carbon::parse($enrollment->pivot->start_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        />
                        <flux:description>Start date is required for invoice generation</flux:description>
                    </flux:field>

                    <!-- End Date -->
                    <flux:field>
                        <flux:label>End Date</flux:label>
                        <flux:input
                            name="end_date"
                            type="date"
                            value="{{ old('end_date', $enrollment->pivot->end_date ? \Carbon\Carbon::parse($enrollment->pivot->end_date)->format('Y-m-d') : now()->addYear()->format('Y-m-d')) }}"
                        />
                        <flux:description>End date defaults to 1 year from start date</flux:description>
                    </flux:field>

                    <!-- Notes -->
                    <flux:field>
                        <flux:label>Notes</flux:label>
                        <flux:textarea
                            name="notes"
                            placeholder="Add any notes about this enrollment..."
                            rows="3"
                        >{{ old('notes', $enrollment->pivot->notes) }}</flux:textarea>
                    </flux:field>

                    <div class="flex justify-end space-x-3">
                        <flux:button variant="ghost" href="{{ route('enrollment.index') }}">
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            <flux:icon.check class="h-4 w-4 mr-2" />
                            Update Enrollment
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
