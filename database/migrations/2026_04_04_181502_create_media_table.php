<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galerie_id')->constrained('galeries')->onDelete('cascade');
            $table->enum('type', ['photo', 'video'])->default('photo');
            $table->string('url');
            $table->string('url_miniature')->nullable();
            $table->string('legende')->nullable();
            $table->string('url_youtube')->nullable(); // Pour les vidéos YouTube
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->index(['galerie_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};