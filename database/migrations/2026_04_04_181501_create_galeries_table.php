<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeries', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_couverture')->nullable();
            $table->enum('categorie', ['sorties', 'ceremonies', 'activites', 'resultats', 'autres'])->default('autres');
            $table->year('annee')->nullable();
            $table->boolean('publiee')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['publiee', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeries');
    }
};