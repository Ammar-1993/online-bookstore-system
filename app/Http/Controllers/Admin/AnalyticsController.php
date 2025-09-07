<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Query\Builder;

class AnalyticsController extends Controller
{
    /** أعمدة الكمية والسعر كنصوص SQL آمنة */
    private function metricSql(): array
    {
        $qtyCol = Schema::hasColumn('order_items', 'quantity') ? 'quantity' :
            (Schema::hasColumn('order_items', 'qty') ? 'qty' : null);

        $priceCol = Schema::hasColumn('order_items', 'unit_price') ? 'unit_price' :
            (Schema::hasColumn('order_items', 'price') ? 'price' : null);

        $qtySql = $qtyCol ? "COALESCE(oi.$qtyCol, 0)" : "0";
        $priceSql = $priceCol ? "COALESCE(oi.$priceCol, 0)" : "0";
        $lineTotalSql = "($qtySql) * ($priceSql)";

        return [$qtySql, $priceSql, $lineTotalSql];
    }

    /** طبِّق الفلاتر (تصنيف/ناشر/مؤلف) على استعلام الطلبات */
    // داخل App\Http\Controllers\Admin\AnalyticsController


    private function applyFilters(Builder $q, Request $r): void
    {
        // سنستخدم alias مختلف لهذه الانضمامات حتى لا تتعارض مع "b" في استعلامات الـ Top
        $needBookJoin = $r->filled('category_id') || $r->filled('publisher_id') || $r->filled('author_id');

        if ($needBookJoin) {
            // books for filters
            $q->join('books as bf', 'bf.id', '=', 'oi.book_id');
        }

        if ($r->filled('category_id')) {
            $q->where('bf.category_id', (int) $r->integer('category_id'));
        }

        if ($r->filled('publisher_id')) {
            $q->where('bf.publisher_id', (int) $r->integer('publisher_id'));
        }

        if ($r->filled('author_id')) {
            // pivot for filters
            $q->join('author_book as abf', 'abf.book_id', '=', 'bf.id')
                ->where('abf.author_id', (int) $r->integer('author_id'));
        }
    }


    /** الفترة السابقة للمقارنة */
    private function previousWindow(string $from, string $to): array
    {
        $fromTs = strtotime($from . ' 00:00:00');
        $toTs = strtotime($to . ' 23:59:59');
        $days = max(1, (int) round(($toTs - $fromTs) / 86400));
        $prevTo = date('Y-m-d', $fromTs - 86400);
        $prevFrom = date('Y-m-d', strtotime("$prevTo -$days days"));
        return [$prevFrom, $prevTo];
    }

