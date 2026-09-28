<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('order_number')->unique()->after('user_id');
            $table->decimal('total_amount', 10, 2)->default(0)->after('order_number');
            $table->string('status')->default('pending')->after('total_amount');
            $table->string('delivery_type')->default('pickup')->after('status');
            $table->text('delivery_address')->nullable()->after('delivery_type');
            $table->string('phone')->nullable()->after('delivery_address');
            $table->text('notes')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'order_number',
                'total_amount',
                'status',
                'delivery_type',
                'delivery_address',
                'phone',
                'notes',
            ]);
        });
    }
};
