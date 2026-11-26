<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Suivi des envois de newsletter pour les actualités et désinscription des abonnés. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $t) {
            $t->timestamp('newsletter_sent_at')->nullable()->after('views');
        });
        Schema::table('newsletter_subscribers', function (Blueprint $t) {
            $t->timestamp('unsubscribed_at')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('news', fn (Blueprint $t) => $t->dropColumn('newsletter_sent_at'));
        Schema::table('newsletter_subscribers', fn (Blueprint $t) => $t->dropColumn('unsubscribed_at'));
    }
};
