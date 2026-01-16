<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Student Name',
            'Matric Number',
            'Course',
            'Lecturer',
            'Captured At',
            'Status'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance['ID'],
            $attendance['Student Name'],
            $attendance['Matric Number'],
            $attendance['Course'],
            $attendance['Lecturer'],
            $attendance['Captured At'],
            $attendance['Status']
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
            'B' => 25,
            'C' => 20,
            'D' => 25,
            'E' => 25,
            'F' => 20,
            'G' => 15,
        ];
    }
}
