<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Exception;

class UserRepository
{
    /**
     * Récupérer un utilisateur par email
     */
    public function findUserByEmail(string $email): ?object
    {
        $sql = "SELECT * FROM USER_ WHERE Email = ?";
        $result = DB::select($sql, [$email]);

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function findUserById(string $id): ?object
    {
        $sql = "SELECT * FROM USER_ WHERE id_user = ?";
        $result = DB::select($sql, [$id]);

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Vérifier si un email existe déjà
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) as count FROM USER_ WHERE Email = ?";
        $result = DB::select($sql, [$email]);

        return $result[0]->count > 0;
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $userData): bool
    {
        $sql = "INSERT INTO USER_ (
            id_user, Name, Email, Password, LieuNaissance,
            date_flux, Nom_Emploi, Secteurs_Activite,
            Type_Contrat, Duree_Contrat, DateNaissance, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            return DB::insert($sql, [
                $userData["id_user"],
                $userData["Name"],
                $userData["Email"],
                $userData["Password"],
                $userData["LieuNaissance"],
                $userData["date_flux"],
                $userData["Nom_Emploi"],
                $userData["Secteurs_Activite"],
                $userData["Type_Contrat"],
                $userData["Duree_Contrat"],
                $userData["DateNaissance"],
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mettre à jour la date de dernière connexion
     */
    public function updateLastLogin(string $userId): bool
    {
        $sql = "UPDATE USER_ SET date_flux = NOW() WHERE id_user = ?";

        try {
            return DB::update($sql, [$userId]);
        } catch (Exception $e) {
            return false;
        }
    }
}
