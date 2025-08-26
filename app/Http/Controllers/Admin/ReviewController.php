<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Book, User}; // ← أضف Book و User



class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('status'); // approved | pending | null
        $q = trim((string) $request->get('q', ''));

        // فرز آمن
        $sort = $request->get('sort', 'created_at');   // id | rating | approved | created_at | user | book
        $dir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['id', 'rating', 'approved', 'created_at', 'user', 'book'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $query = Review::query()
            ->with(['book:id,title,slug,seller_id', 'user:id,name,email'])
            ->when(auth()->user()->hasRole('Seller'), fn(Builder $b) => $b->forSeller(auth()->id()))
            ->when($filter === 'approved', fn($b) => $b->where('approved', true))
            ->when($filter === 'pending', fn($b) => $b->where('approved', false))
            // بحث مجمّع (كتاب أو مستخدم)
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(function (Builder $g) use ($q) {
                    $g->whereHas('book', fn($bb) => $bb->where('title', 'like', "%{$q}%"))
                        ->orWhereHas('user', fn($uu) => $uu->where('name', 'like', "%{$q}%"));
                });
            });

        // تطبيق الترتيب
        if (in_array($sort, ['id', 'rating', 'approved', 'created_at'], true)) {
            $query->orderBy($sort, $dir);
        } elseif ($sort === 'user') {
            $query->orderBy(
                User::select('name')->whereColumn('users.id', 'reviews.user_id'),
                $dir
            );
        } elseif ($sort === 'book') {
            $query->orderBy(
                Book::select('title')->whereColumn('books.id', 'reviews.book_id'),
                $dir
            );
        }

        $reviews = $query->paginate(12)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'filter', 'q', 'sort', 'dir'));
    }


    public function toggle(Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);

        $review->update(['approved' => !$review->approved]);

        // تحديث كاش التقييمات
        $review->book?->recalculateRatings();

        return back()->with('success', 'تم تحديث حالة المراجعة.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);
        $book = $review->book;
        $review->delete();

        $book?->recalculateRatings();

        return back()->with('success', 'تم حذف المراجعة.');
    }
}
