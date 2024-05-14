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
        Schema::table('working_histories', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('position', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_histories', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->string('position', 255)->nullable(false)->change();
        });
    }
};
