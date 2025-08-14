<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('status'); // approved|pending|null
        $q      = trim((string)$request->get('q',''));

        $reviews = Review::query()
            ->with(['book:id,title,slug,seller_id','user:id,name,email'])
            ->when(auth()->user()->hasRole('Seller'), function (Builder $b) {
                $b->forSeller(auth()->id()); // من سكوب Review
            })
            ->when($filter === 'approved', fn($b) => $b->where('approved', true))
            ->when($filter === 'pending',  fn($b) => $b->where('approved', false))
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->whereHas('book', fn($bb) => $bb->where('title', 'like', "%{$q}%"))
                  ->orWhereHas('user', fn($uu) => $uu->where('name', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews','filter','q'));
    }

    public function toggle(Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);
        $review->update(['approved' => ! $review->approved]);

        return back()->with('success', 'تم تحديث حالة المراجعة.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);
        $review->delete();

        return back()->with('success', 'تم حذف المراجعة.');
    }
}
