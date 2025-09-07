<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;
use App\Http\Requests\BookFilterRequest;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Author;


class BookController extends Controller
{
    public function show(Book $book): View
    {
        // العلاقات الأساسية لعرض التفاصيل
        $book->load([
            'category:id,name,slug',
            'publisher:id,name,slug',
            'authors:id,name,slug',
        ]);

        // المراجعات المعتمدة + المستخدم + ترقيم الصفحات
        $reviews = $book->reviews()
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->onEachSide(1)
            ->fragment('reviews');

        // متوسط وعدد التقييمات (باستخدام الحقول المخبأة إن وُجدت وإلا fallback)
        $avgRating    = (float) ($book->ratings_avg   ?? 0);
        $ratingsCount = (int)   ($book->ratings_count ?? 0);

        if ($ratingsCount === 0) {
            $agg = $book->reviews()
                ->approved()
                ->selectRaw('COUNT(*) as c, COALESCE(AVG(rating),0) as a')
                ->first();
            $ratingsCount = (int) ($agg->c ?? 0);
            $avgRating    = round((float) ($agg->a ?? 0), 2);
        }

        // توزيع التقييمات: group by rating
        $grouped = $book->reviews()
            ->approved()
            ->selectRaw('rating, COUNT(*) as c')
            ->groupBy('rating')
            ->pluck('c', 'rating'); // [rating => count]

        $starsDist = [];
        for ($i = 1; $i <= 5; $i++) {
            $starsDist[$i] = (int) ($grouped[$i] ?? 0);
        }

        $total = array_sum($starsDist);
        $starsPercent = [];
        foreach ($starsDist as $i => $c) {
            $starsPercent[$i] = $total > 0 ? round(($c * 100) / $total, 0) : 0;
        }

        // كتب مرتبطة: أولوية للتصنيف، ثم الناشر، وإلا أحدث المنشور
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
            'book'          => $book,
            'reviews'       => $reviews,
            'avgRating'     => $avgRating,
            'ratingsCount'  => $ratingsCount,
            'starsDist'     => $starsDist,
            'starsPercent'  => $starsPercent,
            'related'       => $related,
        ]);
    }

    // ...

    public function index(BookFilterRequest $request)
    {
        $filters = $request->validated();

        $query = Book::query()
            ->with(['authors:id,name,slug', 'publisher:id,name,slug', 'category:id,name,slug'])
            ->filter($filters)
            ->sorted($filters['sort'] ?? null);

        $books = $query->paginate($filters['per_page'])->withQueryString();

        // خيارات القوائم (يُمكن كاشها لاحقًا)
        $categories = Category::query()->select('id','name','slug')->orderBy('name')->get();
        $publishers = Publisher::query()->select('id','name','slug')->orderBy('name')->get();
        $authors    = Author::query()->select('id','name','slug')->orderBy('name')->take(200)->get();

        return view('books.index', compact('books', 'categories', 'publishers', 'authors', 'filters'));
    }

    // جزء النتائج (AJAX)
    public function search(BookFilterRequest $request)
    {
        $filters = $request->validated();

        $books = Book::query()
            ->with(['authors:id,name,slug', 'publisher:id,name,slug', 'category:id,name,slug'])
            ->filter($filters)
            ->sorted($filters['sort'] ?? null)
            ->paginate($filters['per_page'])
            ->withQueryString();

        // نرجّع جزء HTML (Blade) لسهولة الاستبدال
        return view('books.partials.grid', compact('books'));
    }
}
