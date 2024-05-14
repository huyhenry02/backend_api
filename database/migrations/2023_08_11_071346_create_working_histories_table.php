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
        Schema::create('working_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('curriculum_vitae_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->uuid('position_id');
            $table->string('company', 255);
            $table->timestamps();
        });

        Schema::table('working_histories', static function (Blueprint $table) {
            $table->foreign('curriculum_vitae_id')->references('id')->on('curriculum_vitaes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_histories');
    }
};
