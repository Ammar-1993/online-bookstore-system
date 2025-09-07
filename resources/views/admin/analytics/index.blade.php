@extends('admin.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 space-y-6">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">لوحة التحليلات</h1>

            <form method="GET" class="flex items-center gap-2 flex-wrap" aria-label="فلاتر التحليلات">
                {{-- Range --}}
                <select id="range" name="range"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2">
                    <option value="last_7" @selected($range === 'last_7')>آخر 7 أيام</option>
                    <option value="last_30" @selected($range === 'last_30')>آخر 30 يومًا</option>
                    <option value="last_90" @selected($range === 'last_90')>آخر 90 يومًا</option>
                    <option value="this_month" @selected($range === 'this_month')>هذا الشهر</option>
                    <option value="custom" @selected($range === 'custom')>مخصص</option>
                </select>

                {{-- من/إلى --}}
                <input id="from" type="date" name="from" value="{{ $from }}"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2 w-36">
                <input id="to" type="date" name="to" value="{{ $to   }}"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2 w-36">

                {{-- فلاتر الدومين --}}
                <select name="category_id"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected($selectedCategory === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="publisher_id"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2">
                    <option value="">كل الناشرين</option>
                    @foreach($publishers as $p)
                        <option value="{{ $p->id }}" @selected($selectedPublisher === $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>

                <select name="author_id"
                    class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm px-3 py-2">
                    <option value="">كل المؤلفين</option>
                    @foreach($authors as $a)
                        <option value="{{ $a->id }}" @selected($selectedAuthor === $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>

                <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">تطبيق</button>

                {{-- تصدير CSV يحافظ على الفلاتر الحالية --}}
                <a href="{{ route('admin.analytics.export', request()->query()) }}"
                    class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-sm">
                    تصدير CSV
                </a>
            </form>
        </div>

        {{-- KPIs + مقارنة الفترة السابقة --}}
        @php
            $revDelta = $prevRevenue > 0 ? (($revenue - $prevRevenue) / max(1e-9, $prevRevenue)) * 100 : null;
            $ordDelta = $prevOrders > 0 ? (($ordersCount - $prevOrders) / max(1, $prevOrders)) * 100 : null;
          @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
                <div class="text-sm text-gray-500 dark:text-gray-400">الإيراد</div>
                <div class="mt-2 text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                    {{ number_format($revenue, 2) }} {{ $currency }}
                </div>
                <div class="text-xs mt-1">
                    @if(!is_null($revDelta))
                        <span class="{{ $revDelta >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $revDelta >= 0 ? '▲' : '▼' }} {{ number_format(abs($revDelta), 1) }}%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">مقارنة بالفترة السابقة</span>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
                <div class="text-sm text-gray-500 dark:text-gray-400">عدد الطلبات المدفوعة</div>
                <div class="mt-2 text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                    {{ number_format($ordersCount) }}
                </div>
                <div class="text-xs mt-1">
                    @if(!is_null($ordDelta))
                        <span class="{{ $ordDelta >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $ordDelta >= 0 ? '▲' : '▼' }} {{ number_format(abs($ordDelta), 1) }}%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">مقارنة بالفترة السابقة</span>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
                <div class="text-sm text-gray-500 dark:text-gray-400">متوسط قيمة الطلب (AOV)</div>
                <div class="mt-2 text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                    {{ number_format($aov, 2) }} {{ $currency }}
                </div>
            </div>

            <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
                <div class="text-sm text-gray-500 dark:text-gray-400">العناصر المباعة</div>
                <div class="mt-2 text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">
                    {{ number_format($itemsSold) }}
                </div>
            </div>
        </div>


        {{-- الرسم --}}
        <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">الإيراد والطلبات حسب اليوم</h2>
                <div class="text-xs text-gray-500">{{ $from }} → {{ $to }}</div>
            </div>
            <div class="relative h-80 md:h-96"><canvas id="revChart"></canvas></div>
        </div>

        {{-- الجداول (كما هي في الدفعة 1) --}}
        @include('admin.analytics.partials.top_tables')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const rangeSel = document.getElementById('range');
            const fromEl = document.getElementById('from');
            const toEl = document.getElementById('to');
            const toggleDates = () => {
                const custom = rangeSel.value === 'custom';
                fromEl.disabled = toEl.disabled = !custom;
                fromEl.classList.toggle('opacity-50', !custom);
                toEl.classList.toggle('opacity-50', !custom);
            };
            toggleDates(); rangeSel.addEventListener('change', toggleDates);

            const labels = @json($labels);
            const rev = @json($seriesRevenue);
            const ord = @json($seriesOrders);

            const ctx = document.getElementById('revChart').getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            const tickColor = isDark ? '#E5E7EB' : '#1F2937';
            const gridColor = isDark ? 'rgba(148,163,184,.25)' : 'rgba(148,163,184,.35)';

            if (window.__revChart) window.__revChart.destroy();

            window.__revChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'الإيراد', data: rev, tension: 0.35, yAxisID: 'y1', pointRadius: 2, borderWidth: 2 },
                        { label: 'الطلبات', data: ord, tension: 0.35, yAxisID: 'y2', pointRadius: 2, borderWidth: 2 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { color: tickColor } },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed.y ?? 0;
                                    const money = ctx.dataset.yAxisID === 'y1';
                                    return `${ctx.dataset.label}: ` + new Intl.NumberFormat('ar', { maximumFractionDigits: money ? 2 : 0 }).format(v) + (money ? ' {{ $currency }}' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { color: tickColor, maxTicksLimit: 10, maxRotation: 0 }, grid: { color: gridColor } },
                        y1: { position: 'left', beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } },
                        y2: { position: 'right', beginAtZero: true, ticks: { color: tickColor }, grid: { display: false } },
                    }
                }
            });

            document.getElementById('theme-toggle')?.addEventListener('click', () => {
                setTimeout(() => {
                    const dark = document.documentElement.classList.contains('dark');
                    const t = dark ? '#E5E7EB' : '#1F2937';
                    const g = dark ? 'rgba(148,163,184,.25)' : 'rgba(148,163,184,.35)';
                    const ch = window.__revChart;
                    ch.options.plugins.legend.labels.color = t;
                    ch.options.scales.x.ticks.color = t; ch.options.scales.x.grid.color = g;
                    ch.options.scales.y1.ticks.color = t; ch.options.scales.y1.grid.color = g;
                    ch.options.scales.y2.ticks.color = t;
                    ch.update();
                }, 0);
            });
        })();
    </script>
@endsection