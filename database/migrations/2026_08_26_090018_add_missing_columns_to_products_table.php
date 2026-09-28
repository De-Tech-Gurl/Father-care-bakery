<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add foreign key for category
            $table->foreignId('category_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->onDelete('set null');

            // Add slug (unique)
            $table->string('slug')
                ->unique()
                ->after('name');

            // Add cost price
            $table->decimal('cost_price', 10, 2)
                ->nullable()
                ->after('price');

            // Rename 'stock' to 'stock_quantity' (if you already have 'stock')
            // If you have 'stock' column, rename it:
            $table->renameColumn('stock', 'stock_quantity');

            // If you don't have 'stock' and want a new column:
            // $table->integer('stock_quantity')->default(0)->after('cost_price');

            // Add low stock threshold
            $table->integer('low_stock_threshold')
                ->default(5)
                ->after('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'slug',
                'cost_price',
                'stock_quantity',
                'low_stock_threshold',
            ]);
            // If you renamed stock back:
            $table->renameColumn('stock_quantity', 'stock');
        });
    }
};
