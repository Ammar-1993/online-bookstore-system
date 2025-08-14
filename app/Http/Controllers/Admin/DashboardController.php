<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Book, Category, Publisher, Author, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        // إحصاءات عامة للكتب
        $totalBooks     = Book::count();
        $publishedBooks = Book::where('status', 'published')->count();
        $draftBooks     = Book::where('status', 'draft')->count();
        $lowStockCount  = Book::where('stock_qty', '<', 5)->count();

        // كيانات أخرى (مع فحوصات احتياطية)
        $categoriesCount = Schema::hasTable('categories') ? Category::count() : 0;
        $authorsCount    = Schema::hasTable('authors')    ? Author::count()    : 0;
        $publishersCount = Schema::hasTable('publishers') ? Publisher::count() : 0;
        $usersCount      = User::count();

        // أعلى التصنيفات (حسب عدد الكتب)
        $topCategories = Category::withCount('books')
            ->orderByDesc('books_count')
            ->take(5)->get(['id','name','slug']);

        // أحدث الكتب
        $recentBooks = Book::with(['category:id,name,slug','publisher:id,name,slug'])
            ->latest('id')
            ->take(8)
            ->get(['id','title','slug','status','category_id','publisher_id','created_at']);

        // سلسلة عدد الكتب المضافة لآخر 7 أيام
        $from = now()->subDays(6)->startOfDay();
        $raw  = Book::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd')
            ->all();

        $labels = [];
        $series = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('m/d'); // أو استخدم العربية عند تفعيلها
            $series[] = (int)($raw[$day] ?? 0);
        }

        return view('admin.dashboard', [
            'metrics' => [
                'totalBooks'     => $totalBooks,
                'publishedBooks' => $publishedBooks,
                'draftBooks'     => $draftBooks,
                'lowStockCount'  => $lowStockCount,

                'categoriesCount'=> $categoriesCount,
                'authorsCount'   => $authorsCount,
                'publishersCount'=> $publishersCount,
                'usersCount'     => $usersCount,
            ],
            'topCategories' => $topCategories,
            'recentBooks'   => $recentBooks,
            'chart'         => [
                'labels' => $labels,
                'series' => $series,
            ],
        ]);
    }
}
