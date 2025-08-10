<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','bio','photo_path'];

    public function books() { return $this->belongsToMany(Book::class); } // author_book
}
