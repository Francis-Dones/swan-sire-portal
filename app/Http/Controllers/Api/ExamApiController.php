<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('exam_id', 'ilike', "%$s%")->orWhere('vessel_name', 'ilike', "%$s%")->orWhere('submitted_by', 'ilike', "%$s%");
            });
        }
        return response()->json($query->orderBy('submitted_date', 'desc')->paginate(20));
    }

    public function show($id)
    {
        return response()->json(Exam::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_id'         => 'required|string',
            'vessel_name'     => 'required|string',
            'person_in_charge'=> 'required|string',
            'submitted_by'    => 'required|string',
            'email'           => 'required|email',
        ]);
        return response()->json(Exam::create($request->all()), 201);
    }
}
