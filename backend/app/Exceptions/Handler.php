<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Handle JWT exceptions
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'error' => 'Token expiré',
                'message' => 'Votre session a expiré. Veuillez vous reconnecter.',
                'code' => 'TOKEN_EXPIRED'
            ], 401);
        }

        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'error' => 'Token invalide',
                'message' => 'Votre token d\'authentification est invalide.',
                'code' => 'TOKEN_INVALID'
            ], 401);
        }

        if ($exception instanceof TokenBlacklistedException) {
            return response()->json([
                'error' => 'Token révoqué',
                'message' => 'Votre session a été révoquée. Veuillez vous reconnecter.',
                'code' => 'TOKEN_BLACKLISTED'
            ], 401);
        }

        if ($exception instanceof JWTException) {
            return response()->json([
                'error' => 'Erreur d\'authentification',
                'message' => 'Une erreur s\'est produite lors de l\'authentification.',
                'code' => 'JWT_ERROR'
            ], 401);
        }

        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Erreur de validation',
                'message' => 'Les données fournies sont invalides.',
                'errors' => $exception->errors(),
                'code' => 'VALIDATION_ERROR'
            ], 422);
        }

        // Handle HTTP exceptions
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: $this->getDefaultMessage($statusCode);

            return response()->json([
                'error' => $this->getErrorTitle($statusCode),
                'message' => $message,
                'code' => 'HTTP_ERROR'
            ], $statusCode);
        }

        // Handle other exceptions
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Erreur serveur',
                'message' => config('app.debug') ? $exception->getMessage() : 'Une erreur inattendue s\'est produite.',
                'code' => 'SERVER_ERROR'
            ], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Get default message for HTTP status codes.
     */
    private function getDefaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Requête invalide.',
            401 => 'Non autorisé. Veuillez vous authentifier.',
            403 => 'Accès interdit. Vous n\'avez pas les permissions nécessaires.',
            404 => 'Ressource non trouvée.',
            405 => 'Méthode non autorisée.',
            422 => 'Les données fournies sont invalides.',
            429 => 'Trop de requêtes. Veuillez ralentir.',
            500 => 'Erreur serveur interne.',
            502 => 'Erreur de passerelle.',
            503 => 'Service indisponible.',
            default => 'Une erreur s\'est produite.',
        };
    }

    /**
     * Get error title for HTTP status codes.
     */
    private function getErrorTitle(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Requête invalide',
            401 => 'Non autorisé',
            403 => 'Accès interdit',
            404 => 'Non trouvé',
            405 => 'Méthode non autorisée',
            422 => 'Erreur de validation',
            429 => 'Trop de requêtes',
            500 => 'Erreur serveur',
            502 => 'Erreur de passerelle',
            503 => 'Service indisponible',
            default => 'Erreur',
        };
    }
} 