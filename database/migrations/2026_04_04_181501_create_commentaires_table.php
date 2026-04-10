<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->string('nom');
            $table->string('email');
            $table->text('contenu');
            $table->boolean('approuve')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'approuve']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};