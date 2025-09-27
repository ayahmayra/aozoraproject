<x-layouts.app :title="__('Generate Invoices')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Generate Invoices</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Manually generate invoices for enrollments</flux:text>
            </div>
            <flux:button variant="outline" href="{{ route('admin.invoices') }}">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Invoices
            </flux:button>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
        @endif

        @if (session()->has('error'))
            <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
        @endif

        <!-- Generate Form -->
        <flux:card>
            <div class="p-6">
                <flux:heading size="lg" class="mb-4">Invoice Generation Settings</flux:heading>
                
                <form method="POST" action="{{ route('admin.invoices.generate.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Period Selection -->
                        <flux:field>
                            <flux:label>Start Month</flux:label>
                            <flux:select name="start_month" required>
                                <option value="">Select start month</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('start_month', now()->month) == $i ? 'selected' : '' }}>
                                        {{ \DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </flux:select>
                            <flux:description>Starting month for billing period</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>End Month</flux:label>
                            <flux:select name="end_month" required>
                                <option value="">Select end month</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('end_month', now()->month) == $i ? 'selected' : '' }}>
                                        {{ \DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </flux:select>
                            <flux:description>Ending month for billing period</flux:description>
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Year Selection -->
                        <flux:field>
                            <flux:label>Year</flux:label>
                            <flux:select name="year" required>
                                @for($year = now()->year - 1; $year <= now()->year + 2; $year++)
                                    <option value="{{ $year }}" {{ old('year', now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </flux:select>
                            <flux:description>Year for billing period</flux:description>
                        </flux:field>

                        <!-- Generation Mode -->
                        <flux:field>
                            <flux:label>Generation Mode</flux:label>
                            <flux:select name="generation_mode">
                                <option value="monthly">Generate monthly invoices</option>
                                <option value="semester">Generate semester invoices</option>
                                <option value="yearly">Generate yearly invoices</option>
                            </flux:select>
                            <flux:description>How to generate invoices within the period</flux:description>
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Payment Method Filter -->
                        <flux:field>
                            <flux:label>Payment Method</flux:label>
                            <flux:select name="payment_method">
                                <option value="">All Payment Methods</option>
                                <option value="monthly">Monthly</option>
                                <option value="semester">Semester</option>
                                <option value="yearly">Yearly</option>
                            </flux:select>
                            <flux:description>Filter by payment method (optional)</flux:description>
                        </flux:field>

                        <!-- Preview -->
                        <flux:field>
                            <flux:label>Period Preview</flux:label>
                            <div id="period-preview" class="p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                                Select start and end months to see preview
                            </div>
                            <flux:description>Preview of selected billing period</flux:description>
                        </flux:field>
                    </div>

                    <!-- Enrollment Selection -->
                    <flux:field>
                        <flux:label>Target Enrollments</flux:label>
                        <flux:description class="mb-4">Select specific enrollments to generate invoices for, or leave empty to generate for all active enrollments.</flux:description>
                        
                        <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
                            @forelse($enrollments as $enrollment)
                                <label class="flex items-center space-x-3 py-2 hover:bg-gray-50 rounded px-2">
                                    <flux:input 
                                        type="checkbox" 
                                        name="enrollment_ids[]" 
                                        value="{{ $enrollment->id }}"
                                        class="rounded"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium">{{ $enrollment->student->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $enrollment->student->user->email }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium">{{ $enrollment->subject->name }}</div>
                                                <div class="text-xs text-gray-500">{{ ucfirst($enrollment->payment_method) }} - Rp {{ number_format($enrollment->payment_amount, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <flux:icon.user-group class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p>No active enrollments found</p>
                                </div>
                            @endforelse
                        </div>
                    </flux:field>

                    <!-- Generate Button -->
                    <div class="flex justify-end">
                        <flux:button variant="primary" type="submit">
                            <flux:icon.document-plus class="h-4 w-4 mr-2" />
                            Generate Invoices
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>

        <!-- Information Card -->
        <flux:card>
            <div class="p-6">
                <flux:heading size="lg" class="mb-4">Generation Information</flux:heading>
                
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <flux:icon.user class="h-5 w-5 text-blue-500 mt-0.5" />
                            <div>
                                <div class="text-sm font-medium">Manual Generation</div>
                                <div class="text-xs text-gray-600">Invoices are generated manually by admin with custom period selection.</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <flux:icon.shield-check class="h-5 w-5 text-green-500 mt-0.5" />
                            <div>
                                <div class="text-sm font-medium">Duplicate Prevention</div>
                                <div class="text-xs text-gray-600">The system will not generate duplicate invoices for the same period.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <flux:icon.calendar class="h-5 w-5 text-purple-500 mt-0.5" />
                            <div>
                                <div class="text-sm font-medium">Due Dates</div>
                                <div class="text-xs text-gray-600">Monthly: 7 days, Semester: 14 days, Yearly: 30 days</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <flux:icon.clock class="h-5 w-5 text-orange-500 mt-0.5" />
                            <div>
                                <div class="text-sm font-medium">Flexible Periods</div>
                                <div class="text-xs text-gray-600">Generate invoices for any custom period range.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllBtn = document.getElementById('selectAll');
            const enrollmentCheckboxes = document.querySelectorAll('input[name="enrollment_ids[]"]');
            
            // Select all checkbox functionality
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    const isChecked = this.checked;
                    enrollmentCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });
            }

            // Period preview functionality
            const startMonthSelect = document.querySelector('select[name="start_month"]');
            const endMonthSelect = document.querySelector('select[name="end_month"]');
            const yearSelect = document.querySelector('select[name="year"]');
            const previewDiv = document.getElementById('period-preview');

            function updatePreview() {
                const startMonth = parseInt(startMonthSelect.value);
                const endMonth = parseInt(endMonthSelect.value);
                const year = yearSelect.value;

                if (startMonth && endMonth && year) {
                    const monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];

                    let previewText = '';
                    if (startMonth === endMonth) {
                        previewText = `${monthNames[startMonth - 1]} ${year}`;
                    } else {
                        const months = [];
                        if (startMonth <= endMonth) {
                            // Same year
                            for (let i = startMonth; i <= endMonth; i++) {
                                months.push(monthNames[i - 1]);
                            }
                        } else {
                            // Cross year (e.g., Nov to Feb)
                            for (let i = startMonth; i <= 12; i++) {
                                months.push(monthNames[i - 1]);
                            }
                            for (let i = 1; i <= endMonth; i++) {
                                months.push(monthNames[i - 1]);
                            }
                        }
                        previewText = `${months.join(', ')} ${year}`;
                    }

                    previewDiv.innerHTML = `<strong>Billing Period:</strong> ${previewText}`;
                    previewDiv.className = 'p-3 bg-blue-50 rounded-lg text-sm text-blue-800 border border-blue-200';
                } else {
                    previewDiv.innerHTML = 'Select start and end months to see preview';
                    previewDiv.className = 'p-3 bg-gray-50 rounded-lg text-sm text-gray-600';
                }
            }

            // Add event listeners
            startMonthSelect.addEventListener('change', updatePreview);
            endMonthSelect.addEventListener('change', updatePreview);
            yearSelect.addEventListener('change', updatePreview);

            // Initial preview update
            updatePreview();
        });
    </script>
</x-layouts.app>
