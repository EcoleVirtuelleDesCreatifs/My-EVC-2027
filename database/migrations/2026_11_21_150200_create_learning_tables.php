<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Séances et présences, projets, attributions, dépôts et commentaires. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('module')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('mode', 20)->default('virtuel');    // virtuel | presentiel
            $table->string('meet_url')->nullable();
            $table->string('room')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->timestamps();
            $table->index('starts_at');
        });

        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);                      // present | retard | absent
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->unique(['seance_id', 'student_id']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('Projet');
            $table->text('brief');
            $table->boolean('published')->default(true);
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_on');
            $table->string('status', 20)->default('afaire');   // afaire | encours | soumis | reprendre | valide
            $table->string('appreciation')->nullable();         // Excellent | Très bien | Acquis | À renforcer
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'student_id']);
            $table->index(['status', 'due_on']);
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->json('links')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->unsignedInteger('size');
            $table->timestamps();
        });

        Schema::create('assignment_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 20)->default('commentaire'); // commentaire | attribution | demarrage | depot | reprise | validation
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['assignment_comments', 'submission_files', 'submissions', 'assignments', 'projects', 'presences', 'seances'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
