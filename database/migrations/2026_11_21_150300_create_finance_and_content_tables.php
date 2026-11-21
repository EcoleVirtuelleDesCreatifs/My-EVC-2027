<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Paiements, bibliothèque, certificats, actualités, messages, paramètres, journal d'audit. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('label');                            // Acompte, Solde
            $table->unsignedInteger('amount');
            $table->date('due_on');
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 20)->unique();     // REC-2026-0001
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('amount');
            $table->string('method', 30);                       // Orange Money, MTN MoMo, Moov Money, Wave, Espèces, Virement
            $table->string('transaction_ref')->nullable();
            $table->date('paid_on');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->nullable()->constrained()->nullOnDelete();  // NULL = toutes les formations
            $table->string('category', 20);                     // logiciels | ebooks | polices | graphiques | modeles | supports | tutos
            $table->string('title');
            $table->string('description', 300);
            $table->string('format', 60);
            $table->string('size', 30)->nullable();
            $table->string('licence', 60)->default('Réservé EVC');
            $table->string('compatible')->nullable();
            $table->string('url')->nullable();                  // site officiel
            $table->string('path')->nullable();                 // fichier hébergé
            $table->boolean('essential')->default(false);
            $table->boolean('published')->default(true);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('resource_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['resource_id', 'student_id']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();               // EVC-CERT-2026-0451
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('appreciation')->default('Acquis');
            $table->date('issued_on');
            $table->timestamps();
            $table->unique(['student_id', 'formation_id']);
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 60);
            $table->string('label', 60)->nullable();
            $table->string('excerpt', 400);
            $table->string('cover');
            $table->longText('body');
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('source', 40)->default('contact');   // contact | partenariat
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->json('fields');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'settings', 'newsletter_subscribers', 'contact_messages', 'news', 'certificates',
                  'resource_downloads', 'resources', 'payments', 'payment_schedules'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
