<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $error, string $message, string $code = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $error,
            'message' => $message,
        ];

        if ($code) {
            $response['code'] = $code;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get error message from config
     */
    protected function getErrorMessage(string $key, string $type = 'auth'): array
    {
        return config("errors.{$type}.{$key}", [
            'error' => 'Erreur',
            'message' => 'Une erreur s\'est produite.',
            'code' => 'UNKNOWN_ERROR'
        ]);
    }

    /**
     * Get HTTP error message from config
     */
    protected function getHttpErrorMessage(int $statusCode): array
    {
        return config("errors.http_codes.{$statusCode}", [
            'error' => 'Erreur',
            'message' => 'Une erreur s\'est produite.',
            'code' => 'HTTP_ERROR'
        ]);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        $errorConfig = $this->getErrorMessage('validation', 'validation');
        
        return response()->json([
            'success' => false,
            'error' => $errorConfig['error'],
            'message' => $errorConfig['message'],
            'code' => $errorConfig['code'],
            'errors' => $errors
        ], 422);
    }

    /**
     * Authentication error response
     */
    protected function authErrorResponse(string $key): JsonResponse
    {
        $errorConfig = $this->getErrorMessage($key, 'auth');
        
        return response()->json([
            'success' => false,
            'error' => $errorConfig['error'],
            'message' => $errorConfig['message'],
            'code' => $errorConfig['code']
        ], 401);
    }

    /**
     * Not found error response
     */
    protected function notFoundResponse(string $message = null): JsonResponse
    {
        $errorConfig = config('errors.not_found');
        
        return response()->json([
            'success' => false,
            'error' => $errorConfig['error'],
            'message' => $message ?: $errorConfig['message'],
            'code' => $errorConfig['code']
        ], 404);
    }

    /**
     * Server error response
     */
    protected function serverErrorResponse(string $message = null): JsonResponse
    {
        $errorConfig = config('errors.server');
        
        return response()->json([
            'success' => false,
            'error' => $errorConfig['error'],
            'message' => $message ?: $errorConfig['message'],
            'code' => $errorConfig['code']
        ], 500);
    }
} 