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
        Schema::create('contract_insurance_processed_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->uuid('insurance_policy_id');
            $table->date('received_date');
            $table->date('completed_date');
            $table->float('refund_amount');
            $table->date('refunded_date');
            $table->timestamps();
        });
        Schema::table('contract_insurance_processed_histories', static function (Blueprint $table) {
            $table->foreign('contract_id')->references('id')->on('contracts');
        });
        Schema::table('contract_insurance_processed_histories', static function (Blueprint $table) {
            $table->foreign('insurance_policy_id')->references('id')->on('insurance_policies');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_insurance_processed_histories');
    }
};
