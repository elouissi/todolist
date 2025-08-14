<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the custom error messages used throughout
    | the application for better user experience and consistency.
    |
    */

    'auth' => [
        'token_expired' => [
            'error' => 'Token expiré',
            'message' => 'Votre session a expiré. Veuillez vous reconnecter.',
            'code' => 'TOKEN_EXPIRED'
        ],
        'token_invalid' => [
            'error' => 'Token invalide',
            'message' => 'Votre token d\'authentification est invalide.',
            'code' => 'TOKEN_INVALID'
        ],
        'token_blacklisted' => [
            'error' => 'Token révoqué',
            'message' => 'Votre session a été révoquée. Veuillez vous reconnecter.',
            'code' => 'TOKEN_BLACKLISTED'
        ],
        'token_missing' => [
            'error' => 'Token manquant',
            'message' => 'Token d\'authentification requis.',
            'code' => 'TOKEN_MISSING'
        ],
        'invalid_credentials' => [
            'error' => 'Identifiants invalides',
            'message' => 'L\'email ou le mot de passe est incorrect.',
            'code' => 'INVALID_CREDENTIALS'
        ],
        'user_not_found' => [
            'error' => 'Utilisateur non trouvé',
            'message' => 'Aucun utilisateur correspondant à ce token.',
            'code' => 'USER_NOT_FOUND'
        ],
        'authentication_error' => [
            'error' => 'Erreur d\'authentification',
            'message' => 'Une erreur s\'est produite lors de l\'authentification.',
            'code' => 'AUTHENTICATION_ERROR'
        ],
        'logout_error' => [
            'error' => 'Erreur lors de la déconnexion',
            'message' => 'Une erreur s\'est produite lors de la déconnexion.',
            'code' => 'LOGOUT_ERROR'
        ],
        'refresh_error' => [
            'error' => 'Erreur de rafraîchissement',
            'message' => 'Une erreur s\'est produite lors du rafraîchissement du token.',
            'code' => 'REFRESH_ERROR'
        ],
        'profile_error' => [
            'error' => 'Erreur d\'authentification',
            'message' => 'Une erreur s\'est produite lors de la récupération du profil.',
            'code' => 'PROFILE_ERROR'
        ],
    ],

    'validation' => [
        'error' => 'Erreur de validation',
        'message' => 'Les données fournies sont invalides.',
        'code' => 'VALIDATION_ERROR'
    ],

    'server' => [
        'error' => 'Erreur serveur',
        'message' => 'Une erreur inattendue s\'est produite.',
        'code' => 'SERVER_ERROR'
    ],

    'not_found' => [
        'error' => 'Non trouvé',
        'message' => 'La ressource demandée n\'a pas été trouvée.',
        'code' => 'NOT_FOUND'
    ],

    'forbidden' => [
        'error' => 'Accès interdit',
        'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.',
        'code' => 'FORBIDDEN'
    ],

    'too_many_requests' => [
        'error' => 'Trop de requêtes',
        'message' => 'Vous avez effectué trop de requêtes. Veuillez ralentir.',
        'code' => 'TOO_MANY_REQUESTS'
    ],

    'http_codes' => [
        400 => [
            'error' => 'Requête invalide',
            'message' => 'La requête envoyée est invalide.',
            'code' => 'BAD_REQUEST'
        ],
        401 => [
            'error' => 'Non autorisé',
            'message' => 'Veuillez vous authentifier pour accéder à cette ressource.',
            'code' => 'UNAUTHORIZED'
        ],
        403 => [
            'error' => 'Accès interdit',
            'message' => 'Vous n\'avez pas les permissions nécessaires.',
            'code' => 'FORBIDDEN'
        ],
        404 => [
            'error' => 'Non trouvé',
            'message' => 'Ressource non trouvée.',
            'code' => 'NOT_FOUND'
        ],
        405 => [
            'error' => 'Méthode non autorisée',
            'message' => 'La méthode HTTP utilisée n\'est pas autorisée.',
            'code' => 'METHOD_NOT_ALLOWED'
        ],
        422 => [
            'error' => 'Erreur de validation',
            'message' => 'Les données fournies sont invalides.',
            'code' => 'VALIDATION_ERROR'
        ],
        429 => [
            'error' => 'Trop de requêtes',
            'message' => 'Trop de requêtes. Veuillez ralentir.',
            'code' => 'TOO_MANY_REQUESTS'
        ],
        500 => [
            'error' => 'Erreur serveur',
            'message' => 'Erreur serveur interne.',
            'code' => 'INTERNAL_SERVER_ERROR'
        ],
        502 => [
            'error' => 'Erreur de passerelle',
            'message' => 'Erreur de passerelle.',
            'code' => 'BAD_GATEWAY'
        ],
        503 => [
            'error' => 'Service indisponible',
            'message' => 'Service temporairement indisponible.',
            'code' => 'SERVICE_UNAVAILABLE'
        ],
    ],
]; 