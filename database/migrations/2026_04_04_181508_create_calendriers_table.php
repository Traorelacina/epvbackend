<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chiffres_cles', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('valeur');
            $table->string('suffixe')->nullable(); // ex: "%", "ans", "+"
            $table->string('icone')->nullable(); // Nom icône (ex: "FaTrophy")
            $table->string('couleur', 7)->default('#1B4F8A');
            $table->text('description')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chiffres_cles');
    }
};