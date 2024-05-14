<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('skill_coefficient');
                $table->dropColumn('workplace');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->uuid('workplace')->nullable();
            $table->float('skill_coefficient')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('skill_coefficient');
            $table->dropColumn('workplace');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('workplace')->nullable();
            $table->string('skill_coefficient')->nullable();
        });    }
};
