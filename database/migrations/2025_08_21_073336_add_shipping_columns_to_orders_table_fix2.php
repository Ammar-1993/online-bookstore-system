<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // معلومات الشحن/التتبع
            $table->string('tracking_number')->nullable()->after('payment_status');
            $table->string('shipping_carrier', 50)->nullable()->after('tracking_number');
            $table->string('tracking_url')->nullable()->after('shipping_carrier');
            $table->timestamp('shipped_at')->nullable()->after('placed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'shipping_carrier', 'tracking_url', 'shipped_at']);
        });
    }
};
