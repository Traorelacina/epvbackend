<?php
// database/migrations/2026_04_06_000002_create_equipes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('poste'); // Directeur, Enseignant, etc.
            $table->string('role')->nullable(); // Rôle spécifique
            
            // Photo
            $table->string('photo')->nullable();
            
            // Biographie
            $table->text('bio')->nullable();
            $table->text('description')->nullable();
            
            // Contact
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            
            // Informations pédagogiques
            $table->string('matiere')->nullable(); // Matière enseignée
            $table->string('classe')->nullable(); // Classe responsable
            $table->string('diplome')->nullable(); // Diplôme
            
            // Réseaux sociaux
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            
            // Organisation
            $table->integer('ordre')->default(0);
            $table->boolean('affiche')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('affiche');
            $table->index('ordre');
            $table->index('poste');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipes');
    }
};