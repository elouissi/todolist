<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JWTAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Utilisateur non trouvé',
                    'message' => 'Aucun utilisateur correspondant à ce token.',
                    'code' => 'USER_NOT_FOUND'
                ], 401);
            }
            
            return $next($request);
            
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token expiré',
                'message' => 'Votre session a expiré. Veuillez vous reconnecter.',
                'code' => 'TOKEN_EXPIRED'
            ], 401);
            
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token invalide',
                'message' => 'Votre token d\'authentification est invalide.',
                'code' => 'TOKEN_INVALID'
            ], 401);
            
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'error' => 'Token révoqué',
                'message' => 'Votre session a été révoquée. Veuillez vous reconnecter.',
                'code' => 'TOKEN_BLACKLISTED'
            ], 401);
            
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token manquant',
                'message' => 'Token d\'authentification requis.',
                'code' => 'TOKEN_MISSING'
            ], 401);
        }
    }
} 