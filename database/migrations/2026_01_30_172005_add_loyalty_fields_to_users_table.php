<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // phone might already exist from 2026_01_30_074415_add_profile_fields_to_users_table.php
            // We ensure it's unique and indexed here if it doesn't have it, but for simplicity 
            // and to avoid 'doctrine/dbal' dependency issues if not present, we will just add the balance 
            // and if phone doesn't exist (unlikely given previous migration), we add it.
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->unique()->index()->after('email');
            } else {
                // If it exists, we want to make it unique and indexed.
                // Note: unique() might fail if there's duplicate data, but this is a fresh-ish DB.
                $table->string('phone')->nullable()->unique()->index()->change();
            }
            $table->bigInteger('loyalty_points_balance')->default(0)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('loyalty_points_balance');
            // We usually don't drop unique indexes in reverse if they were changed
        });
    }
};
