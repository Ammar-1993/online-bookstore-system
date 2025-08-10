<?php
// 8) create_orders_table
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['pending','paid','processing','shipped','completed','cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid','paid','refunded'])->default('unpaid');
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
