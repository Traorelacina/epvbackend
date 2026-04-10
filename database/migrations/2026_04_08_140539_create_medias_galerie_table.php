<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias_galerie', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->string('fichier'); // chemin du fichier image/video
            $table->string('type')->default('image'); // image, video
            $table->string('alt')->nullable(); // texte alternatif
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->string('categorie')->nullable(); // catégorie du média
            $table->string('taille')->nullable(); // taille du fichier
            $table->string('mime_type')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('est_publie')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour les recherches
            $table->index('type');
            $table->index('categorie');
            $table->index('est_publie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias_galerie');
    }
};