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
        Schema::create('marketing_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('source')->nullable(); // Traffic source
            $table->string('medium')->nullable(); // Marketing medium
            $table->string('campaign')->nullable(); // Campaign name
            $table->integer('visits')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('page_views')->default(0);
            $table->integer('add_to_carts')->default(0);
            $table->integer('checkouts_initiated')->default(0);
            $table->integer('purchases')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0); // Percentage
            $table->timestamps();

            $table->index(['date', 'source', 'medium', 'campaign']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_metrics');
    }
};
