<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id'); // L'utilisateur qui suit
            $table->unsignedBigInteger('following_id'); // L'utilisateur qui est suivi
            $table->timestamps();

            // Clés étrangères
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('following_id')->references('id')->on('users')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['follower_id']);
            $table->index(['following_id']);

            // Empêcher qu'un utilisateur suive deux fois la même personne
            $table->unique(['follower_id', 'following_id']);

            // Contrainte pour empêcher qu'un utilisateur se suive lui-même sera gérée dans le code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
