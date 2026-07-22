<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionImage;
use Illuminate\Http\Request;

class InspectionImageApiController extends Controller
{
    public function index(Request $request)
    {
        $query = InspectionImage::query();

        if ($request->vessel_id) $query->where('vessel_id', $request->vessel_id);
        if ($request->inspection_id) $query->where('inspection_id', $request->inspection_id);
        if ($request->inspection_type) $query->where('inspection_type', $request->inspection_type);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function show($id)
    {
        $image = InspectionImage::withImageData()->findOrFail($id);
        $image->image_url = $image->image_url_attribute;
        return response()->json($image);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vessel_id'      => 'required|integer',
            'inspection_id'  => 'required|integer',
            'image_name'     => 'required|string',
            'inspection_type'=> 'required|string',
            'image_data'     => 'required|string',
            'image_mime_type'=> 'required|string',
        ]);

        $image = InspectionImage::create($request->all());
        return response()->json($image, 201);
    }

    public function destroy($id)
    {
        InspectionImage::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
