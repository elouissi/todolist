<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

       public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $user = $this->authService->register($request->all());
            
            // Marquer l'utilisateur comme actif
            $user->markAsActive();
            
            return $this->successResponse([
                'user' => $user,
                'message' => 'Bienvenue sur TaskFlow Pro ! Votre compte a été créé avec succès.'
            ], 'Inscription réussie', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Erreur lors de la création de votre compte TaskFlow Pro.');
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $result = $this->authService->login($request->only('email', 'password'));
            
            if (!$result) {
                return $this->authErrorResponse('invalid_credentials');
            }

            // Marquer l'utilisateur comme actif
            Auth::user()->markAsActive();

            return $this->successResponse([
                'token' => $result,
                'user' => Auth::user(),
                'message' => 'Bon retour sur TaskFlow Pro !'
            ], 'Connexion réussie');
        } catch (\Exception $e) {
            return $this->authErrorResponse('authentication_error');
        }
    }

 

    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->successResponse(null, 'Déconnexion réussie');
        } catch (JWTException $e) {
            return $this->authErrorResponse('logout_error');
        }
    }

    public function refresh()
    {
        try {
            $token = $this->authService->refresh();
            return $this->successResponse(['token' => $token], 'Token rafraîchi avec succès');
        } catch (TokenExpiredException $e) {
            return $this->authErrorResponse('token_expired');
        } catch (TokenInvalidException $e) {
            return $this->authErrorResponse('token_invalid');
        } catch (JWTException $e) {
            return $this->authErrorResponse('refresh_error');
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->authErrorResponse('user_not_found');
            }

            return $this->successResponse($user, 'Profil récupéré avec succès');
        } catch (JWTException $e) {
            return $this->authErrorResponse('profile_error');
        }
    }
} 