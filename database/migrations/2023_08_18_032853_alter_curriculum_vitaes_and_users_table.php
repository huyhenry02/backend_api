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
        Schema::table('curriculum_vitaes', static function (Blueprint $table) {
            $table->uuid('position_id')->nullable()->change();
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->addColumn('uuid', 'employee_id');
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculum_vitaes', static function (Blueprint $table) {
            $table->uuid('position_id')->change();
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
