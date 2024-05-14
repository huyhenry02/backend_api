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
        Schema::create('unit_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 63);
            $table->string('code',63);
            $table->enum('status',CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_levels');
    }
};
