<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Phase 2 : Web TV, RECO 60, QCM, messagerie. */
return new class extends Migration
{
    public function up(): void
    {
        /* ---------- Web TV ---------- */
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seance_id')->nullable()->constrained()->nullOnDelete();   // replay d'une séance
            $table->string('module')->default('Séances');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source', 10)->default('fichier');   // fichier | youtube | vimeo
            $table->string('url')->nullable();
            $table->string('path')->nullable();
            $table->unsignedSmallInteger('duration_min')->default(0);
            $table->boolean('published')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('seconds')->default(0);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['video_id', 'student_id']);
        });

        /* ---------- RECO 60 ---------- */
        Schema::table('formations', function (Blueprint $table) {
            $table->boolean('has_reco')->default(false)->after('modules');
        });
        Schema::create('reco_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sector')->nullable();
            $table->text('description');
            $table->text('objectives')->nullable();
            $table->string('channels')->nullable();
            $table->json('steps');            // [{t, d, done}]
            $table->json('kpis')->nullable(); // [{l, v}]
            $table->date('started_on');
            $table->string('status', 20)->default('en_cours');   // en_cours | rendu | evalue
            $table->string('report_path')->nullable();
            $table->string('report_name')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('appreciation')->nullable();
            $table->text('evaluation')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'formation_id']);
        });
        Schema::create('reco_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reco_project_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('action');
            $table->text('result')->nullable();
            $table->decimal('hours', 5, 1)->default(0);
            $table->string('link')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('feedback_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('feedback_at')->nullable();
            $table->timestamps();
        });

        /* ---------- QCM ---------- */
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_min')->default(15);
            $table->unsignedTinyInteger('attempts_allowed')->default(2);
            $table->unsignedTinyInteger('pass_percent')->default(60);
            $table->boolean('required')->default(false);   // compte pour la certification
            $table->boolean('published')->default(false);
            $table->dateTime('opens_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->json('options');
            $table->unsignedTinyInteger('answer');
            $table->text('explanation')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->json('answers')->nullable();     // {question_id: index}
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedSmallInteger('total')->default(0);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        /* ---------- Messagerie ---------- */
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('last_read_id')->nullable();   // dernier message lu (exact, contrairement à une date à la seconde)
            $table->timestamp('last_read_at')->nullable();
            $table->unique(['conversation_id', 'user_id']);
        });
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['messages', 'conversation_user', 'conversations', 'quiz_attempts', 'quiz_questions', 'quizzes', 'reco_entries', 'reco_projects', 'video_progress', 'videos'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('formations', fn (Blueprint $table) => $table->dropColumn('has_reco'));
    }
};
