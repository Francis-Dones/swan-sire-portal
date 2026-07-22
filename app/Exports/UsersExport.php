<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function array(): array
    {
        return array_map(function ($u) {
            return [
                $u['id'] ?? '',
                $u['username'] ?? '',
                $u['email'] ?? '',
                $u['address'] ?? '',
                $u['age'] ?? '',
                $u['token_type'] ?? '',
                isset($u['created_at']) ? date('Y-m-d', strtotime($u['created_at'])) : '',
            ];
        }, $this->users);
    }

    public function headings(): array
    {
        return ['ID', 'Username', 'Email', 'Address', 'Age', 'Token Type', 'Created At'];
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
        return 'Users';
    }
}
