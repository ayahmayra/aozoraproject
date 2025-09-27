<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $year;
    protected $groupedInvoices;
    protected $months;
    protected $monthlyTotals;

    public function __construct($year, $groupedInvoices, $months, $monthlyTotals)
    {
        $this->year = $year;
        $this->groupedInvoices = $groupedInvoices;
        $this->months = $months;
        $this->monthlyTotals = $monthlyTotals;
    }

    public function collection()
    {
        $data = collect();
        
        // Add data rows
        foreach ($this->groupedInvoices as $key => $invoices) {
            $parts = explode('|', $key);
            $studentName = $parts[0];
            $subjectName = $parts[1];
            
            $row = [
                'student_name' => $studentName,
                'subject_name' => $subjectName,
            ];
            
            // Add monthly data
            foreach ($this->months as $monthNum => $monthName) {
                $monthInvoice = $invoices->first(function($invoice) use ($monthNum) {
                    return $invoice->billing_period_start->month == $monthNum;
                });
                
                if ($monthInvoice && $monthInvoice->payment_status === 'verified') {
                    // Only show verified payments with raw number (no Rp, no formatting)
                    $row["month_{$monthNum}"] = $monthInvoice->paid_amount;
                } else {
                    // Empty cell for non-verified or no invoice
                    $row["month_{$monthNum}"] = '';
                }
            }
            
            $data->push($row);
        }
        
        // Add total row
        $totalRow = [
            'student_name' => 'TOTAL TERVERIFIKASI',
            'subject_name' => '',
        ];
        
        foreach ($this->months as $monthNum => $monthName) {
            if ($this->monthlyTotals[$monthNum] > 0) {
                // Raw number for totals (no Rp, no formatting)
                $totalRow["month_{$monthNum}"] = $this->monthlyTotals[$monthNum];
            } else {
                // Empty cell for zero totals
                $totalRow["month_{$monthNum}"] = '';
            }
        }
        
        $data->push($totalRow);
        
        return $data;
    }

    public function headings(): array
    {
        $headings = ['Nama Student', 'Subject'];
        
        foreach ($this->months as $monthNum => $monthName) {
            $headings[] = $monthName;
        }
        
        return $headings;
    }

    public function map($row): array
    {
        $mapped = [
            $row['student_name'],
            $row['subject_name'],
        ];
        
        foreach ($this->months as $monthNum => $monthName) {
            $mapped[] = $row["month_{$monthNum}"];
        }
        
        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E5E7EB',
                    ],
                ],
            ],
            // Style the total row
            count($this->groupedInvoices) + 2 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'F3F4F6',
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 25, // Nama Student
            'B' => 20, // Subject
        ];
        
        // Set width for month columns
        $column = 'C';
        foreach ($this->months as $monthNum => $monthName) {
            $widths[$column] = 15;
            $column++;
        }
        
        return $widths;
    }
}