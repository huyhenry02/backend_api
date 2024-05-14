<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $enum = ['pending', 'approved', 'processing', 'completed', 'rejected'];
    private string $enum_default = 'pending';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registerer')->references('id')->on('employees')->onDelete('cascade');
            $table->foreignUuid('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('phone', 12);
            $table->string('identification', 12);
            $table->string('reason')->nullable();
            $table->string('reject_reason')->nullable();
            $table->enum('status', $this->enum)->default($this->enum_default);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
