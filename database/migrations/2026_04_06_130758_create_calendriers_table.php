<?php
// database/migrations/2026_04_06_000001_create_calendriers_table.php

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
        Schema::create('calendriers', function (Blueprint $table) {
            $table->id();
            
            // Champs principaux
            $table->string('titre');
            $table->text('description')->nullable();
            
            // Dates
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            
            // Type d'événement (vacances, examen, evenement, rentree, etc.)
            $table->string('type')->default('evenement');
            
            // Couleur pour l'affichage dans le calendrier (optionnelle)
            $table->string('couleur')->nullable();
            
            // Statut
            $table->boolean('actif')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('actif');
            $table->index('type');
            $table->index('date_debut');
            $table->index(['actif', 'date_debut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendriers');
    }
};