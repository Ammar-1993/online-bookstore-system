<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{
    const SESSION_KEY = 'compare.items';
    const MAX_ITEMS = 4;

    public function index(Request $request)
    {
        $ids = $this->ids($request);
        $books = Book::query()
            ->with(['authors:id,name,slug', 'publisher:id,name,slug', 'category:id,name,slug'])
            ->whereIn('id', $ids)
            ->get()
            // نحافظ على ترتيب الإضافة
            ->sortBy(fn($b) => array_search($b->id, $ids))
            ->values();

        return view('compare.index', [
            'books' => $books,
            'ids'   => $ids,
        ]);
    }

    public function toggle(Request $request, Book $book)
    {
        if ($request->user()) {
            $user = $request->user();
            $exists = $user->compareBooks()->where('book_id', $book->id)->exists();

            if ($exists) {
                $user->compareBooks()->detach($book->id);
            } else {
                if ($user->compareBooks()->count() >= self::MAX_ITEMS) {
                    return response()->json(['ok' => false, 'reason' => 'limit', 'max' => self::MAX_ITEMS], 422);
                }
                $user->compareBooks()->syncWithoutDetaching([$book->id]);
            }
            $this->forget($user->id);

            $count = $user->compareBooks()->count();
            return response()->json(['ok' => true, 'active' => ! $exists, 'count' => $count]);
        }

        // ضيف: Session
        $ids = $this->sessionIds($request);
        $active = in_array($book->id, $ids, true);

        if ($active) {
            $ids = array_values(array_diff($ids, [$book->id]));
        } else {
            if (count($ids) >= self::MAX_ITEMS) {
                return response()->json(['ok' => false, 'reason' => 'limit', 'max' => self::MAX_ITEMS], 422);
            }
            $ids[] = $book->id;
        }
        $request->session()->put(self::SESSION_KEY, $ids);

        return response()->json(['ok' => true, 'active' => ! $active, 'count' => count($ids)]);
    }

    public function destroy(Request $request, Book $book)
    {
        if ($request->user()) {
            $request->user()->compareBooks()->detach($book->id);
            $this->forget($request->user()->id);
        } else {
            $ids = $this->sessionIds($request);
            $ids = array_values(array_diff($ids, [$book->id]));
            $request->session()->put(self::SESSION_KEY, $ids);
        }

        return back()->with('success', 'أُزيل من المقارنة.');
    }

    /** Helpers */
    private function ids(Request $request): array
    {
        if ($request->user()) {
            $userId = $request->user()->id;
            return Cache::remember("compare:{$userId}", now()->addMinutes(10), function () use ($userId) {
                return DB::table('compares')->where('user_id', $userId)->pluck('book_id')->all();
            });
        }
        return $this->sessionIds($request);
    }

    private function sessionIds(Request $request): array
    {
        return array_values(array_unique(array_map('intval', (array) $request->session()->get(self::SESSION_KEY, []))));
    }

    private function forget(int $userId): void
    {
        Cache::forget("compare:{$userId}");
        Cache::forget("compare_count:{$userId}");
    }
}
