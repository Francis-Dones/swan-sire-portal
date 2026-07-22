<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'tb_exam';

    protected $fillable = [
        'exam_id', 'vessel_name', 'person_in_charge',
        'submitted_date', 'submitted_by', 'email', 'answers',
    ];

    protected $casts = [
        'answers' => 'array',
        'submitted_date' => 'datetime',
    ];
    
    // Accessor to ensure answers is always an array
    public function getAnswersAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($value) ? $value : [];
    }
    
    // Mutator to ensure answers is stored as JSON
    public function setAnswersAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['answers'] = json_encode($value);
        } elseif (is_string($value)) {
            // Validate if it's valid JSON
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['answers'] = $value;
            } else {
                $this->attributes['answers'] = json_encode(['error' => 'Invalid JSON', 'original' => $value]);
            }
        } else {
            $this->attributes['answers'] = json_encode([]);
        }
    }
}