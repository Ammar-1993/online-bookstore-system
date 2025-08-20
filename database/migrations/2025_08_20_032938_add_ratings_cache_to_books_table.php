<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('ratings_avg', 3, 2)->default(0)->after('seller_id');
            $table->unsignedInteger('ratings_count')->default(0)->after('ratings_avg');
            $table->index(['ratings_count', 'ratings_avg']);
        });

        // تهيئة أولية (إن وُجدت مراجعات مسبقًا)
        // NB: نعتمد حسابًا بسيطًا عبر SQL؛ لو لم تكن MySQL 8+ يدعم AVG/COUNT عاديين.
        $sql = <<<SQL
UPDATE books b
LEFT JOIN (
  SELECT book_id, COUNT(*) AS c, COALESCE(AVG(rating),0) AS a
  FROM reviews
  WHERE approved = 1
  GROUP BY book_id
) r ON r.book_id = b.id
SET b.ratings_count = COALESCE(r.c,0),
    b.ratings_avg   = ROUND(COALESCE(r.a,0), 2)
SQL;
        DB::statement($sql);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['ratings_count', 'ratings_avg']);
            $table->dropColumn(['ratings_avg', 'ratings_count']);
        });
    }
};
