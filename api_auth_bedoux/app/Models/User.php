<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "USER_";
    protected $primaryKey = "id_user";
    public $incrementing = true;
    protected $keyType = "string";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "id_user",
        "Name",
        "Email",
        "Password",
        "LieuNaissance",
        "date_flux",
        "Nom_Emploi",
        "Secteurs_Activite",
        "Type_Contrat",
        "Duree_Contrat",
        "DateNaissance",
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ["Password"];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        "DateNaissance" => "date",
        "date_flux" => "datetime",
        "created_at" => "datetime",
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return "id_user";
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * Relation avec les Bedoux
     */
    public function bedoux()
    {
        return $this->hasMany(Bedoux::class, "id_user", "id_user");
    }
}
