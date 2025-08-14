<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthService
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function register(array $data)
    {
        try {
            if (isset($data['image'])) {
                $data['image'] = $this->uploadImage($data['image']);
            }
            $data['password'] = Hash::make($data['password']);
            return $this->users->create($data);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la création du compte: ' . $e->getMessage());
        }
    }

    public function login(array $credentials)
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return false;
            }
            return $token;
        } catch (JWTException $e) {
            throw new JWTException('Erreur lors de la génération du token: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (TokenExpiredException $e) {
            // Token already expired, no need to invalidate
            throw new TokenExpiredException('Token déjà expiré');
        } catch (TokenInvalidException $e) {
            // Token already invalid, no need to invalidate
            throw new TokenInvalidException('Token déjà invalide');
        } catch (JWTException $e) {
            throw new JWTException('Erreur lors de la déconnexion: ' . $e->getMessage());
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                throw new JWTException('Aucun token trouvé pour le rafraîchissement');
            }
            
            return JWTAuth::refresh($token);
        } catch (TokenExpiredException $e) {
            throw new TokenExpiredException('Token expiré, impossible de le rafraîchir');
        } catch (TokenInvalidException $e) {
            throw new TokenInvalidException('Token invalide, impossible de le rafraîchir');
        } catch (JWTException $e) {
            throw new JWTException('Erreur lors du rafraîchissement du token: ' . $e->getMessage());
        }
    }

    public function uploadImage($image)
    {
        try {
            $path = $image->store('users', 'public');
            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
        }
    }
} 