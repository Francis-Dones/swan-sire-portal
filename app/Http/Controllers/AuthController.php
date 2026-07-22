<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function showLogin()
    {
        // Check if already logged in
        if (session('api_token') && session('api_token_expires_at') > now()) {
            return redirect()->route('dashboard');
        }
        
        // Clear expired session
        if (session('api_token') && session('api_token_expires_at') <= now()) {
            $this->clearSession();
            return view('auth.login')->with('warning', 'Your session has expired. Please login again.');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('Login attempt started', [
            'username' => $request->login,
            'api_url' => config('services.external_api.base_url'),
            'use_fallback' => env('USE_LOCAL_FALLBACK', false)
        ]);

        // Call external API for authentication
        $result = $this->api->login($request->login, $request->password);

        Log::info('API Login result', [
            'success' => $result['success'] ?? false,
            'has_token' => isset($result['data']['token']),
            'error' => $result['error'] ?? null,
            'status' => $result['status'] ?? null
        ]);

        if ($result['success'] && isset($result['data']['token']) && !empty($result['data']['token'])) {
            $apiData = $result['data'];
            
            // Get token expiry from API response or set default
            $tokenExpiry = isset($apiData['expires_in']) 
                ? now()->addSeconds($apiData['expires_in'])
                : now()->addMinutes(config('session.lifetime', 120));
            
            // Store API token and user info in session
            session([
                'api_token' => $apiData['token'],
                'api_token_expires_at' => $tokenExpiry,
                'api_user' => $apiData['user'] ?? [],
                'user_name' => $apiData['user']['username'] ?? $apiData['user']['name'] ?? $request->login,
                'user_email' => $apiData['user']['email'] ?? '',
                'login_time' => now(),
            ]);
            
            // Force save session
            session()->save();
            
            Log::info('User logged in successfully via EXTERNAL API', [
                'session_id' => session()->getId(),
                'username' => $request->login,
                'token_expiry' => $tokenExpiry,
                'ip' => $request->ip()
            ]);
            
            // Regenerate session ID for security
            $request->session()->regenerate();
            
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        // Optional fallback for development only
        $useLocalFallback = env('USE_LOCAL_FALLBACK', false);
        
        if ($useLocalFallback) {
            Log::info('Attempting local database fallback');
            
            $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = User::where($loginField, $request->login)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user, $request->boolean('remember'));
                
                $tokenExpiry = now()->addMinutes(config('session.lifetime', 120));
                
                session([
                    'api_token' => 'local_auth',
                    'api_token_expires_at' => $tokenExpiry,
                    'user_name' => $user->username ?? $user->name ?? $request->login,
                    'user_email' => $user->email,
                    'user_id' => $user->id,
                    'login_time' => now(),
                ]);
                
                session()->save();
                $request->session()->regenerate();
                
                Log::info('Local user logged in (fallback)', [
                    'session_id' => session()->getId(),
                    'user_id' => $user->id,
                ]);
                
                return redirect()->route('dashboard')->with('success', 'Welcome back! (Local Mode)');
            }
        }

        // Build error message
        $errorMessage = 'Unable to authenticate. ';
        
        if (isset($result['error'])) {
            $errorMessage .= $result['error'];
        } elseif (isset($result['status'])) {
            $errorMessage .= 'API returned status: ' . $result['status'];
        } else {
            $errorMessage .= 'External API connection failed.';
        }
        
        Log::warning('Failed login attempt', [
            'login' => $request->login,
            'ip' => $request->ip(),
            'error' => $errorMessage
        ]);

        return back()->withErrors([
            'login' => $errorMessage,
        ])->withInput($request->only('login'));
    }

    public function logout(Request $request)
    {
        // Call API logout if token exists
        if (session('api_token') && session('api_token') !== 'local_auth') {
            try {
                $this->api->logout();
                Log::info('API logout successful');
            } catch (\Exception $e) {
                Log::error('API logout failed: ' . $e->getMessage());
            }
        }

        Auth::logout();
        $this->clearSession();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::info('User logged out', ['session_id' => session()->getId()]);
        
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
    
    /**
     * Clear all session data
     */
    private function clearSession()
    {
        session()->forget([
            'api_token', 
            'api_token_expires_at',
            'api_user', 
            'user_name', 
            'user_email',
            'user_id',
            'login_time'
        ]);
    }
    
    /**
     * Check if session is valid
     */
    public function checkSession(Request $request)
    {
        if (!session('api_token')) {
            return response()->json(['valid' => false, 'message' => 'No session found']);
        }
        
        if (session('api_token_expires_at') && now()->greaterThan(session('api_token_expires_at'))) {
            return response()->json(['valid' => false, 'message' => 'Session expired']);
        }
        
        return response()->json([
            'valid' => true,
            'expires_at' => session('api_token_expires_at'),
            'user_name' => session('user_name')
        ]);
    }
}