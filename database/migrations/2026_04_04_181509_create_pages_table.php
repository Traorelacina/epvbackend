<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique(); // ex: "accueil", "ecole", "pedagogie"
            $table->string('titre');
            $table->text('sous_titre')->nullable();
            $table->longText('contenu')->nullable();
            $table->string('image_hero')->nullable();
            $table->string('meta_titre')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};