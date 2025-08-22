<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('orders', 'shipping_carrier')) {
                $table->string('shipping_carrier')->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('shipping_carrier');
            }
            if (! Schema::hasColumn('orders', 'tracking_url')) {
                $table->string('tracking_url')->nullable()->after('shipped_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tracking_url')) {
                $table->dropColumn('tracking_url');
            }
            if (Schema::hasColumn('orders', 'shipped_at')) {
                $table->dropColumn('shipped_at');
            }
            if (Schema::hasColumn('orders', 'shipping_carrier')) {
                $table->dropColumn('shipping_carrier');
            }
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
        });
    }
};
