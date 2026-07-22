<?php
// app/Models/QuestionnaireResponse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    protected $table = 'questionnaire_responses';
    
    protected $fillable = ['exam_id', 'question_code', 'response', 'remarks'];
    
    protected $casts = [
        'response' => 'string',
    ];
    
    // No relationship to Exam model since exam data comes from API
}