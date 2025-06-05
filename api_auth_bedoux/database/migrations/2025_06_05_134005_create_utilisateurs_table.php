<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create("USER_", function (Blueprint $table) {
            $table->id();
            $table->string("Name", 50);
            $table->string("Email", 255)->unique();
            $table->string("Password");
            $table->string("LieuNaissance", 95);
            $table->date("DateNaissance");
            $table->string("Nom_Emploi", 95);
            $table->string("Secteurs_Activite", 150);
            $table->string("Type_Contrat", 150);
            $table->string("Duree_Contrat", 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("utilisateurs");
    }
};
