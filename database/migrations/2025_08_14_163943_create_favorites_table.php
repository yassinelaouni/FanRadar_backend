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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('favoriteable_id'); // ID du post ou produit
            $table->string('favoriteable_type'); // 'App\Models\Post' ou 'App\Models\Product'
            $table->timestamps();

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['favoriteable_id', 'favoriteable_type']);

            // Empêcher qu'un utilisateur ajoute deux fois le même item en favori
            $table->unique(['user_id', 'favoriteable_id', 'favoriteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
