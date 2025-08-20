<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','book_id','rating','comment','approved'];

    protected $with = ['user'];

    // علاقات
    public function user(){ return $this->belongsTo(User::class); }
    public function book(){ return $this->belongsTo(Book::class); }

    // سكوبات مفيدة
    public function scopeApproved($q){ $q->where('approved', true); }
    public function scopeForSeller($q, int $sellerId){
        $q->whereHas('book', fn($b) => $b->where('seller_id', $sellerId));
    }

    /**
     * بعد حفظ/حذف/تحديث الاعتماد، أعد حساب تقييمات الكتاب المرتبط.
     */
    protected static function booted(): void
    {
        $recalc = function(Review $review): void {
            // نستخدم find لتجنّب علاقات غير محملة
            if ($review->book_id) {
                if ($book = Book::find($review->book_id)) {
                    $book->recalculateRatings();
                }
            }
        };

        static::saved($recalc);
        static::deleted($recalc);
        static::updated(function(Review $review) use ($recalc) {
            if ($review->wasChanged('approved') || $review->wasChanged('rating')) {
                $recalc($review);
            }
        });
    }
}
