<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $books = $user->wishlistBooks()
            ->with(['authors:id,name,slug', 'publisher:id,name,slug', 'category:id,name,slug'])
            ->latest('wishlists.created_at')
            ->paginate(12);

        $wishlistIds = $this->ids($user->id);

        return view('wishlist.index', compact('books', 'wishlistIds'));
    }

    public function store(Request $request, Book $book)
    {
        $user = $request->user();
        $user->wishlistBooks()->syncWithoutDetaching([$book->id]);
        $this->forget($user->id);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'active' => true]);
        }

        return back()->with('success', 'تمت الإضافة إلى المفضّلة.');
    }

    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();
        $user->wishlistBooks()->detach($book->id);
        $this->forget($user->id);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'active' => false]);
        }

        return back()->with('success', 'تمت الإزالة من المفضّلة.');
    }

    public function toggle(Request $request, Book $book)
    {
        $user = $request->user();
        $attached = $user->wishlistBooks()->where('book_id', $book->id)->exists();

        $attached
            ? $user->wishlistBooks()->detach($book->id)
            : $user->wishlistBooks()->syncWithoutDetaching([$book->id]);

        $this->forget($user->id);

        return response()->json(['ok' => true, 'active' => ! $attached]);
    }

    /*** Caching helpers ***/
    private function ids(int $userId): array
    {
        return Cache::remember("wishlist:{$userId}", now()->addMinutes(10), function () use ($userId) {
            return \DB::table('wishlists')->where('user_id', $userId)->pluck('book_id')->all();
        });
    }

    private function forget(int $userId): void
    {
        Cache::forget("wishlist:{$userId}");
        Cache::forget("wishlist_count:{$userId}");
    }
}
