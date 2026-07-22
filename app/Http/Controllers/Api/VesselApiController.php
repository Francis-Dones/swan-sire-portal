<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionImage;
use Illuminate\Http\Request;

class VesselApiController extends Controller
{
    public function index()
    {
        $vessels = InspectionImage::selectRaw('vessel_id, COUNT(*) as total_images, COUNT(DISTINCT inspection_id) as total_inspections, MAX(created_at) as last_inspection')
            ->groupBy('vessel_id')->orderBy('vessel_id')->paginate(20);
        return response()->json($vessels);
    }

    public function inspections($id)
    {
        $inspections = InspectionImage::where('vessel_id', $id)
            ->selectRaw('inspection_id, inspection_type, COUNT(*) as image_count, MAX(created_at) as last_updated')
            ->groupBy('inspection_id', 'inspection_type')
            ->orderBy('last_updated', 'desc')
            ->get();
        return response()->json($inspections);
    }

    public function images($id)
    {
        return response()->json(InspectionImage::where('vessel_id', $id)->orderBy('created_at', 'desc')->paginate(20));
    }
}
