<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horaires', function (Blueprint $table) {
            $table->id();
            $table->string('niveau'); // ex: "Maternelle", "Primaire", "Direction"
            $table->string('periode'); // ex: "Matin", "Après-midi", "Journée entière"
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('jours')->nullable(); // ex: "Lundi au Vendredi"
            $table->text('notes')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horaires');
    }
};