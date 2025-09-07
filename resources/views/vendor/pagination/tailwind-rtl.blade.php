@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        <!-- سطح المكتب -->
        <div class="hidden sm:flex items-center">
            {{-- السابق --}}
            @if ($paginator->onFirstPage())
                <span
                    class="px-3 py-2 ms-1 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-400 cursor-not-allowed select-none"
                    aria-disabled="true" aria-label="السابق">
                    السابق
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 ms-1 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors"
                   rel="prev" aria-label="السابق" title="العودة للصفحة السابقة">
                    السابق
                </a>
            @endif

            {{-- الصفحات --}}
            <ul class="flex flex-row-reverse items-center mx-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="px-3 py-2 text-slate-500 dark:text-slate-400 select-none">…</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <span
                                        aria-current="page"
                                        class="px-3 py-2 mx-0.5 rounded border border-indigo-600 bg-indigo-600 text-white select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}"
                                       class="px-3 py-2 mx-0.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors"
                                       aria-label="اذهب إلى الصفحة {{ $page }}" title="الذهاب إلى الصفحة {{ $page }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>

            {{-- التالي --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 me-1 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors"
                   rel="next" aria-label="التالي" title="الانتقال للصفحة التالية">
                    التالي
                </a>
            @else
                <span
                    class="px-3 py-2 me-1 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-400 cursor-not-allowed select-none"
                    aria-disabled="true" aria-label="التالي">
                    التالي
                </span>
            @endif
        </div>

        <!-- الجوال -->
        <div class="flex sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span
                    class="px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-400 cursor-not-allowed select-none"
                    aria-disabled="true" aria-label="السابق">
                    السابق
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors"
                   rel="prev" aria-label="السابق" title="العودة">
                    السابق
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors"
                   rel="next" aria-label="التالي" title="التالي">
                    التالي
                </a>
            @else
                <span
                    class="px-3 py-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-400 cursor-not-allowed select-none"
                    aria-disabled="true" aria-label="التالي">
                    التالي
                </span>
            @endif
        </div>
    </nav>
@endif
