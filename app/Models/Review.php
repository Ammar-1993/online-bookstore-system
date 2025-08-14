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
}
