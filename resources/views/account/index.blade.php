<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة حسابي - المتجر الإلكتروني للكتب</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    {{-- الشريط العلوي (موحّد الأسلوب مع الواجهة العامة) --}}
    <header
        class="sticky top-0 z-40 backdrop-blur bg-white/85 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="h-14 flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group" data-ripple data-loader>
                    <span
                        class="inline-grid place-items-center w-8 h-8 rounded-xl bg-indigo-600 text-white shadow-sm">📚</span>
                    <span
                        class="font-display font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition">
                        المتجر الإلكتروني للكتب
                    </span>
                </a>

                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50/70 dark:hover:bg-white/5">
                        الرئيسية
                    </a>
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50/70 dark:hover:bg-white/5">
                        طلباتي
                    </a>
                    <button type="button" data-open-profile
                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50/70 dark:hover:bg-white/5">
                        تحديث الملف الشخصي
                    </button>
                </nav>

                <div class="flex items-center gap-2">
                    @include('partials.theme-toggle-button')

                    {{-- قائمة بسيطة للمحمول --}}
                    <div class="md:hidden">
                        <button id="acc-burger" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10"
                            aria-label="القائمة">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="acc-mobile" class="md:hidden hidden pb-3">
                <nav class="grid gap-1 pt-2">
                    <a href="{{ route('home') }}"
                        class="px-2 py-1.5 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-white/10">الرئيسية</a>
                    <a href="{{ route('orders.index') }}"
                        class="px-2 py-1.5 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-white/10">طلباتي</a>
                    <button type="button" data-open-profile
                        class="text-start px-2 py-1.5 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-white/10">تحديث
                        الملف الشخصي</button>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <x-flash-stack />

        {{-- الترحيب + أزرار سريعة --}}
        @php $user = $user ?? auth()->user(); @endphp
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-display font-semibold">مرحبًا {{ $user->name }} 👋</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">هذا ملخّص حسابك ونشاطك الأخير.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}"
                    class="rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm" data-ripple
                    data-loader>تابع التسوّق</a>
                <a href="{{ route('orders.index') }}"
                    class="rounded-xl px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">عرض
                    كل الطلبات</a>
                <button type="button" data-open-profile
                    class="rounded-xl px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">تحديث
                    الملف الشخصي</button>
            </div>
        </div>

        {{-- بطاقات المؤشرات --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">إجمالي الطلبات</div>
                <div class="text-2xl font-semibold tabular-nums">{{ number_format($totalOrders) }}</div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">طلبات نشِطة</div>
                <div class="text-2xl font-semibold tabular-nums">{{ number_format($activeOrders) }}</div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">طلبات غير مدفوعة</div>
                <div class="text-2xl font-semibold tabular-nums">{{ number_format($unpaidOrders) }}</div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">إجمالي المدفوع</div>
                <div class="text-2xl font-semibold tabular-nums">
                    {{ number_format($totalPaidAmount, 2) }} {{ config('app.currency', 'USD') }}
                </div>
            </div>
        </div>

        {{-- بيانات أساسية --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5 mb-8">
            <div class="font-display font-semibold mb-3">بياناتي</div>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">الاسم</div>
                    <div class="font-medium">{{ $user->name }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">البريد الإلكتروني</div>
                    <div class="font-medium" dir="ltr">{{ $user->email }}</div>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <button type="button" data-open-profile
                    class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline">
                    إدارة الملف الشخصي
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M8 5l8 7-8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- آخر الطلبات --}}
        @php
            $statusBadge = fn($s) => match ($s) {
                'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300',
                'shipped' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                default => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300'
            };
            $payBadge = fn($s) => match ($s) {
                'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                'refunded' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                'unpaid' => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300',
                default => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300'
            };
        @endphp

        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 overflow-x-auto">
            <div class="flex items-center justify-between p-5">
                <div class="font-display font-semibold">آخر الطلبات</div>
                <a href="{{ route('orders.index') }}"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">كل الطلبات</a>
            </div>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-14 z-10">
                    <tr class="text-right text-gray-700 dark:text-gray-200">
                        <th class="px-4 py-2">رقم الطلب</th>
                        <th class="px-4 py-2">التاريخ</th>
                        <th class="px-4 py-2 text-center">الحالة</th>
                        <th class="px-4 py-2 text-center">الدفع</th>
                        <th class="px-4 py-2 text-right">الإجمالي</th>
                        <th class="px-4 py-2 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentOrders as $o)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5">
                            <td class="px-4 py-2 tabular-nums">
                                <span
                                    class="text-indigo-600 dark:text-indigo-400">{{ method_exists($o, 'getNumberAttribute') ? $o->number : $o->id }}</span>
                            </td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap"
                                title="{{ $o->created_at->format('Y-m-d H:i') }}">
                                {{ $o->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $statusBadge($o->status) }}">{{ $o->status }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $payBadge($o->payment_status ?? 'unpaid') }}">
                                    {{ $o->payment_status ?? 'unpaid' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right tabular-nums">
                                {{ number_format($o->total_amount, 2) }} {{ $o->currency ?? config('app.currency', 'USD') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('orders.show', $o) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                                    data-ripple>عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-300">لا توجد طلبات
                                بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    {{-- النافذة المنبثقة لتحديث الملف الشخصي (تُحمّل مرة واحدة) --}}
    @include('account.partials.profile-modal') {{-- تأكد أن الرابط داخلها يستخدم route("account.dashboard") وليس
    account.index --}}

    <x-page-loader />

    <script>
        // فتح/إغلاق قائمة المحمول
        document.getElementById('acc-burger')?.addEventListener('click', () => {
            document.getElementById('acc-mobile')?.classList.toggle('hidden');
        });

        // فتح المودال من أي زر يحمل data-open-profile
        document.querySelectorAll('[data-open-profile]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('profile-modal')?.classList.remove('hidden');
            });
        });
    </script>
</body>

</html>