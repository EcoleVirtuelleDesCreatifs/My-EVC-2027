<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->json('first_payment')->nullable();
            $table->foreignId('first_payment_id')->nullable()->constrained('payments')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('first_payment_id');
            $table->dropColumn('first_payment');
        });
    }
};
