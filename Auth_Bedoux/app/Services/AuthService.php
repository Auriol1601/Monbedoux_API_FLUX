<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Inscription d'un utilisateur
     */
    public function register(array $data): array
    {
        // Vérifier si l'email existe
        if ($this->userRepository->emailExists($data['Email'])) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà utilisé'
            ];
        }

        // Préparer les données utilisateur
        $userData = [
            'id_user' => Str::uuid(),
            'Name' => $data['Name'],
            'Email' => $data['Email'],
            'Password' => Hash::make($data['Password']),
            'LieuNaissance' => $data['LieuNaissance'],
            'DateNaissance' => $data['DateNaissance'],
            'date_flux' => now()->format('Y-m-d H:i:s'),
            'Nom_Emploi' => $data['Nom_Emploi'],
            'Secteurs_Activite' => $data['Secteurs_Activite'],
            'Type_Contrat' => $data['Type_Contrat'],
            'Duree_Contrat' => $data['Duree_Contrat'] ?? null,
        ];

        // Créer l'utilisateur
        if ($this->userRepository->createUser($userData)) {
            $user = $this->userRepository->findUserById($userData['id_user']);
            $token = $this->generateToken($user);

            return [
                'success' => true,
                'message' => 'Inscription réussie',
                'user' => $this->formatUser($user),
                'token' => $token
            ];
        }

        return [
            'success' => false,
            'message' => 'Erreur lors de l\'inscription'
        ];
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(string $email, string $password): array
    {
        // Récupérer l'utilisateur
        $user = $this->userRepository->findUserByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email non trouvé'
            ];
        }

        // Vérifier le mot de passe
        if (!Hash::check($password, $user->Password)) {
            return [
                'success' => false,
                'message' => 'Mot de passe incorrect'
            ];
        }

        // Mettre à jour la dernière connexion
        $this->userRepository->updateLastLogin($user->id_user);

        // Générer le token
        $token = $this->generateToken($user);

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $this->formatUser($user),
            'token' => $token
        ];
    }

    /**
     * Déconnexion (blacklister le token)
     */
    public function logout(string $token): array
    {
        // Ajouter le token à la blacklist
        Cache::put('blacklist_' . hash('sha256', $token), true, now()->addHours(24));

        return [
            'success' => true,
            'message' => 'Déconnexion réussie'
        ];
    }

    /**
     * Vérifier un token et récupérer l'utilisateur
     */
    public function verifyToken(string $token): ?object
    {
        // Vérifier si le token est blacklisté
        if (Cache::has('blacklist_' . hash('sha256', $token))) {
            return null;
        }

        // Récupérer l'utilisateur depuis le cache du token
        $userId = Cache::get('token_' . hash('sha256', $token));
        
        if (!$userId) {
            return null;
        }

        return $this->userRepository->findUserById($userId);
    }

    /**
     * Générer un token simple
     */
    private function generateToken(object $user): string
    {
        $tokenData = [
            'user_id' => $user->id_user,
            'email' => $user->Email,
            'time' => time()
        ];

        $token = base64_encode(json_encode($tokenData)) . '.' . Str::random(40);

        // Stocker le token en cache (expire dans 24h)
        Cache::put('token_' . hash('sha256', $token), $user->id_user, now()->addHours(24));

        return $token;
    }

    /**
     * Formater les données utilisateur (enlever le mot de passe)
     */
    private function formatUser(object $user): array
    {
        return [
            'id_user' => $user->id_user,
            'Name' => $user->Name,
            'Email' => $user->Email,
            'LieuNaissance' => $user->LieuNaissance,
            'DateNaissance' => $user->DateNaissance,
            'Nom_Emploi' => $user->Nom_Emploi,
            'Secteurs_Activite' => $user->Secteurs_Activite,
            'Type_Contrat' => $user->Type_Contrat,
            'Duree_Contrat' => $user->Duree_Contrat,
            'created_at' => $user->created_at
        ];
    }
}