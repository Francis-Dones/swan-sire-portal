<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.external_api.base_url', 'https://api.sire2.swan-manila.com/api');
        $this->timeout = config('services.external_api.timeout', 30);
        
        Log::info('ApiService initialized', [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout
        ]);
    }

    /**
     * Get headers for API request
     */
    protected function getHeaders(): array
    {
        $token = session('api_token');
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        
        if ($token && $token !== 'local_auth') {
            $headers['Authorization'] = "Bearer {$token}";
        }
        
        return $headers;
    }

    /**
     * Make GET request to external API
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $url = $this->buildUrl($endpoint);
            
            Log::debug('API GET request', ['url' => $url, 'params' => $params]);
            
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->get($url, $params);

            return $this->formatResponse($response);
            
        } catch (\Exception $e) {
            Log::error("API GET Error: {$endpoint} - " . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Make POST request to external API
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $url = $this->buildUrl($endpoint);
            
            Log::debug('API POST request', [
                'url' => $url, 
                'data' => array_keys($data) // Don't log passwords
            ]);
            
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->post($url, $data);

            return $this->formatResponse($response);
            
        } catch (\Exception $e) {
            Log::error("API POST Error: {$endpoint} - " . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Make PUT request to external API
     */
    public function put(string $endpoint, array $data = []): array
    {
        try {
            $url = $this->buildUrl($endpoint);
            
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->put($url, $data);

            return $this->formatResponse($response);
            
        } catch (\Exception $e) {
            Log::error("API PUT Error: {$endpoint} - " . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Make DELETE request to external API
     */
    public function delete(string $endpoint): array
    {
        try {
            $url = $this->buildUrl($endpoint);
            
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->delete($url);

            return $this->formatResponse($response);
            
        } catch (\Exception $e) {
            Log::error("API DELETE Error: {$endpoint} - " . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Authenticate user with external API
     */
    public function login(string $username, string $password): array
    {
        Log::info('Attempting external API login', [
            'url' => $this->buildUrl('login'),
            'username' => $username
        ]);
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout($this->timeout)
            ->post($this->buildUrl('login'), [
                'username' => $username,
                'password' => $password
            ]);
            
            Log::info('External API login response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Handle different response structures
                $token = $data['token'] ?? 
                        $data['access_token'] ?? 
                        $data['data']['token'] ?? 
                        null;
                
                $expiresIn = $data['expires_in'] ?? 
                            $data['expiresIn'] ?? 
                            $data['data']['expires_in'] ?? 
                            config('session.lifetime', 120) * 60;
                
                $user = $data['user'] ?? 
                       $data['data']['user'] ?? 
                       ['username' => $username, 'email' => null];
                
                return [
                    'success' => true,
                    'data' => [
                        'token' => $token,
                        'expires_in' => $expiresIn,
                        'user' => $user
                    ]
                ];
            }
            
            // Handle error responses
            $errorMessage = $response->json('message') ?? 
                           $response->json('error') ?? 
                           'Authentication failed with status: ' . $response->status();
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'status' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('External API login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Logout from external API
     */
    public function logout(): array
    {
        $token = session('api_token');
        
        if (!$token || $token === 'local_auth') {
            return ['success' => true];
        }
        
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(10)
                ->post($this->buildUrl('logout'));
                
            return ['success' => $response->successful()];
            
        } catch (\Exception $e) {
            Log::error('API logout error: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Get authenticated user info
     */
    public function getUser(): array
    {
        return $this->get('user');
    }

    /**
     * Build full URL
     */
    protected function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');
        return "{$baseUrl}/{$endpoint}";
    }

    /**
     * Format API response
     */
    protected function formatResponse($response): array
    {
        return [
            'success' => $response->successful(),
            'data' => $response->json(),
            'status' => $response->status(),
        ];
    }

    /**
     * Format error response
     */
    protected function errorResponse(string $error): array
    {
        return [
            'success' => false,
            'data' => null,
            'error' => $error,
            'status' => 500
        ];
    }

    // ============ INSPECTION IMAGES ============
    
    public function getInspectionImages(array $params = []): array
    {
        return $this->get('inspection-images', $params);
    }

    public function getInspectionImagesByVessel(int $vesselId): array
    {
        return $this->get("inspection-images/vessel/{$vesselId}");
    }

    public function getInspectionImagesByInspection(int $inspectionId): array
    {
        return $this->get("inspection-images/inspection/{$inspectionId}");
    }

    public function getInspectionImage(int $id): array
    {
        return $this->get("inspection-images/{$id}");
    }

    public function deleteInspectionImage(int $id): array
    {
        return $this->delete("inspection-images/{$id}");
    }

    // ============ EXAMS ============
    
    public function getExams(array $params = []): array
    {
        return $this->get('exams', $params);
    }

    public function getExam(int $id): array
    {
        return $this->get("exams/{$id}");
    }

    public function getExamsByVessel(string $vesselName): array
    {
        return $this->get("exams/vessel/{$vesselName}");
    }

    public function getExamsByPerson(string $personName): array
    {
        return $this->get("exams/person/{$personName}");
    }

    public function getExamsByDateRange(string $from, string $to): array
    {
        return $this->post('exams/date-range', ['from' => $from, 'to' => $to]);
    }

    public function createExam(array $data): array
    {
        return $this->post('exams', $data);
    }

    public function updateExam(int $id, array $data): array
    {
        return $this->put("exams/{$id}", $data);
    }

    public function deleteExam(int $id): array
    {
        return $this->delete("exams/{$id}");
    }
}