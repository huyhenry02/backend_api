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
        Schema::table('contract_insurance_processed_histories', function (Blueprint $table) {
            $table->date('received_date')->nullable()->change();
            $table->date('completed_date')->nullable()->change();
            $table->float('refund_amount')->nullable()->change();
            $table->date('refunded_date')->nullable()->change();
        });

        Schema::table('working_histories', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->string('company', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_insurance_processed_histories', function (Blueprint $table) {
            $table->date('received_date')->nullable(false)->change();
            $table->date('completed_date')->nullable(false)->change();
            $table->float('refund_amount')->nullable(false)->change();
            $table->date('refunded_date')->nullable(false)->change();
        });

        Schema::table('working_histories', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            $table->string('company', 255)->nullable(false)->change();
        });
    }
};
