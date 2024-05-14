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
        Schema::table('curriculum_vitaes', function (Blueprint $table) {
                $table->dropColumn('nationality');
                $table->dropColumn('region');
        });
        Schema::table('curriculum_vitaes', function (Blueprint $table) {
            $table->uuid('nationality_id')->nullable();
            $table->uuid('region_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculum_vitaes', function (Blueprint $table) {
            $table->dropColumn('nationality_id');
            $table->dropColumn('region_id');
        });
        Schema::table('curriculum_vitaes', function (Blueprint $table) {
            $table->string('nationality')->nullable();
            $table->string('region')->nullable();
        });
    }
};
