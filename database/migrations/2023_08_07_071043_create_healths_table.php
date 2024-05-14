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
        Schema::create('healths', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('blood_pressure', 255)->nullable();
            $table->integer('heartbeat')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('blood_group', 255)->nullable();
            $table->string('note', 255);
            $table->uuid('employee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healths');
    }
};
