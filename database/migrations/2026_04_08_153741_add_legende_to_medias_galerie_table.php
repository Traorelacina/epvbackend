<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medias_galerie', function (Blueprint $table) {
            if (!Schema::hasColumn('medias_galerie', 'legende')) {
                $table->string('legende')->nullable()->after('fichier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medias_galerie', function (Blueprint $table) {
            if (Schema::hasColumn('medias_galerie', 'legende')) {
                $table->dropColumn('legende');
            }
        });
    }
};