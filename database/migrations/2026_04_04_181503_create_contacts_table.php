<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->string('sujet');
            $table->text('message');
            $table->boolean('lu')->default(false);
            $table->boolean('archive')->default(false);
            $table->timestamp('lu_at')->nullable();
            $table->foreignId('lu_par')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reponse')->nullable();
            $table->timestamp('repondu_at')->nullable();
            $table->foreignId('repondu_par')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['lu', 'archive']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};