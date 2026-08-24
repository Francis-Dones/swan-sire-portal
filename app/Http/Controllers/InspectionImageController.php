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
        $vessels = $this->vesselOptions();
        $vesselName = $request->get('vessel_name');

        // Default to the first known image-vessel so the page isn't empty on first load.
        if (!$vesselName && !empty($vessels)) {
            $vesselName = $vessels[0]['name'];
        }

        try {
            $currentPage = (int)$request->get('page', 1);

            // Images are keyed on vessel_name (vessel_id is a dead column). A null
            // vessel_name lists everything the caller may see (shore = all).
            $result = $this->api->getInspectionImages(array_filter([
                'vessel_name' => $vesselName,
                'per_page' => $this->perPage,
                'page' => $currentPage,
            ]));

            $allImages = [];
            $apiError = null;

            if ($result && isset($result['data'])) {
                $allImages = $result['data']['data'] ?? $result['data'] ?? [];
                if (!is_array($allImages)) {
                    $allImages = [];
                }
            }

            foreach ($allImages as &$img) {
                unset($img['image_data'], $img['base64_image'], $img['image'], $img['full_base64']);
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
            unset($img);

            // Old API shape returns the full unpaginated list; new shape carries meta.
            $meta = $result['data']['meta'] ?? null;
            if ($meta) {
                $total = $meta['total'] ?? count($allImages);
                $lastPage = $meta['last_page'] ?? 1;
                $images = $allImages;
            } else {
                $total = count($allImages);
                $lastPage = max(1, (int)ceil($total / $this->perPage));
                $offset = ($currentPage - 1) * $this->perPage;
                $images = array_slice($allImages, $offset, $this->perPage);
            }

            return view('images.index', compact('images', 'currentPage', 'lastPage', 'total', 'apiError', 'vessels', 'vesselName'))
                ->with('selectedVesselName', $vesselName);

        } catch (\Exception $e) {
            Log::error('Image index error: ' . $e->getMessage());

            return view('images.index', [
                'images' => [],
                'currentPage' => 1,
                'lastPage' => 1,
                'total' => 0,
                'apiError' => 'Unable to load images: ' . $e->getMessage(),
                'vessels' => $vessels,
                'selectedVesselName' => $vesselName,
            ]);
        }
    }

    /**
     * Derive the vessel list (id + name) from exam records, the same way VesselController does.
     */
    protected function vesselOptions(): array
    {
        try {
            // Source the picker from the images themselves (their vessel_name),
            // not from exams -- exam vessel_names need not match uploaded photos,
            // and tb_exam has no vessel_id to key the old getByVessel path on.
            $result = $this->api->getInspectionImageVessels();
            $names = $result['data'] ?? [];
            if (!is_array($names)) {
                $names = [];
            }

            return collect($names)
                ->filter(fn ($n) => is_string($n) && trim($n) !== '')
                ->map(fn ($n) => ['id' => null, 'name' => $n])
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Vessel options error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolve a vessel_id for export endpoints from the request, or fall back to the first
     * known vessel instead of a hardcoded id.
     */
    protected function resolveVesselIdForExport(Request $request): ?int
    {
        $vesselId = $request->get('vessel_id');
        $vesselName = $request->get('vessel_name');

        if ($vesselId) {
            return (int) $vesselId;
        }

        $vessels = $this->vesselOptions();

        if ($vesselName) {
            $match = collect($vessels)->firstWhere('name', $vesselName);
            return $match['id'] ?? null;
        }

        return $vessels[0]['id'] ?? null;
    }

    /**
     * Stream a single image straight from the API without embedding base64 in the page.
     */
    public function raw($id)
    {
        $cacheKey = 'image_raw_' . $id;
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            $result = $this->api->getInspectionImage($id);
            $image = $result['data']['data'] ?? $result['data'] ?? null;

            if (!$image) {
                abort(404, 'Image not found');
            }

            $imageData = $image['image_data'] ?? $image['base64_image'] ?? $image['image'] ?? $image['data'] ?? null;

            if (empty($imageData)) {
                abort(404, 'No image data available');
            }

            $imageData = preg_replace('/^data:[^;]+;base64,/', '', $imageData);
            $imageData = preg_replace('/\s+/', '', $imageData);

            $cached = [
                'mime' => $image['image_mime_type'] ?? 'image/jpeg',
                'data' => $imageData,
            ];

            Cache::put($cacheKey, $cached, 300);
        }

        $binary = base64_decode($cached['data']);

        // Allow-list the served content type; anything not a recognized image type is
        // downgraded to a generic download so a stored non-image mime can't be reflected
        // as an executable content type.
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = in_array($cached['mime'], $allowedMimes, true) ? $cached['mime'] : 'application/octet-stream';

        // Vessel ownership is enforced server-side by the API (crew are scoped to their own
        // vessel); this proxy just streams what the API already authorized for the caller.
        return response($binary, 200)
            ->header('Content-Type', $mime)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Disposition', 'inline; filename="image-' . $id . '"')
            ->header('Cache-Control', 'private, max-age=300');
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
            $vesselId = $this->resolveVesselIdForExport($request);
            if (!$vesselId) {
                return back()->with('error', 'Select a vessel before exporting.');
            }
            $result = $this->api->getInspectionImagesByVessel($vesselId, array_filter([
                'vessel_name' => $request->get('vessel_name'),
            ]));
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
            $vesselId = $this->resolveVesselIdForExport($request);
            if (!$vesselId) {
                return back()->with('error', 'Select a vessel before exporting.');
            }
            $result = $this->api->getInspectionImagesByVessel($vesselId, array_filter([
                'vessel_name' => $request->get('vessel_name'),
            ]));
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