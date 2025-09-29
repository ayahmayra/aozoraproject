@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-layouts.app title="Invoice Detail">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Detail Invoice</flux:heading>
                <flux:text class="mt-2">Informasi lengkap mengenai invoice {{ $invoice->invoice_number }}</flux:text>
            </div>
            <div class="flex space-x-3">
                <flux:button variant="outline" href="{{ route('parent.invoice') }}">
                    Kembali ke Daftar
                </flux:button>
                <flux:button variant="primary" onclick="window.print()">
                    <flux:icon.printer class="h-4 w-4 mr-2" />
                    Print Invoice
                </flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    {{ session('success') }}
                </flux:callout>
            @endif

            @if($errors->any())
                <flux:callout variant="error" icon="exclamation-triangle" class="mb-6">
                    <flux:heading size="sm" class="mb-2">Terdapat kesalahan dalam form:</flux:heading>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </flux:callout>
            @endif
            <!-- Invoice Header -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="lg" class="mb-4">Informasi Invoice</flux:heading>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Invoice Number:</span>
                                    <span>{{ $invoice->invoice_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Invoice Date:</span>
                                    <span>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Due Date:</span>
                                    <span>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Status:</span>
                                    <span>
                                        @if($invoice->payment_status === 'pending')
                                            <flux:badge color="red">Pending</flux:badge>
                                        @elseif($invoice->payment_status === 'paid')
                                            <flux:badge color="yellow">Paid</flux:badge>
                                        @else
                                            <flux:badge color="green">Verified</flux:badge>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <flux:heading size="lg" class="mb-4">Informasi Student</flux:heading>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Student:</span>
                                    <span>{{ $invoice->student->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Subject:</span>
                                    <span>{{ $invoice->subject->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Payment Method:</span>
                                    <flux:badge color="blue">{{ ucfirst($invoice->payment_method) }}</flux:badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Billing Period -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <flux:heading size="lg" class="mb-4">Periode Tagihan</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Tanggal Mulai</flux:label>
                            <flux:text class="font-medium">{{ $invoice->billing_period_start ? $invoice->billing_period_start->format('d M Y') : '-' }}</flux:text>
                        </div>
                        <div>
                            <flux:label>Tanggal Selesai</flux:label>
                            <flux:text class="font-medium">{{ $invoice->billing_period_end ? $invoice->billing_period_end->format('d M Y') : '-' }}</flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Payment Information -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Pembayaran</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Total Amount:</span>
                                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($invoice->paid_amount > 0)
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">Paid Amount:</span>
                                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($invoice->payment_date)
                                    <div class="flex justify-between">
                                        <span class="font-medium">Payment Date:</span>
                                        <span>{{ $invoice->payment_date ? $invoice->payment_date->format('d M Y H:i') : '-' }}</span>
                                    </div>
                                @endif
                                @if($invoice->payment_reference)
                                    <div class="flex justify-between">
                                        <span class="font-medium">Payment Reference:</span>
                                        <span class="font-mono">{{ $invoice->payment_reference }}</span>
                                    </div>
                                @endif
                                @if($invoice->payment_proof)
                                    <div class="flex justify-between">
                                        <span class="font-medium">Payment Proof:</span>
                                        <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            <flux:icon.document-text class="h-4 w-4 inline mr-1" />
                                            View Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            @if($invoice->payment_status === 'pending')
                                <flux:callout variant="warning" icon="exclamation-triangle" heading="Menunggu Pembayaran">
                                    <p>Invoice ini belum dibayar. Silakan lakukan pembayaran sesuai dengan metode yang dipilih.</p>
                                </flux:callout>
                            @elseif($invoice->payment_status === 'paid')
                                <flux:callout variant="info" icon="information-circle" heading="Menunggu Verifikasi">
                                    <p>Pembayaran telah diterima dan sedang menunggu verifikasi dari admin.</p>
                                </flux:callout>
                            @else
                                <flux:callout variant="success" icon="check-circle" heading="Terverifikasi">
                                    <p>Pembayaran telah diverifikasi dan selesai.</p>
                                </flux:callout>
                            @endif
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Payment Information (for paid invoices) -->
            @if($invoice->payment_status === 'paid')
                <flux:card class="mb-6">
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Informasi Pembayaran</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Payment Reference:</span>
                                        <span class="font-mono">{{ $invoice->payment_reference }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Payment Date:</span>
                                        <span>{{ $invoice->payment_date ? $invoice->payment_date->format('d M Y H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Paid Amount:</span>
                                        <span class="font-bold text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($invoice->payment_proof)
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">Payment Proof:</span>
                                        <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                                            <flux:icon.document-text class="h-4 w-4 mr-1" />
                                            View Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endif

            <!-- Payment Update Form (only for pending invoices) -->
            @if($invoice->payment_status === 'pending')
                <flux:card class="mb-6">
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Update Pembayaran</flux:heading>
                        <flux:text class="mb-6">Silakan isi informasi pembayaran dan upload bukti pembayaran untuk mengupdate status invoice.</flux:text>
                        
                        <form action="{{ route('parent.invoice.payment.update', $invoice) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:field>
                                    <flux:label>Nomor Referensi Pembayaran *</flux:label>
                                    <flux:input name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Masukkan nomor referensi pembayaran" required />
                                    @error('payment_reference')
                                        <flux:text color="red" size="sm">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                                
                                <flux:field>
                                    <flux:label>Tanggal Pembayaran *</flux:label>
                                    <flux:input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required />
                                    @error('payment_date')
                                        <flux:text color="red" size="sm">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                            </div>
                            
                            <div class="mt-6">
                                <flux:field>
                                    <flux:label>Bukti Pembayaran *</flux:label>
                                    <flux:input type="file" name="payment_proof" accept="image/*,.pdf" required />
                                    <flux:text size="sm" class="mt-1">Format yang diterima: JPG, PNG, PDF. Maksimal 2MB.</flux:text>
                                    @error('payment_proof')
                                        <flux:text color="red" size="sm">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                            </div>
                            
                            <div class="mt-6">
                                <flux:field>
                                    <flux:label>Catatan (Opsional)</flux:label>
                                    <flux:textarea name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</flux:textarea>
                                    @error('notes')
                                        <flux:text color="red" size="sm">{{ $message }}</flux:text>
                                    @enderror
                                </flux:field>
                            </div>
                            
                            <div class="mt-6 flex justify-end space-x-3">
                                <flux:button type="button" variant="outline" onclick="history.back()">
                                    Batal
                                </flux:button>
                                <flux:button type="submit" variant="primary">
                                    <flux:icon.banknotes class="h-4 w-4 mr-2" />
                                    Update Pembayaran
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:card>
            @endif

            <!-- Notes -->
            @if($invoice->notes)
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-4">Catatan</flux:heading>
                        <flux:text>{{ $invoice->notes }}</flux:text>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 20px;
            }
            
            .print-content {
                margin-top: 20px;
            }
        }
    </style>
</x-layouts.app>