<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Candidatures issues de la préinscription du site, et leurs pièces jointes. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();        // PRE-2026-0001
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->json('formations');                        // ['DG', 'CM']
            $table->string('mode')->nullable();
            $table->json('answers')->nullable();               // toutes les réponses du formulaire
            $table->string('stage', 20)->default('recue');     // recue | eligible | test | paiement | activee | refusee
            $table->unsignedTinyInteger('test_score')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['stage', 'created_at']);
        });

        Schema::create('candidature_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('path');
            $table->string('original_name');
            $table->unsignedInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidature_files');
        Schema::dropIfExists('candidatures');
    }
};
