<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Routing: use slug in URLs
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Auto-generate unique slug when saving
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            // لو ما أُرسل slug، نولده من الاسم
            if (blank($category->slug) && filled($category->name)) {
                $base = Str::slug($category->name, '-');
                $slug = $base;
                $i = 1;

                // تأكد من uniqueness (يتجاهل السجل الحالي عند التحديث)
                while (
                    static::where('slug', $slug)
                        ->when($category->exists, fn ($q) => $q->where('id', '!=', $category->id))
                        ->exists()
                ) {
                    $slug = $base.'-'.$i++;
                }

                $category->slug = $slug;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (اختياري)
    |--------------------------------------------------------------------------
    */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
