<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Formations, promotions (sessions), étudiants, inscriptions, formateurs. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();          // DG, CM, GIA
            $table->string('name');
            $table->string('short_name');
            $table->text('pitch')->nullable();
            $table->unsignedInteger('price');              // FCFA
            $table->unsignedInteger('deposit');            // acompte à l'inscription
            $table->unsignedTinyInteger('months');
            $table->unsignedSmallInteger('balance_after_days')->default(30);  // solde dû N jours après le début
            $table->string('pay_rule')->nullable();
            $table->json('modules')->nullable();           // [{t, ch: [...]}]
            $table->string('color', 9)->default('#3A3591');
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status', 20)->default('ouverte');   // ouverte | en_cours | terminee
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('matricule', 20)->unique();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('level', 20)->default('Débutant');     // Débutant | Intermédiaire | Avancé
            $table->string('status', 20)->default('actif');        // actif | suspendu | certifie | abandon
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode', 20)->default('En ligne');       // En ligne | Présentiel
            $table->string('status', 20)->default('en_cours');     // en_cours | terminee | abandon
            $table->timestamps();
            $table->unique(['student_id', 'formation_id']);
        });

        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Formateur');
            $table->timestamps();
            $table->unique(['user_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('formations');
    }
};
