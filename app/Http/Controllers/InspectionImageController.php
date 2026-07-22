<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ImagesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class InspectionImageController extends Controller
{
    protected $api;
    protected $perPage = 12;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        try {
            $currentPage = (int)$request->get('page', 1);
            $defaultVesselId = 1;
            
            $result = $this->api->getInspectionImagesByVessel($defaultVesselId);
            
            $allImages = [];
            $apiError = null;
            
            if ($result && isset($result['data'])) {
                $allImages = $result['data']['data'] ?? $result['data'] ?? [];
                if (!is_array($allImages)) {
                    $allImages = [];
                }
            }
            
            foreach ($allImages as &$img) {
                if (!isset($img['inspection_loc']) && isset($img['inspectionLoc'])) {
                    $img['inspection_loc'] = $img['inspectionLoc'];
                }
                if (!isset($img['inspection_loc']) && isset($img['location'])) {
                    $img['inspection_loc'] = $img['location'];
                }
                if (!isset($img['inspection_loc']) && isset($img['inspection_location'])) {
                    $img['inspection_loc'] = $img['inspection_location'];
                }
                if (!isset($img['vessel_name']) && isset($img['vesselName'])) {
                    $img['vessel_name'] = $img['vesselName'];
                }
            }
            
            $total = count($allImages);
            $lastPage = max(1, ceil($total / $this->perPage));
            
            $offset = ($currentPage - 1) * $this->perPage;
            $images = array_slice($allImages, $offset, $this->perPage);
            
            return view('images.index', compact('images', 'currentPage', 'lastPage', 'total', 'apiError'));
            
        } catch (\Exception $e) {
            Log::error('Image index error: ' . $e->getMessage());
            
            return view('images.index', [
                'images' => [],
                'currentPage' => 1,
                'lastPage' => 1,
                'total' => 0,
                'apiError' => 'Unable to load images: ' . $e->getMessage()
            ]);
        }
    }

    public function getImageData($id)
    {
        try {
            ini_set('memory_limit', '256M');
            
            $cacheKey = 'image_data_' . $id;
            $cachedData = Cache::get($cacheKey);
            
            if ($cachedData) {
                return response()->json($cachedData);
            }
            
            $result = $this->api->getInspectionImage($id);
            
            if (!$result || !isset($result['data'])) {
                return response()->json([
                    'success' => false,
                    'image_data' => '',
                    'error' => 'Image not found'
                ], 404);
            }
            
            $image = $result['data']['data'] ?? $result['data'] ?? null;
            
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'image_data' => '',
                    'error' => 'No image data available'
                ], 404);
            }
            
            $imageData = $image['image_data'] ?? null;
            
            if (empty($imageData)) {
                if (isset($image['base64_image'])) {
                    $imageData = $image['base64_image'];
                } elseif (isset($image['image'])) {
                    $imageData = $image['image'];
                } elseif (isset($image['data'])) {
                    $imageData = $image['data'];
                }
            }
            
            if (empty($imageData)) {
                return response()->json([
                    'success' => false,
                    'image_data' => '',
                    'error' => 'No image data available'
                ], 404);
            }
            
            $location = $image['inspection_loc'] ?? 
                       $image['inspectionLoc'] ?? 
                       $image['location'] ?? 
                       $image['inspection_location'] ?? 
                       'N/A';
            
            $responseData = [
                'success' => true,
                'image_data' => $imageData,
                'image_mime_type' => $image['image_mime_type'] ?? 'image/jpeg',
                'image_name' => $image['image_name'] ?? 'Image',
                'vessel_name' => $image['vessel_name'] ?? $image['vesselName'] ?? $image['vessel_id'] ?? 'N/A',
                'inspection_id' => $image['inspection_id'] ?? $image['inspectionId'] ?? 'N/A',
                'inspection_date' => $image['inspection_date'] ?? $image['inspectionDate'] ?? 'N/A',
                'inspection_loc' => $location,
                'inspector_name' => $image['inspector_name'] ?? $image['inspectorName'] ?? 'N/A',
                'remarks' => $image['remarks'] ?? 'N/A'
            ];
            
            Cache::put($cacheKey, $responseData, 300);
            
            return response()->json($responseData);
            
        } catch (\Exception $e) {
            Log::error('Get image data error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'image_data' => '',
                'error' => 'Failed to load image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            ini_set('memory_limit', '256M');
            
            $result = $this->api->getInspectionImage($id);
            
            $image = $result['data']['data'] ?? $result['data'] ?? null;

            if (!$image) {
                return redirect()->route('images.index')->with('error', 'Image not found.');
            }

            return view('images.show', compact('image'));
            
        } catch (\Exception $e) {
            Log::error('Show image error: ' . $e->getMessage());
            return redirect()->route('images.index')->with('error', 'Failed to load image: ' . $e->getMessage());
        }
    }

    /**
     * DOWNLOAD IMAGE - FIXED VERSION
     * Uses temporary file with chunked writing to avoid memory issues
     */
    public function download($id)
    {
        try {
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '512M');
            
            // Get image data from API
            $result = $this->api->getInspectionImage($id);
            
            if (!$result || !isset($result['data'])) {
                abort(404, 'Image not found');
            }
            
            $image = $result['data']['data'] ?? $result['data'] ?? null;
            
            if (!$image) {
                abort(404, 'Image data not available');
            }
            
            // Get image data
            $imageData = $image['image_data'] ?? 
                        $image['base64_image'] ?? 
                        $image['image'] ?? 
                        $image['data'] ?? 
                        null;
            
            if (empty($imageData)) {
                abort(404, 'No image data available');
            }
            
            $imageName = $image['image_name'] ?? $image['name'] ?? 'image';
            $mimeType = $image['image_mime_type'] ?? $image['mime_type'] ?? 'image/jpeg';
            
            // Clean filename
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $imageName);
            if (!str_ends_with($filename, '.jpg') && !str_ends_with($filename, '.jpeg')) {
                $filename .= '.jpg';
            }
            
            // Remove whitespace from base64
            $imageData = preg_replace('/\s+/', '', $imageData);
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            $handle = fopen($tempFile, 'wb');
            
            if (!$handle) {
                abort(500, 'Could not create temporary file');
            }
            
            // Write in small chunks to avoid memory issues
            $chunkSize = 1024 * 64; // 64KB chunks
            $offset = 0;
            $length = strlen($imageData);
            
            while ($offset < $length) {
                $chunk = substr($imageData, $offset, $chunkSize);
                $decoded = base64_decode($chunk);
                fwrite($handle, $decoded);
                $offset += $chunkSize;
                
                // Free memory immediately
                unset($chunk, $decoded);
            }
            
            fclose($handle);
            
            // Get file size
            $fileSize = filesize($tempFile);
            
            // Return download and delete temp file after send
            return response()->download($tempFile, $filename, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'no-cache, must-revalidate',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Download image error: ' . $e->getMessage());
            return back()->with('error', 'Unable to download image. Please try again.');
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $defaultVesselId = 1;
            $result = $this->api->getInspectionImagesByVessel($defaultVesselId);
            $images = $result['data']['data'] ?? $result['data'] ?? [];
            
            if (!is_array($images)) {
                $images = [];
            }
            
            foreach ($images as &$image) {
                unset($image['image_data']);
                if (isset($image['inspectionLoc']) && !isset($image['inspection_loc'])) {
                    $image['inspection_loc'] = $image['inspectionLoc'];
                }
                if (isset($image['location']) && !isset($image['inspection_loc'])) {
                    $image['inspection_loc'] = $image['location'];
                }
            }
            
            return Excel::download(new ImagesExport($images), 'inspection-images-' . now()->format('Y-m-d') . '.xlsx');
            
        } catch (\Exception $e) {
            Log::error('Export Excel error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $defaultVesselId = 1;
            $result = $this->api->getInspectionImagesByVessel($defaultVesselId);
            $images = $result['data']['data'] ?? $result['data'] ?? [];
            
            if (!is_array($images)) {
                $images = [];
            }
            
            foreach ($images as &$image) {
                unset($image['image_data']);
                if (isset($image['inspectionLoc']) && !isset($image['inspection_loc'])) {
                    $image['inspection_loc'] = $image['inspectionLoc'];
                }
                if (isset($image['location']) && !isset($image['inspection_loc'])) {
                    $image['inspection_loc'] = $image['location'];
                }
            }
            
            $pdf = Pdf::loadView('exports.images-pdf', compact('images'));
            return $pdf->download('inspection-images-' . now()->format('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Export PDF error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->api->deleteInspectionImage($id);

            if (isset($result['success']) && $result['success']) {
                Cache::forget('image_data_' . $id);
                return back()->with('success', 'Image deleted successfully.');
            }

            return back()->with('error', 'Failed to delete image.');
            
        } catch (\Exception $e) {
            Log::error('Delete image error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete image.');
        }
    }
}