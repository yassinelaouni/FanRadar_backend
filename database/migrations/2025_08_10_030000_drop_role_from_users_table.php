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
        // Cette migration ne fait plus rien car le champ 'role' n'existe plus
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire ici non plus
    }
};
