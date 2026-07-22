<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ExamsExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $exams;

    public function __construct(array $exams)
    {
        $this->exams = $exams;
    }

    public function array(): array
    {
        return array_map(function ($exam) {
            return [
                $exam['id'] ?? '',
                $exam['exam_id'] ?? '',
                $exam['vessel_name'] ?? '',
                $exam['person_in_charge'] ?? '',
                $exam['submitted_by'] ?? '',
                $exam['email'] ?? '',
                isset($exam['submitted_date']) ? date('Y-m-d H:i', strtotime($exam['submitted_date'])) : '',
                is_array($exam['answers'] ?? null) ? count($exam['answers']) : 0,
                isset($exam['created_at']) ? date('Y-m-d H:i', strtotime($exam['created_at'])) : '',
            ];
        }, $this->exams);
    }

    public function headings(): array
    {
        return ['ID', 'Exam ID', 'Vessel Name', 'Person In Charge', 'Submitted By', 'Email', 'Submitted Date', 'Answer Count', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1e3a5f']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Exams';
    }
}
