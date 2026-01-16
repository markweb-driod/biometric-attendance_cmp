<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Phone',
            'Department',
            'Academic Level',
            'Status',
            'Created At'
        ];
    }

    public function map($student): array
    {
        return [
            $student['ID'],
            $student['Matric Number'],
            $student['Full Name'],
            $student['Email'],
            $student['Phone'],
            $student['Department'],
            $student['Academic Level'],
            $student['Status'],
            $student['Created At']
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
            'E' => 15,
            'F' => 20,
            'G' => 15,
            'H' => 10,
            'I' => 20,
        ];
    }
}
