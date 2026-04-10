<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipe', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('role'); // ex: "Directrice", "Enseignante CP1"
            $table->string('classe')->nullable();
            $table->text('biographie')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('affiche')->default(true);
            $table->timestamps();

            $table->index(['affiche', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipe');
    }
};