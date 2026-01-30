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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('vat_rate', 5, 2)->default(0)->after('cost_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('vat_amount', 12, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vat_rate');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('vat_amount');
        });
    }
};
