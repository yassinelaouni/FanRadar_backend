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
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id');

            // Champs pour la relation polymorphe
            $table->unsignedBigInteger('taggable_id');
            $table->string('taggable_type');

            $table->timestamps();

            // Clé étrangère vers la table tags
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

            // Index pour améliorer les performances des relations polymorphes
            $table->index(['taggable_id', 'taggable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};
