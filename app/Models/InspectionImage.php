<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionImage extends Model
{
    protected $table = 'inspection_images';

    protected $fillable = [
        'vessel_id', 
        'inspection_id', 
        'image_name',
        'image_data', 
        'image_mime_type', 
        'inspection_type',
        'inspection_loc',
        'inspector_name',
        'remarks',
        'detection_status',
        'detection_conf',
        'is_active',
        'inspection_date',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['image_data'];
    
    // Accessor to get vessel name (you may need to implement this based on your vessel relationship)
    public function getVesselNameAttribute()
    {
        // If you have a vessel relationship, use it here
        // Otherwise, you might need to fetch from vessel table
        return $this->vessel_id ?? 'N/A';
    }
}