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
        Schema::create('hierarchies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->nullable();
            $table->string('name',127);
            $table->string('unit_code', 63);
            $table->string('tax_code', 63)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('registration_number', 63)->nullable();
            $table->date('date_of_issue')->nullable();
            $table->string('place_of_issue', 127)->nullable();
            $table->string('representative', 63)->nullable();
            $table->string('position', 63)->nullable();
            $table->uuid('level_id');
            $table->text('mandates')->nullable();
            $table->enum('status', CommonStatusEnum::values())->default(CommonStatusEnum::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('hierarchies', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('hierarchies')->cascadeOnDelete();
            $table->foreign('level_id')->references('id')->on('unit_levels')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hierarchies');
    }
};
