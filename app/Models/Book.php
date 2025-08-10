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
        'category_id','publisher_id','seller_id'
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function category()  { return $this->belongsTo(Category::class); }
    public function publisher() { return $this->belongsTo(Publisher::class); }
    public function authors()   { return $this->belongsToMany(Author::class); } // author_book
    public function reviews()   { return $this->hasMany(Review::class); }
    public function seller()    { return $this->belongsTo(User::class, 'seller_id'); }
}
