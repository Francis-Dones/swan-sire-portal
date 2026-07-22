<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (str_contains($e->getMessage(), 'Allowed memory size')) {
                Log::critical('Memory limit exhausted: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Check for memory error
        if (str_contains($e->getMessage(), 'Allowed memory size')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Memory limit exceeded. Please try with a smaller request.',
                    'success' => false
                ], 500);
            }
            
            if (view()->exists('errors.memory')) {
                return response()->view('errors.memory', [], 500);
            }
            
            return response()->view('errors.500', [], 500);
        }
        
        // Handle fatal errors
        if ($e instanceof \Symfony\Component\ErrorHandler\Error\FatalError) {
            Log::error('Fatal error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'A fatal error occurred. Please try again.',
                    'success' => false
                ], 500);
            }
            
            if (view()->exists('errors.fatal')) {
                return response()->view('errors.fatal', [], 500);
            }
        }
        
        return parent::render($request, $e);
    }
}