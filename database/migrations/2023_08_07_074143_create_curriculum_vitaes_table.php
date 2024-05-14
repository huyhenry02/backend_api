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
        Schema::create('curriculum_vitaes', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('code', 50)->unique();
            $table->string('name', 50);
            $table->string('nationality', 50)->nullable();
            $table->string('email', 100);
            $table->string('phone_number', 50);
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('country', 255)->nullable();
            $table->boolean('marital')->nullable();
            $table->string('ethnic', 50)->nullable();
            $table->string('region', 50)->nullable();
            $table->string('identification', 50);
            $table->string('place_of_issue', 50)->nullable();
            $table->date('date_of_issue')->nullable();
            $table->string('tax_code', 50)->nullable();
            $table->date('onboard_date')->nullable();
            $table->uuid('leader_id')->nullable();
            $table->uuid('subsidiary_id')->nullable();
            $table->uuid('position_id');
            $table->string('address', 255)->nullable();
            $table->string('bank_account_number', 255)->nullable();
            $table->string('bank_account_name', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_branch', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('curriculum_vitaes', static function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('leader_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_vitaes');
    }
};
