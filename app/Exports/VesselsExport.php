<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VesselsExport implements FromArray, WithHeadings, WithStyles
{
    protected $vessels;

    public function __construct(array $vessels)
    {
        $this->vessels = $vessels;
    }

    public function array(): array
    {
        return $this->vessels;
    }

    public function headings(): array
    {
        return [
            'Vessel Name',
            'Total Exams',
            'Last Inspection',
            'Persons In Charge'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}