<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medias_galerie', function (Blueprint $table) {
            // Vérifier si la colonne 'url' existe et la renommer en 'fichier'
            if (Schema::hasColumn('medias_galerie', 'url')) {
                $table->renameColumn('url', 'fichier');
            } elseif (!Schema::hasColumn('medias_galerie', 'fichier')) {
                $table->string('fichier')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medias_galerie', function (Blueprint $table) {
            if (Schema::hasColumn('medias_galerie', 'fichier')) {
                $table->renameColumn('fichier', 'url');
            }
        });
    }
};