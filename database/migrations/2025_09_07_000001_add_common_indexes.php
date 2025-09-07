<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        $sql = "SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1";
        return (bool) DB::selectOne($sql, [$table, $index]);
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    private function ensureIndexOnColumn(string $table, string $index, string $column): void
    {
        if ($this->hasColumn($table, $column) && ! $this->indexExists($table, $index)) {
            Schema::table($table, function (Blueprint $t) use ($column, $index) {
                $t->index($column, $index);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("DROP INDEX {$index} ON {$table}");
        }
    }

    public function up(): void
    {
        // books
        $this->ensureIndexOnColumn('books', 'idx_books_slug', 'slug');
        $this->ensureIndexOnColumn('books', 'idx_books_category_id', 'category_id');
        $this->ensureIndexOnColumn('books', 'idx_books_publisher_id', 'publisher_id');
        $this->ensureIndexOnColumn('books', 'idx_books_created_at', 'created_at');

        // categories / publishers / authors
        $this->ensureIndexOnColumn('categories', 'idx_categories_slug', 'slug');
        $this->ensureIndexOnColumn('publishers', 'idx_publishers_slug', 'slug');
        $this->ensureIndexOnColumn('authors', 'idx_authors_slug', 'slug');

        // reviews — جرّب status ثم approved ثم is_approved
        if ($this->hasColumn('reviews', 'status')) {
            $this->ensureIndexOnColumn('reviews', 'idx_reviews_status', 'status');
        } elseif ($this->hasColumn('reviews', 'approved')) {
            $this->ensureIndexOnColumn('reviews', 'idx_reviews_approved', 'approved');
        } elseif ($this->hasColumn('reviews', 'is_approved')) {
            $this->ensureIndexOnColumn('reviews', 'idx_reviews_is_approved', 'is_approved');
        }
        $this->ensureIndexOnColumn('reviews', 'idx_reviews_book_id', 'book_id');
        $this->ensureIndexOnColumn('reviews', 'idx_reviews_user_id', 'user_id');
        $this->ensureIndexOnColumn('reviews', 'idx_reviews_created_at', 'created_at');

        // orders
        $this->ensureIndexOnColumn('orders', 'idx_orders_user_id', 'user_id');
        $this->ensureIndexOnColumn('orders', 'idx_orders_status', 'status');
        $this->ensureIndexOnColumn('orders', 'idx_orders_payment_status', 'payment_status');
        $this->ensureIndexOnColumn('orders', 'idx_orders_created_at', 'created_at');

        // order_items
        $this->ensureIndexOnColumn('order_items', 'idx_order_items_order_id', 'order_id');
        $this->ensureIndexOnColumn('order_items', 'idx_order_items_book_id', 'book_id');

        // cart_items — دعم user_id أو session_id حسب تصميم الجدول
        if ($this->hasColumn('cart_items', 'user_id')) {
            $this->ensureIndexOnColumn('cart_items', 'idx_cart_items_user_id', 'user_id');
        } elseif ($this->hasColumn('cart_items', 'session_id')) {
            $this->ensureIndexOnColumn('cart_items', 'idx_cart_items_session_id', 'session_id');
        }
        $this->ensureIndexOnColumn('cart_items', 'idx_cart_items_book_id', 'book_id');
    }

    public function down(): void
    {
        // books
        $this->dropIndexIfExists('books', 'idx_books_slug');
        $this->dropIndexIfExists('books', 'idx_books_category_id');
        $this->dropIndexIfExists('books', 'idx_books_publisher_id');
        $this->dropIndexIfExists('books', 'idx_books_created_at');

        // categories / publishers / authors
        $this->dropIndexIfExists('categories', 'idx_categories_slug');
        $this->dropIndexIfExists('publishers', 'idx_publishers_slug');
        $this->dropIndexIfExists('authors', 'idx_authors_slug');

        // reviews
        $this->dropIndexIfExists('reviews', 'idx_reviews_status');
        $this->dropIndexIfExists('reviews', 'idx_reviews_approved');
        $this->dropIndexIfExists('reviews', 'idx_reviews_is_approved');
        $this->dropIndexIfExists('reviews', 'idx_reviews_book_id');
        $this->dropIndexIfExists('reviews', 'idx_reviews_user_id');
        $this->dropIndexIfExists('reviews', 'idx_reviews_created_at');

        // orders
        $this->dropIndexIfExists('orders', 'idx_orders_user_id');
        $this->dropIndexIfExists('orders', 'idx_orders_status');
        $this->dropIndexIfExists('orders', 'idx_orders_payment_status');
        $this->dropIndexIfExists('orders', 'idx_orders_created_at');

        // order_items
        $this->dropIndexIfExists('order_items', 'idx_order_items_order_id');
        $this->dropIndexIfExists('order_items', 'idx_order_items_book_id');

        // cart_items
        $this->dropIndexIfExists('cart_items', 'idx_cart_items_user_id');
        $this->dropIndexIfExists('cart_items', 'idx_cart_items_session_id');
        $this->dropIndexIfExists('cart_items', 'idx_cart_items_book_id');
    }
};
