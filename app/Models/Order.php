<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','status','payment_status','total_amount','currency','shipping_address','billing_address','placed_at'];
    protected $casts = ['shipping_address'=>'array','billing_address'=>'array','placed_at'=>'datetime'];

    public function items(){ return $this->hasMany(OrderItem::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
