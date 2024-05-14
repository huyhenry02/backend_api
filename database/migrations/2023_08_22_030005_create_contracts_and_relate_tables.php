<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CommonStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->string('code', 255);
            $table->uuid('contract_type_id');
            $table->uuid('department_id')->nullable();
            $table->uuid('position_id')->nullable();
            $table->string('function')->nullable();
            $table->string('rank')->nullable();
            $table->string('skill_coefficient')->nullable();
            $table->string('workplace')->nullable();
            $table->uuid('employment_type_id')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('signed_date')->nullable();
            $table->uuid('signer')->nullable();
            $table->enum('digital_signature', CommonStatusEnum::values())->nullable();
            $table->date('apply_from_date')->nullable();
            $table->text('note')->nullable();
            $table->text('payment_type')->nullable();
            $table->bigInteger('salary')->nullable();
            $table->string('insurance_book_number', 63)->nullable();
            $table->enum('insurance_book_status', CommonStatusEnum::values())->nullable();
            $table->string('insurers', 127)->nullable();
            $table->string('insurance_card_number', 63)->nullable();
            $table->string('insurance_city_code', 63)->nullable();
            $table->string('medical_examination_place', 127)->nullable();
            $table->date('card_received_date')->nullable();
            $table->date('card_returned_date')->nullable();
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('contract_type_id')->references('id')->on('contract_types')->cascadeOnDelete();
            $table->foreign('employment_type_id')->references('id')->on('employments')->cascadeOnDelete();
        });

        Schema::create('contract_working_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->date('worked_from_date')->nullable();
            $table->date('worked_to_date')->nullable();
            $table->uuid('from_department')->nullable();
            $table->uuid('to_department')->nullable();
            $table->string('reason', 512)->nullable();
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('from_department')->references('id')->on('hierarchies')->cascadeOnDelete();
            $table->foreign('to_department')->references('id')->on('hierarchies')->cascadeOnDelete();
        });

        Schema::create('contract_allowances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->uuid('allowance_id')->nullable();
            $table->bigInteger('benefit')->nullable();
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('allowance_id')->references('id')->on('allowances')->cascadeOnDelete();
        });

        Schema::create('insurance_settlement_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->uuid('insurance_type_id')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('insurance_payment_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->bigInteger('insurance_money')->nullable();
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('insurance_type_id')->references('id')->on('insurance_policies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_working_histories');
        Schema::dropIfExists('contract_allowances');
        Schema::dropIfExists('insurance_settlement_histories');
        Schema::dropIfExists('contracts');
    }
};
