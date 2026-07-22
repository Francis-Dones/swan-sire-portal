@extends('layouts.app')

@section('title', 'Image Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('images.index') }}" class="text-blue-500 hover:underline mb-4 inline-flex items-center gap-2">
        <i class="ti ti-arrow-left"></i> Back to Images
    </a>
    
    @if(isset($image))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Image Display - Using AJAX to fetch image data -->
            <div>
                @php
                    $imageId = $image['id'] ?? $image['image_id'] ?? null;
                @endphp
                
                @if($imageId)
                <div x-data="{ 
                    loaded: false, 
                    imgData: '', 
                    error: false,
                    downloadImage(base64Data, fileName) {
                        const link = document.createElement('a');
                        link.href = 'data:image/jpeg;base64,' + base64Data;
                        link.download = fileName.endsWith('.jpg') ? fileName : fileName + '.jpg';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }"
                     x-init="() => {
                         fetch('{{ route('images.data', $imageId) }}')
                             .then(res => res.json())
                             .then(data => {
                                 if (data.success && data.image_data) {
                                     imgData = data.image_data;
                                     loaded = true;
                                 } else {
                                     error = true;
                                 }
                             })
                             .catch(() => { error = true; });
                     }">
                    
                    <!-- Image Display Area -->
                    <div class="relative">
                        <template x-if="loaded && imgData">
                            <div>
                                <img :src="'data:image/jpeg;base64,' + imgData" 
                                     alt="{{ $image['image_name'] ?? $image['name'] ?? 'Image' }}"
                                     class="w-full rounded-lg shadow-md"
                                     id="detailImage">
                                
                                <!-- Download Button Overlay -->
                                <button @click="downloadImage(imgData, '{{ $image['image_name'] ?? $image['name'] ?? 'image' }}')" 
                                        class="absolute top-4 right-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg transition flex items-center gap-2">
                                    <i class="ti ti-download"></i> Download JPG
                                </button>
                            </div>
                        </template>
                        <template x-if="!loaded && !error">
                            <div class="bg-gray-100 dark:bg-gray-700 h-96 flex flex-col items-center justify-center rounded-lg">
                                <svg class="animate-spin h-16 w-16 text-blue-500" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <p class="mt-4 text-gray-500 dark:text-gray-400">Loading image...</p>
                            </div>
                        </template>
                        <template x-if="error">
                            <div class="bg-gray-100 dark:bg-gray-700 h-96 flex flex-col items-center justify-center rounded-lg">
                                <i class="ti ti-photo-off text-6xl text-gray-400"></i>
                                <p class="text-gray-500 dark:text-gray-400 mt-2">No image data available</p>
                                <p class="text-xs text-gray-400 mt-1">Image ID: {{ $imageId }}</p>
                                
                                <!-- Debug info -->
                                @if(config('app.debug'))
                                <div class="mt-4 p-4 bg-gray-200 dark:bg-gray-600 rounded text-xs text-left w-full max-w-md">
                                    <p class="font-bold">Debug Info:</p>
                                    <p>Image ID: {{ $imageId }}</p>
                                    <p>Available keys: {{ implode(', ', array_keys($image)) }}</p>
                                    <p>Has image_data: {{ isset($image['image_data']) ? 'Yes (length: ' . strlen($image['image_data']) . ')' : 'No' }}</p>
                                    <p>Has base64_image: {{ isset($image['base64_image']) ? 'Yes' : 'No' }}</p>
                                    <p>Has image: {{ isset($image['image']) ? 'Yes' : 'No' }}</p>
                                </div>
                                @endif
                            </div>
                        </template>
                    </div>
                </div>
                @else
                <div class="bg-gray-100 dark:bg-gray-700 h-96 flex flex-col items-center justify-center rounded-lg">
                    <i class="ti ti-photo-off text-6xl text-gray-400"></i>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">No image ID available</p>
                </div>
                @endif
            </div>
            
            <!-- Image Details -->
            <div>
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="ti ti-photo text-blue-500"></i>
                    {{ $image['image_name'] ?? $image['name'] ?? 'Image Details' }}
                </h2>
                
                <div class="space-y-3">
                    <!-- Vessel Name -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-ship text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Vessel Name</dt>
                            <dd class="font-medium">{{ $image['vessel_name'] ?? $image['vesselName'] ?? $image['vessel']['name'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Vessel ID -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-id text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Vessel ID</dt>
                            <dd class="font-medium">{{ $image['vessel_id'] ?? $image['vesselId'] ?? $image['vessel']['id'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Inspection ID -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-clipboard-list text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Inspection ID</dt>
                            <dd class="font-medium">{{ $image['inspection_id'] ?? $image['inspectionId'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Inspection Date -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-calendar text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Inspection Date</dt>
                            <dd class="font-medium">
                                @php
                                    $date = $image['inspection_date'] ?? $image['inspectionDate'] ?? null;
                                    if ($date) {
                                        try {
                                            $timestamp = strtotime($date);
                                            if ($timestamp !== false) {
                                                echo date('F d, Y', $timestamp);
                                            } else {
                                                echo $date;
                                            }
                                        } catch (\Exception $e) {
                                            echo $date;
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                @endphp
                            </dd>
                        </div>
                    </div>
                    
                    <!-- Inspection Location -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-map-pin text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Inspection Location</dt>
                            <dd class="font-medium">{{ $image['inspection_loc'] ?? $image['inspectionLoc'] ?? $image['location'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Inspector -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-user text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Inspector</dt>
                            <dd class="font-medium">{{ $image['inspector_name'] ?? $image['inspectorName'] ?? $image['inspector']['name'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Inspection Type -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-tag text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Inspection Type</dt>
                            <dd>
                                @if(!empty($image['inspection_type']) || !empty($image['inspectionType']))
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-sm">
                                    {{ $image['inspection_type'] ?? $image['inspectionType'] }}
                                </span>
                                @else
                                <span class="text-gray-400">N/A</span>
                                @endif
                            </dd>
                        </div>
                    </div>
                    
                    <!-- Remarks -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-message text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Remarks</dt>
                            <dd class="font-medium">{{ $image['remarks'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                    
                    <!-- Image ID -->
                    <div class="flex items-start gap-2">
                        <i class="ti ti-hash text-gray-400 mt-1"></i>
                        <div>
                            <dt class="font-semibold text-sm text-gray-500">Image ID</dt>
                            <dd class="font-medium text-sm text-gray-500">{{ $image['id'] ?? $image['image_id'] ?? 'N/A' }}</dd>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-6 pt-4 border-t flex flex-wrap gap-3">
                    <a href="{{ route('images.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition flex items-center gap-2">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                    
                    <!-- Download Button (Alternative location) -->
                    @if($imageId)
                    <div x-data="{
                        downloadImage() {
                            fetch('{{ route('images.data', $imageId) }}')
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success && data.image_data) {
                                        const link = document.createElement('a');
                                        link.href = 'data:image/jpeg;base64,' + data.image_data;
                                        link.download = '{{ $image['image_name'] ?? $image['name'] ?? 'image' }}.jpg';
                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);
                                    } else {
                                        alert('Failed to download image');
                                    }
                                })
                                .catch(() => {
                                    alert('Error downloading image');
                                });
                        }
                    }">
                        <button @click="downloadImage()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition flex items-center gap-2">
                            <i class="ti ti-download"></i> Download JPG
                        </button>
                    </div>
                    @endif
                    
                    <form action="{{ route('images.destroy', $image['id'] ?? $image['image_id'] ?? 0) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition flex items-center gap-2">
                            <i class="ti ti-trash"></i> Delete Image
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
        <i class="ti ti-alert-circle text-4xl text-yellow-500"></i>
        <p class="mt-2 text-yellow-700">Image not found. The image may have been deleted or the ID is invalid.</p>
        <a href="{{ route('images.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Back to Images
        </a>
    </div>
    @endif
</div>

<style>
    dt {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.875rem;
    }
    dd {
        font-weight: 500;
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection