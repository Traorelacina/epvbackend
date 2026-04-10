<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temoignages', function (Blueprint $table) {
            $table->id();
            $table->string('nom_parent');
            $table->string('nom_enfant')->nullable();
            $table->string('classe_enfant')->nullable();
            $table->text('contenu');
            $table->tinyInteger('note')->default(5); // Notation 1-5
            $table->string('photo')->nullable();
            $table->boolean('valide')->default(false);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->index(['valide', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temoignages');
    }
};