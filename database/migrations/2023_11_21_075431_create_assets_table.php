<?php

use App\Enums\CommonStatusEnum;
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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code');
            $table->string('management_code');
            $table->string('management_unit');
            $table->float('original_price');
            $table->float('residual_price');
            $table->string('insurance_contract');
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
        });
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id');
            $table->dateTime('created_date');
            $table->string('created_by');
            $table->string('reason');
            $table->string('description');
            $table->string('proposal');
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
        });
        Schema::create('asset_delivery_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id');
            $table->dateTime('created_date');
            $table->string('receiver');
            $table->string('deliver');
            $table->string('reason');
            $table->string('place_of_use');
            $table->string('attachments');
            $table->string('code');
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_delivery_histories');
        Schema::dropIfExists('assets');
    }
};
