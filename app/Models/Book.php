<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','isbn','author_main','description','price','currency',
        'stock_qty','status','published_at','cover_image_path',
        'category_id','publisher_id','seller_id',
        // الحقول المخبأة للتقييمات
        'ratings_avg','ratings_count',
    ];

    protected $casts = [
        'published_at'  => 'datetime',
        'ratings_avg'   => 'decimal:2',
        'ratings_count' => 'integer',
    ];

    public function category()  { return $this->belongsTo(Category::class); }
    public function publisher() { return $this->belongsTo(Publisher::class); }
    public function authors()   { return $this->belongsToMany(Author::class); } // author_book
    public function reviews()   { return $this->hasMany(Review::class); }
    public function seller()    { return $this->belongsTo(User::class, 'seller_id'); }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * يعيد احتساب متوسط/عدد التقييمات المعتمدة وتخزينها في الحقول المخبأة.
     * يُستدعى تلقائياً من Model Review عند الحفظ/الحذف/تغيير الاعتماد.
     */
    public function recalculateRatings(): void
    {
        $agg = $this->reviews()
            ->where('approved', true)
            ->selectRaw('COUNT(*) as c, COALESCE(AVG(rating),0) as a')
            ->first();

        $count = (int) ($agg->c ?? 0);
        $avg   = round((float) ($agg->a ?? 0), 2);

        // saveQuietly لمنع إطلاق أحداث غير لازمة
        $this->forceFill([
            'ratings_count' => $count,
            'ratings_avg'   => $avg,
        ])->saveQuietly();
    }
}
