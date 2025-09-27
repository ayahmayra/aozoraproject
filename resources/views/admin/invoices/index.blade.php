<x-layouts.app :title="__('Invoice Management')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Invoice Management</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Manage student invoices and payments</flux:text>
            </div>
            <div class="flex space-x-3">
                <flux:button variant="primary" href="{{ route('admin.invoices.generate') }}">
                    <flux:icon.plus class="h-4 w-4 mr-2" />
                    Generate Invoices
                </flux:button>
                <flux:button variant="danger" onclick="showDeleteModal()">
                    <flux:icon.trash class="h-4 w-4 mr-2" />
                    Delete Non-Active
                </flux:button>
                <flux:button variant="outline" href="{{ route('admin.invoices.statistics') }}">
                    <flux:icon.chart-bar class="h-4 w-4 mr-2" />
                    Statistics
                </flux:button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
        @endif

        @if (session()->has('error'))
            <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
        @endif

        @if (session()->has('info'))
            <flux:callout class="mb-6" variant="info" icon="information-circle" :heading="session('info')" />
        @endif

        <!-- Filters -->
        <flux:card>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.invoices') }}" class="space-y-4">
                    <!-- First Row: Search and Status Filters -->
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                        <div>
                            <flux:field>
                                <flux:label>Search</flux:label>
                                <flux:input 
                                    name="search" 
                                    placeholder="Invoice number, student, subject..." 
                                    value="{{ request('search') }}"
                                />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Payment Status</flux:label>
                                <flux:select name="payment_status">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="cancelled" {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </flux:select>
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Payment Method</flux:label>
                                <flux:select name="payment_method">
                                    <option value="">All Methods</option>
                                    <option value="monthly" {{ request('payment_method') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="semester" {{ request('payment_method') == 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="yearly" {{ request('payment_method') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </flux:select>
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Billing Period</flux:label>
                                <div class="flex space-x-2">
                                    <flux:select name="filter_month" class="flex-1">
                                        <option value="">All Months</option>
                                        @php
                                            $currentMonth = now()->month;
                                            $selectedMonth = request('filter_month', $currentMonth);
                                        @endphp
                                        <option value="1" {{ $selectedMonth == '1' ? 'selected' : '' }}>January</option>
                                        <option value="2" {{ $selectedMonth == '2' ? 'selected' : '' }}>February</option>
                                        <option value="3" {{ $selectedMonth == '3' ? 'selected' : '' }}>March</option>
                                        <option value="4" {{ $selectedMonth == '4' ? 'selected' : '' }}>April</option>
                                        <option value="5" {{ $selectedMonth == '5' ? 'selected' : '' }}>May</option>
                                        <option value="6" {{ $selectedMonth == '6' ? 'selected' : '' }}>June</option>
                                        <option value="7" {{ $selectedMonth == '7' ? 'selected' : '' }}>July</option>
                                        <option value="8" {{ $selectedMonth == '8' ? 'selected' : '' }}>August</option>
                                        <option value="9" {{ $selectedMonth == '9' ? 'selected' : '' }}>September</option>
                                        <option value="10" {{ $selectedMonth == '10' ? 'selected' : '' }}>October</option>
                                        <option value="11" {{ $selectedMonth == '11' ? 'selected' : '' }}>November</option>
                                        <option value="12" {{ $selectedMonth == '12' ? 'selected' : '' }}>December</option>
                                    </flux:select>
                                    <flux:select name="filter_year" class="flex-1">
                                        <option value="">All Years</option>
                                        @php
                                            $currentYear = now()->year;
                                            $selectedYear = request('filter_year', $currentYear);
                                        @endphp
                                        @for($year = now()->year; $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </flux:select>
                                </div>
                                <flux:description>Filter by billing period (not invoice date)</flux:description>
                            </flux:field>
                        </div>
                    </div>
                    
                    <!-- Second Row: Date Range and Filter Button -->
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                        <div>
                            <flux:field>
                                <flux:label>Date From</flux:label>
                                <flux:input 
                                    name="date_from" 
                                    type="date"
                                    value="{{ request('date_from') }}"
                                />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Date To</flux:label>
                                <flux:input 
                                    name="date_to" 
                                    type="date"
                                    value="{{ request('date_to') }}"
                                />
                            </flux:field>
                        </div>
                        <div class="flex items-end">
                            <flux:button variant="outline" type="submit" class="w-full">
                                <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                                Filter
                            </flux:button>
                        </div>
                        <div class="flex items-end">
                            <flux:button variant="ghost" type="button" onclick="clearFilters()" class="w-full">
                                <flux:icon.x-mark class="h-4 w-4 mr-2" />
                                Clear Filters
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:card>

        <!-- Active Filters Display -->
        @php
            $hasFilters = request()->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to']) || 
                         (request('filter_month') || request('filter_year')) ||
                         (!request()->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to', 'filter_month', 'filter_year']));
        @endphp
        @if($hasFilters)
            <flux:card>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <flux:icon.funnel class="h-4 w-4 text-gray-500" />
                            <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                        </div>
                        <flux:button variant="ghost" size="sm" onclick="clearFilters()">
                            <flux:icon.x-mark class="h-3 w-3 mr-1" />
                            Clear All
                        </flux:button>
                    </div>
                    
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if(request('search'))
                            <flux:badge color="blue" size="sm">
                                Search: {{ request('search') }}
                                <button onclick="removeFilter('search')" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        
                        @if(request('payment_status'))
                            <flux:badge color="green" size="sm">
                                Status: {{ ucfirst(request('payment_status')) }}
                                <button onclick="removeFilter('payment_status')" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        
                        @if(request('payment_method'))
                            <flux:badge color="purple" size="sm">
                                Method: {{ ucfirst(request('payment_method')) }}
                                <button onclick="removeFilter('payment_method')" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        
                        @php
                            $displayMonth = request('filter_month') ?: now()->month;
                            $displayYear = request('filter_year') ?: now()->year;
                            $isDefaultFilter = !request()->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to', 'filter_month', 'filter_year']);
                        @endphp
                        @if($displayMonth && $displayYear)
                            <flux:badge color="orange" size="sm">
                                Billing Period: {{ \DateTime::createFromFormat('!m', $displayMonth)->format('F') }} {{ $displayYear }}
                                @if(!$isDefaultFilter)
                                    <button onclick="removeFilter('filter_month'); removeFilter('filter_year')" class="ml-1 hover:text-red-500">×</button>
                                @endif
                            </flux:badge>
                        @endif
                        
                        @if(request('date_from'))
                            <flux:badge color="cyan" size="sm">
                                From: {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                <button onclick="removeFilter('date_from')" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        
                        @if(request('date_to'))
                            <flux:badge color="cyan" size="sm">
                                To: {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                <button onclick="removeFilter('date_to')" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Invoices Table -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Invoices ({{ $invoices->total() }})</flux:heading>
                    @php
                        $hasActiveFilters = request()->hasAny(['search', 'payment_status', 'payment_method', 'date_from', 'date_to', 'filter_month', 'filter_year']);
                        $isDefaultView = !$hasActiveFilters;
                    @endphp
                    @if($hasActiveFilters)
                        <div class="text-sm text-gray-500">
                            Filtered results
                        </div>
                    @else
                        <div class="text-sm text-blue-600">
                            Showing current month ({{ now()->format('F Y') }})
                        </div>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Invoice #</flux:table.column>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Subject</flux:table.column>
                        <flux:table.column>Period</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Due Date</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($invoices as $invoice)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="font-mono text-sm">{{ $invoice->invoice_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center">
                                        <flux:avatar name="{{ $invoice->student->user->name }}" size="sm" class="mr-2" />
                                        <div>
                                            <div class="text-sm font-medium">{{ $invoice->student->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $invoice->student->user->email }}</div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>{{ $invoice->subject->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm">
                                        {{ $invoice->billing_period_start->format('M d') }} - {{ $invoice->billing_period_end->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($invoice->payment_method) }}</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm font-medium">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
                                    @if($invoice->paid_amount > 0)
                                        <div class="text-xs text-green-600">Paid: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($invoice->payment_status === 'paid')
                                        <flux:badge color="green">Paid</flux:badge>
                                    @elseif($invoice->payment_status === 'overdue')
                                        <flux:badge color="red">Overdue</flux:badge>
                                    @elseif($invoice->payment_status === 'pending')
                                        <flux:badge color="yellow">Pending</flux:badge>
                                    @else
                                        <flux:badge color="gray">Cancelled</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm">{{ $invoice->due_date->format('M d, Y') }}</div>
                                    @if($invoice->isOverdue())
                                        <div class="text-xs text-red-600">{{ $invoice->due_date->diffForHumans() }}</div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" href="{{ route('admin.invoices.show', $invoice) }}">
                                            <flux:icon.eye class="h-4 w-4" />
                                        </flux:button>
                                        @if($invoice->payment_status === 'pending')
                                            <flux:button variant="ghost" size="sm" onclick="markPaid({{ $invoice->id }}, {{ $invoice->total_amount }})">
                                                <flux:icon.check class="h-4 w-4" />
                                            </flux:button>
                                        @elseif($invoice->payment_status === 'paid')
                                            <flux:button variant="ghost" size="sm" onclick="cancelPayment({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                                <flux:icon.x-mark class="h-4 w-4" />
                                            </flux:button>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8" class="text-center py-8">
                                    <div class="text-gray-500">
                                        <flux:icon.document-text class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                        <p class="text-lg font-medium">No invoices found</p>
                                        <p class="text-sm mb-4">Generate invoices to get started</p>
                                        <flux:button variant="primary" href="{{ route('admin.invoices.generate') }}">
                                            <flux:icon.plus class="h-4 w-4 mr-2" />
                                            Generate Invoices
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
            
            <!-- Pagination -->
            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
            @endif
        </flux:card>
    </div>

    <!-- Mark as Paid Modal -->
    <div id="markPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium mb-4">Mark Invoice as Paid</h3>
                <form id="markPaidForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Paid Amount</flux:label>
                            <flux:input name="paid_amount" type="number" step="0.01" required />
                        </flux:field>
                        <flux:field>
                            <flux:label>Payment Date</flux:label>
                            <flux:input name="payment_date" type="date" value="{{ now()->format('Y-m-d') }}" required />
                        </flux:field>
                        <flux:field>
                            <flux:label>Payment Method</flux:label>
                            <flux:select name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Payment Reference</flux:label>
                            <flux:input name="payment_reference" placeholder="Transaction reference number" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Notes</flux:label>
                            <flux:textarea name="notes" rows="3"></flux:textarea>
                        </flux:field>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <flux:button variant="outline" type="button" onclick="closeMarkPaidModal()">Cancel</flux:button>
                        <flux:button variant="primary" type="submit">Mark as Paid</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Payment Modal -->
    <div id="cancelPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center mb-4">
                    <flux:icon.exclamation-triangle class="h-8 w-8 text-orange-500 mr-3" />
                    <h3 class="text-lg font-medium">Cancel Payment</h3>
                </div>
                
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Are you sure you want to cancel the payment for invoice <strong id="cancelInvoiceNumber"></strong>?
                    </p>
                    
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <flux:icon.information-circle class="h-5 w-5 text-orange-500 mt-0.5 mr-2" />
                            <div class="text-sm text-orange-700">
                                <p class="font-medium">This action will:</p>
                                <ul class="mt-1 list-disc list-inside">
                                    <li>Change invoice status from "Paid" to "Pending"</li>
                                    <li>Remove all payment records for this invoice</li>
                                    <li>Reset payment amounts to zero</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="cancelPaymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-end space-x-3">
                        <flux:button variant="outline" type="button" onclick="closeCancelPaymentModal()">Cancel</flux:button>
                        <flux:button variant="danger" type="submit">
                            <flux:icon.x-mark class="h-4 w-4 mr-2" />
                            Cancel Payment
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Non-Active Invoices Modal -->
    <div id="deleteNonActiveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center mb-4">
                    <flux:icon.exclamation-triangle class="h-8 w-8 text-red-500 mr-3" />
                    <h3 class="text-lg font-medium">Delete Non-Active Invoices</h3>
                </div>
                
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-4">
                        This action will permanently delete all invoices that are not marked as "paid" (active). 
                        This includes pending, overdue, and cancelled invoices.
                    </p>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <flux:icon.information-circle class="h-5 w-5 text-red-500 mt-0.5 mr-2" />
                            <div class="text-sm text-red-700">
                                <p class="font-medium">Warning:</p>
                                <ul class="mt-1 list-disc list-inside">
                                    <li>This action cannot be undone</li>
                                    <li>All payment history will be lost</li>
                                    <li>Only paid invoices will be preserved</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <flux:button variant="outline" type="button" onclick="closeDeleteModal()">Cancel</flux:button>
                    <form method="POST" action="{{ route('admin.invoices.delete-non-active') }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <flux:button variant="danger" type="submit">
                            <flux:icon.trash class="h-4 w-4 mr-2" />
                            Delete All Non-Active
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markPaid(invoiceId, totalAmount) {
            const form = document.getElementById('markPaidForm');
            form.action = `/admin/invoices/${invoiceId}/mark-paid`;
            
            // Set the paid amount to the total amount by default
            const paidAmountInput = form.querySelector('input[name="paid_amount"]');
            if (paidAmountInput) {
                paidAmountInput.value = totalAmount;
            }
            
            document.getElementById('markPaidModal').classList.remove('hidden');
        }

        function closeMarkPaidModal() {
            document.getElementById('markPaidModal').classList.add('hidden');
        }

        function cancelPayment(invoiceId, invoiceNumber) {
            const form = document.getElementById('cancelPaymentForm');
            form.action = `/admin/invoices/${invoiceId}/cancel-payment`;
            
            // Set the invoice number in the modal
            document.getElementById('cancelInvoiceNumber').textContent = invoiceNumber;
            
            document.getElementById('cancelPaymentModal').classList.remove('hidden');
        }

        function closeCancelPaymentModal() {
            document.getElementById('cancelPaymentModal').classList.add('hidden');
        }

        function showDeleteModal() {
            document.getElementById('deleteNonActiveModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteNonActiveModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const markPaidModal = document.getElementById('markPaidModal');
            const cancelPaymentModal = document.getElementById('cancelPaymentModal');
            const deleteModal = document.getElementById('deleteNonActiveModal');
            
            if (event.target === markPaidModal) {
                closeMarkPaidModal();
            }
            
            if (event.target === cancelPaymentModal) {
                closeCancelPaymentModal();
            }
            
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        function clearFilters() {
            // Reset all form inputs
            document.querySelector('form').reset();
            
            // Redirect to clean URL
            window.location.href = '{{ route("admin.invoices") }}';
        }

        function removeFilter(filterName) {
            const url = new URL(window.location);
            url.searchParams.delete(filterName);
            window.location.href = url.toString();
        }
    </script>
</x-layouts.app>
