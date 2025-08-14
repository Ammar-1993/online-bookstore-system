<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class BookController extends Controller
{
    public function show(Book $book): View
    {
        // تفاصيل وعلاقات أساسية
        $book->load([
            'category:id,name,slug',
            'publisher:id,name,slug',
            'authors:id,name,slug',
        ]);

        // إحصاءات التقييمات المعتمدة فقط
        $book->loadCount([
            'reviews as ratings_count' => fn ($q) => $q->where('approved', true),
        ])->loadAvg(
            ['reviews as avg_rating' => fn ($q) => $q->where('approved', true)],
            'rating'
        );

        // مراجعات معتمدة + ترقيم صفحات
        $reviews = $book->reviews()
            ->where('approved', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->onEachSide(1)
            ->fragment('reviews');

        // كتب مرتبطة: أولاً بنفس التصنيف إن وُجد، وإلا نحاول بالمُعلِن/الناشر، وإلا نعرض أحدث المنشور
        $related = Book::query()
            ->where('id', '!=', $book->id)
            ->when($book->category_id, fn($q) => $q->where('category_id', $book->category_id))
            ->when(!$book->category_id && $book->publisher_id, fn($q) => $q->where('publisher_id', $book->publisher_id))
            ->where('status', 'published')
            ->select(['id','title','slug','cover_image_path','price','currency','category_id','publisher_id','published_at'])
            ->latest('published_at')
            ->latest('id')
            ->take(8)
            ->get();

        return view('books.show', [
            'book'         => $book,
            'reviews'      => $reviews,
            'avgRating'    => (float) ($book->avg_rating ?? 0),
            'ratingsCount' => (int)   ($book->ratings_count ?? 0),
            'related'      => $related, // <-- مهم
        ]);
    }
}
