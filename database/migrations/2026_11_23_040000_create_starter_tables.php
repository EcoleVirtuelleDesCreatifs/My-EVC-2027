<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* Classes virtuelles (groupes WhatsApp, visio…) par formation, éventuellement par session */
        Schema::create('virtual_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('url', 500);
            $table->string('schedule')->nullable();       // ex. « Lundi et jeudi, 19 h – 21 h »
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        /* Outils de démarrage : logiciels, supports et programmes à télécharger */
        Schema::create('starter_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->nullable()->constrained()->cascadeOnDelete(); // null : toutes les formations
            $table->string('kind', 20);                   // logiciel | support | programme | autre
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('version', 60)->nullable();    // ex. « 2025 », « Windows et macOS »
            $table->string('url', 500)->nullable();       // lien de téléchargement officiel
            $table->string('path')->nullable();           // fichier hébergé (stockage privé)
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('published')->default(true);
            $table->unsignedInteger('downloads')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('starter_tools');
        Schema::dropIfExists('virtual_classes');
    }
};
