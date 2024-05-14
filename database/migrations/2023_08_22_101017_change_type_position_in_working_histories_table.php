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
        Schema::table('working_histories', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->string('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_histories', function (Blueprint $table) {
            $table->uuid('position_id');
            $table->dropColumn('position');
        });
    }
};
