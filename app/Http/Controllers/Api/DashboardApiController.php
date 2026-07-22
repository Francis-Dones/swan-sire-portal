<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionImage;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_inspections' => InspectionImage::distinct('inspection_id')->count('inspection_id'),
            'total_vessels'     => InspectionImage::distinct('vessel_id')->count('vessel_id'),
            'total_images'      => InspectionImage::count(),
            'total_exams'       => Exam::count(),
            'total_users'       => User::count(),
        ]);
    }

    public function chartData()
    {
        $monthly = InspectionImage::selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, DATE_TRUNC('month', created_at) as month_date, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'month_date')
            ->orderBy('month_date')
            ->get();

        $typeDistribution = InspectionImage::selectRaw('inspection_type, COUNT(*) as count')
            ->groupBy('inspection_type')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'monthly'           => $monthly,
            'type_distribution' => $typeDistribution,
        ]);
    }

    public function recentActivity()
    {
        $recentExams = Exam::orderBy('submitted_date', 'desc')->limit(10)->get();
        $recentImages = InspectionImage::orderBy('created_at', 'desc')->limit(10)->get();

        return response()->json([
            'recent_exams'   => $recentExams,
            'recent_images'  => $recentImages,
        ]);
    }
}
