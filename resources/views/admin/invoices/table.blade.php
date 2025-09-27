<x-layouts.app title="Invoice Table">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Invoice Table</flux:heading>
                <flux:text class="mt-2">Advanced invoice management and reporting</flux:text>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Year Filter -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Invoice Table - Tahun {{ $year }}</flux:heading>
                            <flux:text class="mt-1">Tabel invoice tahunan dengan status pembayaran per bulan</flux:text>
                        </div>
                        <div class="flex items-center space-x-4">
                            <flux:field>
                                <flux:label>Tahun</flux:label>
                                <flux:select name="year" onchange="changeFilter()">
                                    @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:label>Subject</flux:label>
                                <flux:select id="subjectFilter" onchange="changeFilter()">
                                    <option value="">Semua Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $subjectFilter == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                            
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Invoice Table -->
            <flux:card>
                <div class="p-6">
                    <div class="flex justify-end mb-6">
                        <flux:button size="xs"  variant="primary" href="{{ route('admin.invoices.table.export', ['year' => $year, 'subject' => $subjectFilter]) }}">
                            <flux:icon.arrow-down-tray class="h-4 w-4 mr-2" />
                            Export Excel
                        </flux:button>
                    </div>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Nama Student</flux:table.column>
                            <flux:table.column>Subject</flux:table.column>
                            @foreach($months as $monthNum => $monthName)
                                <flux:table.column>{{ $monthName }}</flux:table.column>
                            @endforeach
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($groupedInvoices as $key => $invoices)
                                @php
                                    $parts = explode('|', $key);
                                    $studentName = $parts[0];
                                    $subjectName = $parts[1];
                                @endphp
                                <flux:table.row>
                                    <flux:table.cell variant="strong">{{ $studentName }}</flux:table.cell>
                                    <flux:table.cell>{{ $subjectName }}</flux:table.cell>
                                    @foreach($months as $monthNum => $monthName)
                                        @php
                                            $monthInvoice = $invoices->first(function($invoice) use ($monthNum) {
                                                return $invoice->billing_period_start->month == $monthNum;
                                            });
                                        @endphp
                                        <flux:table.cell>
                                            @if($monthInvoice)
                                                @if($monthInvoice->payment_status === 'verified')
                                                    <flux:badge size="sm" color="green" inset="top bottom">{{ number_format($monthInvoice->paid_amount, 0, ',', '.') }}</flux:badge>
                                                @elseif($monthInvoice->payment_status === 'paid')
                                                    <flux:badge size="sm" color="yellow" inset="top bottom">Paid</flux:badge>
                                                @elseif($monthInvoice->payment_status === 'overdue')
                                                    <flux:badge size="sm" color="red" inset="top bottom">Overdue</flux:badge>
                                                @elseif($monthInvoice->payment_status === 'pending')
                                                    <flux:badge size="sm" color="red" inset="top bottom">Pending</flux:badge>
                                                @else
                                                    <flux:badge size="sm" color="gray" inset="top bottom">Cancelled</flux:badge>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </flux:table.cell>
                                    @endforeach
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="{{ count($months) + 2 }}">
                                        <div class="text-center py-12">
                                            <flux:icon.table-cells class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                            <div class="text-gray-500">Tidak ada data invoice untuk tahun {{ $year }}</div>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                            
                            <!-- Total Row -->
                            @if($groupedInvoices->count() > 0)
                                <flux:table.row>
                                    <flux:table.cell variant="strong" colspan="2">
                                        <div class="flex items-center">
                                            <flux:icon.calculator class="h-5 w-5 mr-2" />
                                            Total Terverifikasi
                                        </div>
                                    </flux:table.cell>
                                    @foreach($months as $monthNum => $monthName)
                                        <flux:table.cell>
                                            @if($monthlyTotals[$monthNum] > 0)
                                                <flux:badge size="sm" color="green" inset="top bottom">
                                                    Rp {{ number_format($monthlyTotals[$monthNum], 0, ',', '.') }}
                                                </flux:badge>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </flux:table.cell>
                                    @endforeach
                                </flux:table.row>
                            @endif
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:card>

            <!-- Statistics -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment Statistics -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-6">Statistik Pembayaran Terverifikasi</flux:heading>
                        
                        <div class="space-y-4">
                            <!-- Current Month -->
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div>
                                    <div class="text-sm font-medium text-blue-600">Bulan {{ $months[$currentMonth] }} {{ $currentYear }}</div>
                                    <div class="text-2xl font-bold text-blue-800">Rp {{ number_format($currentMonthTotal, 0, ',', '.') }}</div>
                                </div>
                                <flux:icon.calendar class="h-8 w-8 text-blue-500" />
                            </div>
                            
                            <!-- Current Year -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                <div>
                                    <div class="text-sm font-medium text-green-600">Tahun {{ $currentYear }}</div>
                                    <div class="text-2xl font-bold text-green-800">Rp {{ number_format($currentYearTotal, 0, ',', '.') }}</div>
                                </div>
                                <flux:icon.chart-bar class="h-8 w-8 text-green-500" />
                            </div>
                            
                            <!-- Overall Total -->
                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div>
                                    <div class="text-sm font-medium text-purple-600">Total Keseluruhan</div>
                                    <div class="text-2xl font-bold text-purple-800">Rp {{ number_format($overallTotal, 0, ',', '.') }}</div>
                                </div>
                                <flux:icon.banknotes class="h-8 w-8 text-purple-500" />
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Invoice Status Statistics -->
                <flux:card>
                    <div class="p-6">
                        <flux:heading size="lg" class="mb-6">Status Invoice Tahun {{ $year }}</flux:heading>
                        
                        <div class="space-y-4">
                            <!-- Total Invoices -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <div class="text-sm font-medium text-gray-600">Total Invoice</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ number_format($totalInvoices) }}</div>
                                </div>
                                <flux:icon.document-text class="h-8 w-8 text-gray-500" />
                            </div>
                            
                            <!-- Verified Invoices -->
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                <div>
                                    <div class="text-sm font-medium text-green-600">Terverifikasi</div>
                                    <div class="text-2xl font-bold text-green-800">{{ number_format($verifiedInvoices) }}</div>
                                </div>
                                <flux:icon.shield-check class="h-8 w-8 text-green-500" />
                            </div>
                            
                            <!-- Paid Invoices -->
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div>
                                    <div class="text-sm font-medium text-yellow-600">Sudah Dibayar</div>
                                    <div class="text-2xl font-bold text-yellow-800">{{ number_format($paidInvoices) }}</div>
                                </div>
                                <flux:icon.check-circle class="h-8 w-8 text-yellow-500" />
                            </div>
                            
                            <!-- Pending Invoices -->
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                                <div>
                                    <div class="text-sm font-medium text-red-600">Menunggu Pembayaran</div>
                                    <div class="text-2xl font-bold text-red-800">{{ number_format($pendingInvoices) }}</div>
                                </div>
                                <flux:icon.clock class="h-8 w-8 text-red-500" />
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure DOM is loaded before any manipulation
            console.log('Invoice table page loaded');
        });

        // Prevent Livewire conflicts
        document.addEventListener('livewire:init', function() {
            console.log('Livewire initialized');
        });

        function changeFilter() {
            try {
                // Use more specific selectors to avoid conflicts
                const yearSelect = document.querySelector('select[name="year"]');
                const subjectSelect = document.getElementById('subjectFilter');
                
                if (!yearSelect || !subjectSelect) {
                    console.error('Filter elements not found', { yearSelect, subjectSelect });
                    return;
                }
                
                const year = yearSelect.value;
                const subject = subjectSelect.value;
                
                const url = new URL(window.location);
                url.searchParams.set('year', year);
                
                if (subject) {
                    url.searchParams.set('subject', subject);
                } else {
                    url.searchParams.delete('subject');
                }
                
                window.location.href = url.toString();
            } catch (error) {
                console.error('Error in changeFilter:', error);
            }
        }
    </script>
</x-layouts.app>
