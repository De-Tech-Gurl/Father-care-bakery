<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            $table->string('product_name')->after('product_id');
            $table->unsignedInteger('quantity')->default(1)->after('product_name');
            $table->decimal('price', 10, 2)->after('quantity');
            $table->decimal('total', 10, 2)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['product_name', 'quantity', 'price', 'total']);
        });
    }
};
