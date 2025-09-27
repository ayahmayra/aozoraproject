<x-layouts.app :title="__('Invoice Details')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Invoice #{{ $invoice->invoice_number }}</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Invoice details and payment information</flux:text>
            </div>
            <div class="flex space-x-3">
                <flux:button variant="outline" href="{{ route('admin.invoices') }}">
                    <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                    Back to Invoices
                </flux:button>
                @if($invoice->payment_status === 'pending')
                    <flux:button variant="primary" onclick="markPaid()">
                        <flux:icon.check class="h-4 w-4 mr-2" />
                        Mark as Paid
                    </flux:button>
                @elseif($invoice->payment_status === 'paid')
                    <flux:button variant="primary" onclick="verifyPayment()">
                        <flux:icon.shield-check class="h-4 w-4 mr-2" />
                        Verify Payment
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
        @endif

        @if (session()->has('error'))
            <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Invoice Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Invoice Information -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Invoice Information</flux:heading>
                        
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div>
                                <flux:field>
                                    <flux:label>Invoice Number</flux:label>
                                    <flux:text>{{ $invoice->invoice_number }}</flux:text>
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Invoice Date</flux:label>
                                    <flux:text>{{ $invoice->invoice_date->format('F d, Y') }}</flux:text>
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Due Date</flux:label>
                                    <flux:text class="{{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $invoice->due_date->format('F d, Y') }}
                                        @if($invoice->isOverdue())
                                            ({{ $invoice->due_date->diffForHumans() }})
                                        @endif
                                    </flux:text>
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Payment Status</flux:label>
                                    <div>
                                        @if($invoice->payment_status === 'verified')
                                            <flux:badge color="green">Verified</flux:badge>
                                        @elseif($invoice->payment_status === 'paid')
                                            <flux:badge color="yellow">Paid</flux:badge>
                                        @elseif($invoice->payment_status === 'overdue')
                                            <flux:badge color="red">Overdue</flux:badge>
                                        @elseif($invoice->payment_status === 'pending')
                                            <flux:badge color="red">Pending</flux:badge>
                                        @else
                                            <flux:badge color="gray">Cancelled</flux:badge>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Billing Period</flux:label>
                                    <flux:text>{{ $invoice->billing_period_start->format('M d, Y') }} - {{ $invoice->billing_period_end->format('M d, Y') }}</flux:text>
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Payment Method</flux:label>
                                    <flux:text>{{ ucfirst($invoice->payment_method) }}</flux:text>
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Invoice Items -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Invoice Items</flux:heading>
                        
                        <div class="overflow-x-auto">
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>Item</flux:table.column>
                                    <flux:table.column>Description</flux:table.column>
                                    <flux:table.column>Type</flux:table.column>
                                    <flux:table.column>Quantity</flux:table.column>
                                    <flux:table.column>Unit Price</flux:table.column>
                                    <flux:table.column>Total</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach($invoice->items as $item)
                                        <flux:table.row>
                                            <flux:table.cell>{{ $item->item_name }}</flux:table.cell>
                                            <flux:table.cell>{{ $item->item_description }}</flux:table.cell>
                                            <flux:table.cell>
                                                <flux:badge 
                                                    color="{{ $item->item_type === 'tuition' ? 'blue' : ($item->item_type === 'fee' ? 'green' : ($item->item_type === 'penalty' ? 'red' : 'yellow')) }}"
                                                    size="sm"
                                                >
                                                    {{ ucfirst($item->item_type) }}
                                                </flux:badge>
                                            </flux:table.cell>
                                            <flux:table.cell>{{ $item->quantity }}</flux:table.cell>
                                            <flux:table.cell>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</flux:table.cell>
                                            <flux:table.cell class="font-medium">Rp {{ number_format($item->total_price, 0, ',', '.') }}</flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </div>
                        
                        <!-- Invoice Totals -->
                        <div class="mt-6 border-t pt-4">
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if($invoice->tax_amount > 0)
                                        <div class="flex justify-between">
                                            <span>Tax:</span>
                                            <span>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                        <span>Total:</span>
                                        <span>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if($invoice->paid_amount > 0)
                                        <div class="flex justify-between text-green-600">
                                            <span>Paid:</span>
                                            <span>Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-red-600">
                                            <span>Remaining:</span>
                                            <span>Rp {{ number_format($invoice->getRemainingAmount(), 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Payment History -->
                @if($invoice->payments->count() > 0)
                    <flux:card>
                        <div class="p-6">
                            <flux:heading size="lg" class="mb-4">Payment History</flux:heading>
                            
                            <div class="space-y-4">
                                @foreach($invoice->payments as $payment)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium">Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</div>
                                                <div class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }} - {{ ucfirst($payment->payment_method) }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div>
                                                    @if($payment->status === 'verified')
                                                        <flux:badge color="green">Verified</flux:badge>
                                                    @elseif($payment->status === 'pending')
                                                        <flux:badge color="yellow">Pending</flux:badge>
                                                    @else
                                                        <flux:badge color="red">Rejected</flux:badge>
                                                    @endif
                                                </div>
                                                @if($payment->payment_reference)
                                                    <div class="text-xs text-gray-500 mt-1">Ref: {{ $payment->payment_reference }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @if($payment->payment_notes)
                                            <div class="mt-2 text-xs text-gray-600">{{ $payment->payment_notes }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </flux:card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Student Information -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Student Information</flux:heading>
                        
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:avatar name="{{ $invoice->student->user->name }}" size="lg" />
                            <div>
                                <div class="font-medium">{{ $invoice->student->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->student->user->email }}</div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div><strong>Subject:</strong> {{ $invoice->subject->name }}</div>
                            <div><strong>Enrollment:</strong> #{{ $invoice->enrollment->enrollment_number ?? 'N/A' }}</div>
                            @if($invoice->enrollment->start_date)
                                <div><strong>Start Date:</strong> {{ $invoice->enrollment->start_date->format('M d, Y') }}</div>
                            @endif
                        </div>
                    </div>
                </flux:card>

                <!-- Actions -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Actions</flux:heading>
                        
                        <div class="space-y-3">
                            <flux:button variant="outline" class="w-full" onclick="printInvoice()">
                                <flux:icon.printer class="h-4 w-4 mr-2" />
                                Print Invoice
                            </flux:button>
                            
                            @if($invoice->payment_status === 'pending')
                                <flux:button variant="primary" class="w-full" onclick="markPaid()">
                                    <flux:icon.check class="h-4 w-4 mr-2" />
                                    Mark as Paid
                                </flux:button>
                            @elseif($invoice->payment_status === 'paid')
                                <flux:button variant="primary" class="w-full" onclick="verifyPayment()">
                                    <flux:icon.shield-check class="h-4 w-4 mr-2" />
                                    Verify Payment
                                </flux:button>
                            @endif
                            
                            <flux:button variant="outline" class="w-full" href="{{ route('admin.invoices') }}">
                                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                                Back to List
                            </flux:button>
                        </div>
                    </div>
                </flux:card>

                <!-- Notes -->
                @if($invoice->notes)
                    <flux:card>
                        <div class="p-6">
                            <flux:heading size="lg" class="mb-4">Notes</flux:heading>
                            <flux:text>{{ $invoice->notes }}</flux:text>
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div id="markPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium mb-4">Mark Invoice as Paid</h3>
                <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Paid Amount</flux:label>
                            <flux:input name="paid_amount" type="number" step="0.01" value="{{ $invoice->total_amount }}" required />
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
        function markPaid() {
            document.getElementById('markPaidModal').classList.remove('hidden');
        }

        function closeMarkPaidModal() {
            document.getElementById('markPaidModal').classList.add('hidden');
        }

        function verifyPayment() {
            if (confirm('Are you sure you want to verify the payment for this invoice?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.invoices.verify-payment", $invoice) }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function printInvoice() {
            window.print();
        }
    </script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .card { border: 1px solid #000; margin-bottom: 20px; }
        }
    </style>
</x-layouts.app>
