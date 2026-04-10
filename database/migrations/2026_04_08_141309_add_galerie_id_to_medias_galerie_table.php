<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la table galeries existe avant de créer la clé étrangère
        if (Schema::hasTable('galeries')) {
            Schema::table('medias_galerie', function (Blueprint $table) {
                if (!Schema::hasColumn('medias_galerie', 'galerie_id')) {
                    $table->foreignId('galerie_id')->nullable()
                          ->after('id')
                          ->constrained('galeries')
                          ->onDelete('cascade');
                }
            });
        } else {
            // Si la table n'existe pas, on ajoute juste la colonne sans contrainte
            Schema::table('medias_galerie', function (Blueprint $table) {
                if (!Schema::hasColumn('medias_galerie', 'galerie_id')) {
                    $table->unsignedBigInteger('galerie_id')->nullable()->after('id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('medias_galerie', function (Blueprint $table) {
            if (Schema::hasColumn('medias_galerie', 'galerie_id')) {
                // Supprimer la clé étrangère si elle existe
                $table->dropForeign(['galerie_id']);
                $table->dropColumn('galerie_id');
            }
        });
    }
};