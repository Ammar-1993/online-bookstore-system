<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // رقم تتبع الشحنة (اختياري) + فريد
            $table->string('tracking_number')->nullable()->unique()->after('payment_status');
            // اسم شركة الشحن (اختياري)
            $table->string('shipping_carrier', 50)->nullable()->after('tracking_number');
            // تاريخ/وقت الشحن (اختياري)
            $table->timestamp('shipped_at')->nullable()->after('shipping_carrier');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // إسقاط الفهرس الفريد قبل العمود (للتوافق مع بعض المحركات)
            $table->dropUnique('orders_tracking_number_unique');
            $table->dropColumn(['tracking_number', 'shipping_carrier', 'shipped_at']);
        });
    }
};
