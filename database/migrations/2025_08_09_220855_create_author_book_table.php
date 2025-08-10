<?php
// 5) create_author_book_table (Pivot وفق الترتيب الأبجدي الافتراضي)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->primary(['author_id', 'book_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('author_book'); }
};
