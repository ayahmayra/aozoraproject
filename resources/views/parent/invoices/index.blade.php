<x-layouts.app title="Invoice Management">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Invoice Management</flux:heading>
                <flux:text class="mt-2">Kelola invoice dan pembayaran untuk anak-anak Anda</flux:text>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <flux:field>
                            <flux:label>Search</flux:label>
                            <flux:input name="search" value="{{ request('search') }}" placeholder="Search invoice number, student, or subject..." />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Payment Status</flux:label>
                            <flux:select name="payment_status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </flux:select>
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Payment Method</flux:label>
                            <flux:select name="payment_method">
                                <option value="">All Methods</option>
                                <option value="monthly" {{ request('payment_method') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="semester" {{ request('payment_method') == 'semester' ? 'selected' : '' }}>Semester</option>
                                <option value="yearly" {{ request('payment_method') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </flux:select>
                        </flux:field>
                        
                        <div class="flex items-end space-x-2">
                            <flux:button type="submit" variant="primary">Filter</flux:button>
                            <flux:button type="button" variant="outline" onclick="window.location.href='{{ route('parent.invoice') }}'">Clear</flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>

            <!-- Invoice Table -->
            <flux:card>
                <div class="p-6">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Invoice Number</flux:table.column>
                            <flux:table.column>Student</flux:table.column>
                            <flux:table.column>Subject</flux:table.column>
                            <flux:table.column>Amount</flux:table.column>
                            <flux:table.column>Payment Method</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column>Due Date</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($invoices as $invoice)
                                <flux:table.row>
                                    <flux:table.cell variant="strong">{{ $invoice->invoice_number }}</flux:table.cell>
                                    <flux:table.cell>{{ $invoice->student->user->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $invoice->subject->name }}</flux:table.cell>
                                    <flux:table.cell>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="blue">
                                            {{ ucfirst($invoice->payment_method) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($invoice->payment_status === 'pending')
                                            <flux:badge size="sm" color="red">Pending</flux:badge>
                                        @elseif($invoice->payment_status === 'paid')
                                            <flux:badge size="sm" color="yellow">Paid</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="gray">{{ ucfirst($invoice->payment_status) }}</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button size="xs" variant="outline" href="{{ route('parent.invoice.show', $invoice) }}">
                                            View
                                        </flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="8">
                                        <div class="text-center py-12">
                                            <flux:icon.document-text class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                            <div class="text-gray-500">Tidak ada invoice yang perlu ditangani</div>
                                            <div class="text-sm text-gray-400 mt-2">Semua invoice sudah terverifikasi atau belum ada invoice untuk periode ini</div>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>

                    @if($invoices->hasPages())
                        <div class="mt-6">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Information Card -->
            <flux:card class="mt-6">
                <div class="p-6">
                    <flux:callout variant="info" icon="information-circle" heading="Informasi Invoice">
                        <p>Halaman ini menampilkan invoice yang masih <strong>pending</strong> atau <strong>paid</strong> (belum diverifikasi) untuk anak-anak Anda.</p>
                        <ul class="mt-2 space-y-1 text-sm">
                            <li>• <strong>Pending:</strong> Invoice belum dibayar</li>
                            <li>• <strong>Paid:</strong> Invoice sudah dibayar, menunggu verifikasi admin</li>
                            <li>• Invoice yang sudah <strong>verified</strong> tidak ditampilkan di sini</li>
                        </ul>
                    </flux:callout>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
