<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bedoux extends Model
{
    use HasFactory;

    protected $table = "Bedoux";
    protected $primaryKey = "id_bedoux";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id_bedoux",
        "Name",
        "Montant",
        "OrigineMontant",
        "id_user",
    ];

    protected $casts = [
        "Montant" => "decimal:2",
        "created_at" => "datetime",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "id_user", "id_user");
    }
}
