<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','book_id','qty','unit_price','total_price'];

    public function order(){ return $this->belongsTo(Order::class); }
    public function book(){ return $this->belongsTo(Book::class); }
}
