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

        <!-- Filters -->
        <flux:card>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.invoices') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-5">
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
                            <flux:label>Date From</flux:label>
                            <flux:input 
                                name="date_from" 
                                type="date"
                                value="{{ request('date_from') }}"
                            />
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button variant="outline" type="submit" class="w-full">
                            <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                            Filter
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>

        <!-- Invoices Table -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Invoices ({{ $invoices->total() }})</flux:heading>
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
                                        <flux:avatar name="{{ $invoice->student->name }}" size="sm" class="mr-2" />
                                        <div>
                                            <div class="text-sm font-medium">{{ $invoice->student->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $invoice->student->email }}</div>
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
                                            <flux:button variant="ghost" size="sm" onclick="markPaid({{ $invoice->id }})">
                                                <flux:icon.check class="h-4 w-4" />
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

    <script>
        function markPaid(invoiceId) {
            const form = document.getElementById('markPaidForm');
            form.action = `/admin/invoices/${invoiceId}/mark-paid`;
            document.getElementById('markPaidModal').classList.remove('hidden');
        }

        function closeMarkPaidModal() {
            document.getElementById('markPaidModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
