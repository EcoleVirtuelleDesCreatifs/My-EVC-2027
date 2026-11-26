<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->unsignedInteger('size');
            $table->text('message')->nullable();               // mot de l'étudiant
            $table->string('status', 20)->default('depose');    // depose | valide | a_reprendre
            $table->text('feedback')->nullable();              // retour de l'équipe pédagogique
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_reports');
    }
};
