<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Matric Number',
            'Full Name',
            'Email',
            'Department',
            'Academic Level',
            'Total Attendances',
            'Recent Attendances (30 days)',
            'Status'
        ];
    }

    public function map($student): array
    {
        return [
            $student['ID'],
            $student['Matric Number'],
            $student['Full Name'],
            $student['Email'],
            $student['Department'],
            $student['Academic Level'],
            $student['Total Attendances'],
            $student['Recent Attendances (30 days)'],
            $student['Status']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 20,
            'F' => 15,
            'G' => 18,
            'H' => 25,
            'I' => 10,
        ];
    }
}
