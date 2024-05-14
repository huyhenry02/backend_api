<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->uuid('new_user_id')->nullable();
        });

        // Step 2: Generate a UUID for each row and update the new column
        $tokens = DB::table('audits')->get();
        foreach ($tokens as $token) {
            $uuid = Str::uuid()->toString();
            DB::table('audits')
                ->where('id', $token->id)
                ->update(['new_user_id' => $uuid]);
        }

        // Step 3: Drop the old 'user_id' column
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        // Step 4: Rename the new column to 'user_id' to maintain the foreign key
        Schema::table('audits', function (Blueprint $table) {
            $table->renameColumn('new_user_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
        });
    }
};
