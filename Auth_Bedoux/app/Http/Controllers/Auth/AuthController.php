<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Inscription d'un utilisateur
     */
    public function register(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'Name' => 'required|string|max:50',
            'Email' => 'required|email|max:255',
            'Password' => 'required|string|min:6',
            'LieuNaissance' => 'required|string|max:95',
            'DateNaissance' => 'required|date',
            'Nom_Emploi' => 'required|string|max:95',
            'Secteurs_Activite' => 'required|string|max:150',
            'Type_Contrat' => 'required|string|max:150',
            'Duree_Contrat' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        // Appel du service
        $result = $this->authService->register($request->all());

        // Retourner la réponse
        $statusCode = $result['success'] ? 201 : 400;
        return response()->json($result, $statusCode);
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email',
            'Password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email et mot de passe requis',
                'errors' => $validator->errors()
            ], 422);
        }

        // Appel du service
        $result = $this->authService->login(
            $request->input('Email'),
            $request->input('Password')
        );

        // Retourner la réponse
        $statusCode = $result['success'] ? 200 : 401;
        return response()->json($result, $statusCode);
    }

    /**
     * Déconnexion d'un utilisateur
     */
    public function logout(Request $request): JsonResponse
    {
        // Récupérer le token depuis le header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant'
            ], 400);
        }

        // Appel du service
        $result = $this->authService->logout($token);

        return response()->json($result);
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     */
    public function me(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant'
            ], 401);
        }

        $user = $this->authService->verifyToken($token);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id_user' => $user->id_user,
                'Name' => $user->Name,
                'Email' => $user->Email,
                'LieuNaissance' => $user->LieuNaissance,
                'DateNaissance' => $user->DateNaissance,
                'Nom_Emploi' => $user->Nom_Emploi,
                'Secteurs_Activite' => $user->Secteurs_Activite,
                'Type_Contrat' => $user->Type_Contrat,
                'Duree_Contrat' => $user->Duree_Contrat
            ]
        ]);
    }
}