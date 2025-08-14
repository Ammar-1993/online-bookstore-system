<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('create', Review::class);

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->id],
            [
                'rating'   => (int)$request->rating,
                'comment'  => $request->comment,
                // عدّلها لـ false لو تبغى تمر بالمراجعة اليدوية
                'approved' => true,
            ]
        );

        return back()->with('success', 'تم حفظ تقييمك بنجاح.')->withFragment('reviews');
    }

    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update([
            'rating'  => (int)$request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'تم تحديث المراجعة.')->withFragment('reviews');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        $review->delete();

        return back()->with('success', 'تم حذف المراجعة.')->withFragment('reviews');
    }
}
