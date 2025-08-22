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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rateable_id'); // ID du post ou produit
            $table->string('rateable_type'); // 'App\Models\Post' ou 'App\Models\Product'
            $table->tinyInteger('evaluation')->unsigned()->comment('Note de 0 à 5 étoiles');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['rateable_id', 'rateable_type']);

            // Empêcher qu'un utilisateur note deux fois le même élément
            $table->unique(['user_id', 'rateable_id', 'rateable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
