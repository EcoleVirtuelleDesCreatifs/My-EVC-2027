<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('certificate_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('registry_document_id')->nullable()->unique();
            $table->string('status', 30)->default('a_transmettre');
            $table->string('reference', 80)->nullable();
            $table->string('appreciation')->default('Acquis');
            $table->char('pdf_sha256', 64)->nullable();
            $table->string('verification_url', 1000)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
