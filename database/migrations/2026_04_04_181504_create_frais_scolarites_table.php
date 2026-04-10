<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frais_scolarite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->string('libelle'); // ex: "Frais d'inscription", "Frais de scolarité mensuel"
            $table->decimal('montant', 12, 0); // En FCFA
            $table->string('devise', 10)->default('FCFA');
            $table->enum('periodicite', ['unique', 'mensuel', 'trimestriel', 'annuel'])->default('annuel');
            $table->text('notes')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frais_scolarite');
    }
};