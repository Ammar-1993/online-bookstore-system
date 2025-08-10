<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','book_id','rating','comment','approved'];

    public function user(){ return $this->belongsTo(User::class); }
    public function book(){ return $this->belongsTo(Book::class); }
}
