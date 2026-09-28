<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreignId('product_id')->after('id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_change')->after('product_id');
            $table->string('reason')->after('quantity_change');
            $table->string('reference')->nullable()->after('reason');
            $table->foreignId('created_by')->nullable()->after('reference')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['quantity_change', 'reason', 'reference']);
        });
    }
};
