<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LecturersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Staff ID',
            'Full Name',
            'Email',
            'Phone',
            'Title',
            'Department',
            'Status',
            'Created At'
        ];
    }

    public function map($lecturer): array
    {
        return [
            $lecturer['ID'],
            $lecturer['Staff ID'],
            $lecturer['Full Name'],
            $lecturer['Email'],
            $lecturer['Phone'],
            $lecturer['Title'],
            $lecturer['Department'],
            $lecturer['Status'],
            $lecturer['Created At']
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
            'B' => 15,
            'C' => 25,
            'D' => 30,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 10,
            'I' => 20,
        ];
    }
}