    /** الصفحة الرئيسية */
    public function index(Request $r)
    {
        // نطاق التاريخ
        $range = $r->get('range', 'last_30');
        [$from, $to] = match ($range) {
            'last_7' => [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')],
            'last_30' => [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')],
            'last_90' => [date('Y-m-d', strtotime('-89 days')), date('Y-m-d')],
            'this_month' => [date('Y-m-01'), date('Y-m-d')],
            'custom' => [$r->get('from', date('Y-m-d')), $r->get('to', date('Y-m-d'))],
            default => [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')],
        };

        [$qtySql, $priceSql, $lineTotalSql] = $this->metricSql();

        // قوائم الفلاتر
        $categories = DB::table('categories')->orderBy('name')->get(['id', 'name']);
        $publishers = DB::table('publishers')->orderBy('name')->get(['id', 'name']);
        $authors = DB::table('authors')->orderBy('name')->get(['id', 'name']);

        // كاش حسب المفاتيح + الفلاتر
        $cacheKey = 'anx:' . md5(json_encode([
            'from' => $from,
            'to' => $to,
            'c' => $r->get('category_id'),
            'p' => $r->get('publisher_id'),
            'a' => $r->get('author_id'),
        ]));

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($r, $from, $to, $qtySql, $lineTotalSql) {

            // أساس الاستعلامات
            $base = DB::table('orders as o')
                ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
                ->where('o.payment_status', 'paid')
                ->whereBetween('o.created_at', [$from, $to]);

            // إجماليات
            $totals = (clone $base);
            $this->applyFilters($totals, $r);
            $totalsRow = $totals
                ->selectRaw("SUM($lineTotalSql) as revenue, SUM($qtySql) as items_sold, COUNT(DISTINCT o.id) as orders_count")
                ->first();

            $ordersCount = (int) ($totalsRow->orders_count ?? 0);
            $revenue = (float) ($totalsRow->revenue ?? 0);
            $itemsSold = (int) ($totalsRow->items_sold ?? 0);
            $aov = $ordersCount ? ($revenue / $ordersCount) : 0;

            // سلسلة يومية
            $daily = DB::table('orders as o')
                ->leftJoin('order_items as oi', 'oi.order_id', '=', 'o.id')
                ->where('o.payment_status', 'paid')
                ->whereBetween('o.created_at', [$from, $to]);
            $this->applyFilters($daily, $r);
            $daily = $daily
                ->groupByRaw('DATE(o.created_at)')
                ->orderByRaw('DATE(o.created_at)')
                ->selectRaw("DATE(o.created_at) as d, SUM($lineTotalSql) as revenue, COUNT(DISTINCT o.id) as orders_count")
                ->get()
                ->keyBy('d');

            // إعداد المحور الزمني كامل النطاق
            $labels = [];
            $seriesRevenue = [];
            $seriesOrders = [];
            for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) {
                $key = date('Y-m-d', $d);
                $labels[] = $key;
                $seriesRevenue[] = (float) ($daily[$key]->revenue ?? 0);
                $seriesOrders[] = (int) ($daily[$key]->orders_count ?? 0);
            }

            // TOPs
            $topBooks = (clone $base);
            $this->applyFilters($topBooks, $r);
            $topBooks = $topBooks->join('books as b', 'b.id', '=', 'oi.book_id')
                ->groupBy('b.id', 'b.title')
                ->orderByDesc(DB::raw("SUM($qtySql)"))
                ->limit(5)
                ->get([
                    'b.id',
                    'b.title',
                    DB::raw("SUM($qtySql) as qty"),
                    DB::raw("ROUND(SUM($lineTotalSql),2) as revenue"),
                ]);

            $topCategories = (clone $base);
            $this->applyFilters($topCategories, $r);
            $topCategories = $topCategories->join('books as b', 'b.id', '=', 'oi.book_id')
                ->leftJoin('categories as c', 'c.id', '=', 'b.category_id')
                ->groupBy('c.id', 'c.name')
                ->orderByDesc(DB::raw("SUM($qtySql)"))
                ->limit(5)
                ->get([
                    'c.id',
                    'c.name',
                    DB::raw("SUM($qtySql) as qty"),
                    DB::raw("ROUND(SUM($lineTotalSql),2) as revenue"),
                ]);

            $topAuthors = (clone $base);
            $this->applyFilters($topAuthors, $r);
            $topAuthors = $topAuthors->join('books as b', 'b.id', '=', 'oi.book_id')
                ->join('author_book as ab', 'ab.book_id', '=', 'b.id')
                ->join('authors as a', 'a.id', '=', 'ab.author_id')
                ->groupBy('a.id', 'a.name')
                ->orderByDesc(DB::raw("SUM($qtySql)"))
                ->limit(5)
                ->get([
                    'a.id',
                    'a.name',
                    DB::raw("SUM($qtySql) as qty"),
                    DB::raw("ROUND(SUM($lineTotalSql),2) as revenue"),
                ]);

            $topPublishers = (clone $base);
            $this->applyFilters($topPublishers, $r);
            $topPublishers = $topPublishers->join('books as b', 'b.id', '=', 'oi.book_id')
                ->leftJoin('publishers as p', 'p.id', '=', 'b.publisher_id')
                ->groupBy('p.id', 'p.name')
                ->orderByDesc(DB::raw("SUM($qtySql)"))
                ->limit(5)
                ->get([
                    'p.id',
                    'p.name',
                    DB::raw("SUM($qtySql) as qty"),
                    DB::raw("ROUND(SUM($lineTotalSql),2) as revenue"),
                ]);

            return compact(
                'labels',
                'seriesRevenue',
                'seriesOrders',
                'revenue',
                'itemsSold',
                'ordersCount',
                'aov',
                'topBooks',
                'topCategories',
                'topAuthors',
                'topPublishers'
            );
        });

        // مقارنة الفترة السابقة
        [$pf, $pt] = $this->previousWindow($from, $to);
        $prevKey = 'anxprev:' . md5(json_encode([
            'from' => $pf,
            'to' => $pt,
            'c' => $r->get('category_id'),
            'p' => $r->get('publisher_id'),
            'a' => $r->get('author_id'),
        ]));
        $prev = Cache::remember($prevKey, now()->addMinutes(5), function () use ($r, $pf, $pt, $qtySql, $lineTotalSql) {
            $q = DB::table('orders as o')
                ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
                ->where('o.payment_status', 'paid')
                ->whereBetween('o.created_at', [$pf, $pt]);
            $this->applyFilters($q, $r);
            return $q->selectRaw("SUM($lineTotalSql) as revenue, COUNT(DISTINCT o.id) as orders_count")->first();
        });

        $currency = config('app.currency', 'USD');

        return view('admin.analytics.index', array_merge($data, [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'currency' => $currency,
            'prevRevenue' => (float) ($prev->revenue ?? 0),
            'prevOrders' => (int) ($prev->orders_count ?? 0),
            'categories' => $categories,
            'publishers' => $publishers,
            'authors' => $authors,
            // لقوالب الفلاتر
            'selectedCategory' => (int) $r->integer('category_id'),
            'selectedPublisher' => (int) $r->integer('publisher_id'),
            'selectedAuthor' => (int) $r->integer('author_id'),
        ]));
    }

    /** تصدير CSV: تاريخ, الإيراد, الطلبات */
    public function export(Request $r): StreamedResponse
    {
        // نعيد استخدام index() لحساب السلاسل ولكن دون رندر Blade
        $range = $r->get('range', 'last_30');
        [$from, $to] = match ($range) {
            'last_7' => [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')],
            'last_30' => [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')],
            'last_90' => [date('Y-m-d', strtotime('-89 days')), date('Y-m-d')],
            'this_month' => [date('Y-m-01'), date('Y-m-d')],
            'custom' => [$r->get('from', date('Y-m-d')), $r->get('to', date('Y-m-d'))],
            default => [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')],
        };

        [$qtySql, $priceSql, $lineTotalSql] = $this->metricSql();

        $daily = DB::table('orders as o')
            ->leftJoin('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$from, $to]);
        $this->applyFilters($daily, $r);
        $daily = $daily
            ->groupByRaw('DATE(o.created_at)')
            ->orderByRaw('DATE(o.created_at)')
            ->selectRaw("DATE(o.created_at) as d, SUM($lineTotalSql) as revenue, COUNT(DISTINCT o.id) as orders_count")
            ->pluck('revenue', 'd')
            ->toArray();

        $ordersDaily = DB::table('orders as o')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$from, $to])
            ->groupByRaw('DATE(o.created_at)')
            ->orderByRaw('DATE(o.created_at)')
            ->selectRaw("DATE(o.created_at) as d, COUNT(DISTINCT o.id) as orders_count")
            ->pluck('orders_count', 'd')
            ->toArray();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="analytics-' . $from . '_' . $to . '.csv"',
        ];

        return response()->streamDownload(function () use ($from, $to, $daily, $ordersDaily) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM لفتح سليم في Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['date', 'revenue', 'orders']);
            for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) {
                $k = date('Y-m-d', $d);
                fputcsv($out, [$k, number_format((float) ($daily[$k] ?? 0), 2, '.', ''), (int) ($ordersDaily[$k] ?? 0)]);
            }
            fclose($out);
        }, 'analytics.csv', $headers);
    }
}
