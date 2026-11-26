<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('spent_on');
            $table->string('label');
            $table->string('category', 40);
            $table->unsignedInteger('amount');                  // FCFA
            $table->string('method', 40);                       // même liste que les versements : caisse concernée
            $table->string('beneficiary')->nullable();
            $table->string('reference', 60)->nullable();
            $table->text('note')->nullable();
            $table->string('receipt_path')->nullable();         // justificatif (stockage privé)
            $table->string('receipt_name')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['spent_on', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
