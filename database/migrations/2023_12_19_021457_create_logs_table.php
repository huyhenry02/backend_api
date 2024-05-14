<?php

use App\Enums\ActionLogTypeEnum;
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
        Schema::create('logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('model_type');
            $table->uuid('model_id');
            $table->enum('action',ActionLogTypeEnum::values());
            $table->json('old_data');
            $table->json('new_data');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
