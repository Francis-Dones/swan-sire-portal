<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ImagesExport implements FromArray, WithHeadings, WithMapping
{
    protected $images;

    public function __construct(array $images)
    {
        $this->images = $images;
    }

    public function array(): array
    {
        return $this->images;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Image Name',
            'Vessel ID',
            'Inspection ID',
            'Inspection Type',
            'Description',
            'Created At'
        ];
    }

    public function map($image): array
    {
        return [
            $image['id'] ?? $image['image_id'] ?? 'N/A',
            $image['image_name'] ?? $image['name'] ?? 'N/A',
            $image['vessel_id'] ?? $image['vesselId'] ?? 'N/A',
            $image['inspection_id'] ?? $image['inspectionId'] ?? 'N/A',
            $image['inspection_type'] ?? $image['inspectionType'] ?? 'N/A',
            $image['description'] ?? 'N/A',
            $image['created_at'] ?? $image['createdAt'] ?? 'N/A'
        ];
    }
}