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
        Schema::table('posts', function (Blueprint $table) {
            // Champs déjà ajoutés précédemment
            if (!Schema::hasColumn('posts', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('posts', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('posts', 'fandom')) {
                $table->string('fandom')->nullable()->after('subcategory_id');
            }
            if (!Schema::hasColumn('posts', 'likes')) {
                $table->integer('likes')->default(0)->after('fandom');
            }
            if (!Schema::hasColumn('posts', 'content')) {
                $table->text('content')->nullable()->after('title');
            }
            // Champ media (image ou vidéo principale)
            if (!Schema::hasColumn('posts', 'media')) {
                $table->string('media')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'subcategory_id', 'fandom', 'likes', 'content', 'media']);
        });
    }
};
