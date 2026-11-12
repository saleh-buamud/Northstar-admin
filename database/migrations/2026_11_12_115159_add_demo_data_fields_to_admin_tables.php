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
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('status', 20)->default('active')->index();
            $table->string('demo_key', 100)->nullable()->unique();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('demo_key', 100)->nullable()->unique();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone_number', 30)->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('demo_key', 100)->nullable()->unique();
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->string('reference')->nullable()->unique();
            $table->string('demo_key', 100)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table): void {
            $table->dropUnique(['reference']);
            $table->dropUnique(['demo_key']);
            $table->dropColumn(['reference', 'demo_key']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['demo_key']);
            $table->dropColumn(['phone_number', 'avatar_path', 'demo_key']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropUnique(['demo_key']);
            $table->dropColumn(['sale_price', 'demo_key']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropUnique(['demo_key']);
            $table->dropColumn(['status', 'demo_key']);
        });
    }
};
