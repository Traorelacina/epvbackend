<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('age_min')->nullable(); // ex: "6 mois", "2 ans 6 mois"
            $table->string('age_max')->nullable();
            $table->text('description');
            $table->text('programmes')->nullable(); // JSON ou texte riche
            $table->string('icone')->nullable();
            $table->string('couleur', 7)->default('#1B4F8A');
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('ordre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveaux');
    }
};