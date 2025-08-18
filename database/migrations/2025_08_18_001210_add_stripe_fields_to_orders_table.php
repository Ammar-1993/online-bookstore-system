<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_intent_id')->nullable()->after('payment_status');
            $table->string('charge_id')->nullable()->after('payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('charge_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id','charge_id','paid_at']);
        });
    }
};
