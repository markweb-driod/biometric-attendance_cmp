<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'User',
            'Action',
            'Description',
            'IP Address',
            'User Agent',
            'Timestamp',
            'Type',
            'Severity'
        ];
    }

    public function map($log): array
    {
        return [
            $log['ID'],
            $log['User'],
            $log['Action'],
            $log['Description'],
            $log['IP Address'],
            $log['User Agent'],
            $log['Timestamp'],
            $log['Type'],
            $log['Severity']
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
            'C' => 20,
            'D' => 40,
            'E' => 15,
            'F' => 50,
            'G' => 20,
            'H' => 20,
            'I' => 15,
        ];
    }
}
