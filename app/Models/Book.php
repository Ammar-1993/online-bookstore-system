<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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

    



        /** فلترة عامة حسب مدخلات الطلب */
    public function scopeFilter(Builder $q, array $f): Builder
    {
        // نص البحث (العنوان/الوصف + مؤلف/ناشر/تصنيف)
        if (!empty($f['q'])) {
            $term = mb_strtolower($f['q']);
            $q->where(function (Builder $qq) use ($term) {
                $qq->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                   ->orWhereRaw('LOWER(COALESCE(description, "")) LIKE ?', ["%{$term}%"])
                   ->orWhereHas('authors', fn(Builder $a) => $a->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                   ->orWhereHas('publisher', fn(Builder $p) => $p->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                   ->orWhereHas('category', fn(Builder $c) => $c->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]));
            });
        }

        // تصنيفات/ناشرين/مؤلفين (slug أو id كنص)
        if (!empty($f['category'])) {
            $val = $f['category'];
            $q->whereHas('category', function (Builder $c) use ($val) {
                $c->when(is_numeric($val), fn($w) => $w->where('id', $val),
                                   fn($w) => $w->where('slug', $val));
            });
        }

        if (!empty($f['publisher'])) {
            $val = $f['publisher'];
            $q->whereHas('publisher', function (Builder $p) use ($val) {
                $p->when(is_numeric($val), fn($w) => $w->where('id', $val),
                                   fn($w) => $w->where('slug', $val));
            });
        }

        // author (مفرد) أو authors[] (متعدد)
        $authors = [];
        if (!empty($f['author']))   $authors[] = $f['author'];
        if (!empty($f['authors']))  $authors = array_merge($authors, (array)$f['authors']);
        $authors = array_filter(array_unique($authors));
        if ($authors) {
            $q->whereHas('authors', function (Builder $a) use ($authors) {
                $a->where(function (Builder $aa) use ($authors) {
                    foreach ($authors as $i => $val) {
                        $aa->orWhere(function (Builder $x) use ($val) {
                            is_numeric($val) ? $x->where('id', $val) : $x->where('slug', $val);
                        });
                    }
                });
            });
        }

        // السعر
        if (isset($f['price_min']) && $f['price_min'] !== null) {
            $q->where('price', '>=', (float)$f['price_min']);
        }
        if (isset($f['price_max']) && $f['price_max'] !== null) {
            $q->where('price', '<=', (float)$f['price_max']);
        }

        return $q;
    }

    /** ترتيب */
    public function scopeSorted(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'newest'      => $q->orderByDesc('created_at'),
            'price_asc'   => $q->orderBy('price')->orderByDesc('created_at'),
            'price_desc'  => $q->orderByDesc('price')->orderByDesc('created_at'),
            'rating_desc' => $q->orderByDesc('avg_rating')->orderByDesc('ratings_count')->orderByDesc('created_at'),
            default       => $q->orderByDesc('created_at'), // relevance: حاليًا تقريب للبساطة
        };
    }
}
