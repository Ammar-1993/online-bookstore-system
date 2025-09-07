<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- أفضل الكتب --}}
    <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
        <h3 class="font-semibold mb-3">أفضل الكتب</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500 dark:text-gray-400">
                    <tr class="text-start">
                        <th class="py-2 pe-4">الكتاب</th>
                        <th class="py-2 pe-4 text-end">الكمية</th>
                        <th class="py-2 text-end">الإيراد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topBooks as $r)
                        <tr class="border-t border-black/5 dark:border-white/10">
                            <td class="py-2 pe-4">{{ $r->title }}</td>
                            <td class="py-2 pe-4 text-end tabular-nums">{{ (int) $r->qty }}</td>
                            <td class="py-2 text-end tabular-nums">{{ number_format((float) $r->revenue, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">لا توجد بيانات ضمن
                                النطاق المحدّد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- أفضل التصنيفات --}}
    <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
        <h3 class="font-semibold mb-3">أفضل التصنيفات</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500 dark:text-gray-400">
                    <tr class="text-start">
                        <th class="py-2 pe-4">التصنيف</th>
                        <th class="py-2 pe-4 text-end">الكمية</th>
                        <th class="py-2 text-end">الإيراد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCategories as $r)
                        <tr class="border-t border-black/5 dark:border-white/10">
                            <td class="py-2 pe-4">{{ $r->name ?? '—' }}</td>
                            <td class="py-2 pe-4 text-end tabular-nums">{{ (int) $r->qty }}</td>
                            <td class="py-2 text-end tabular-nums">{{ number_format((float) $r->revenue, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">لا توجد بيانات ضمن
                                النطاق المحدّد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- أفضل المؤلفين --}}
    <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
        <h3 class="font-semibold mb-3">أفضل المؤلفين</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500 dark:text-gray-400">
                    <tr class="text-start">
                        <th class="py-2 pe-4">المؤلف</th>
                        <th class="py-2 pe-4 text-end">الكمية</th>
                        <th class="py-2 text-end">الإيراد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topAuthors as $r)
                        <tr class="border-t border-black/5 dark:border-white/10">
                            <td class="py-2 pe-4">{{ $r->name ?? '—' }}</td>
                            <td class="py-2 pe-4 text-end tabular-nums">{{ (int) $r->qty }}</td>
                            <td class="py-2 text-end tabular-nums">{{ number_format((float) $r->revenue, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">لا توجد بيانات ضمن
                                النطاق المحدّد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- أفضل الناشرين --}}
    <div class="rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10">
        <h3 class="font-semibold mb-3">أفضل الناشرين</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500 dark:text-gray-400">
                    <tr class="text-start">
                        <th class="py-2 pe-4">الناشر</th>
                        <th class="py-2 pe-4 text-end">الكمية</th>
                        <th class="py-2 text-end">الإيراد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topPublishers as $r)
                        <tr class="border-t border-black/5 dark:border-white/10">
                            <td class="py-2 pe-4">{{ $r->name ?? '—' }}</td>
                            <td class="py-2 pe-4 text-end tabular-nums">{{ (int) $r->qty }}</td>
                            <td class="py-2 text-end tabular-nums">{{ number_format((float) $r->revenue, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 dark:text-gray-400">لا توجد بيانات ضمن
                                النطاق المحدّد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>