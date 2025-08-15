@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center" dir="rtl">
        <div class="hidden sm:flex">
            {{-- السابق --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 ms-1 rounded border bg-white text-gray-400 cursor-not-allowed select-none">
                    السابق
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 ms-1 rounded border bg-white hover:bg-gray-50 text-gray-700"
                   rel="prev">
                    السابق
                </a>
            @endif

            {{-- الصفحات --}}
            <ul class="flex flex-row-reverse items-center mx-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="px-3 py-2 text-gray-500 select-none">…</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="px-3 py-2 mx-0.5 rounded border border-indigo-600 bg-indigo-600 text-white select-none">{{ $page }}</li>
                            @else
                                <li>
                                    <a href="{{ $url }}"
                                       class="px-3 py-2 mx-0.5 rounded border bg-white hover:bg-gray-50 text-gray-700">
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
                   class="px-3 py-2 me-1 rounded border bg-white hover:bg-gray-50 text-gray-700"
                   rel="next">
                    التالي
                </a>
            @else
                <span class="px-3 py-2 me-1 rounded border bg-white text-gray-400 cursor-not-allowed select-none">
                    التالي
                </span>
            @endif
        </div>

        {{-- موبايل --}}
        <div class="flex sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded border bg-white text-gray-400 cursor-not-allowed select-none">السابق</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 rounded border bg-white hover:bg-gray-50 text-gray-700">السابق</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 rounded border bg-white hover:bg-gray-50 text-gray-700">التالي</a>
            @else
                <span class="px-3 py-2 rounded border bg-white text-gray-400 cursor-not-allowed select-none">التالي</span>
            @endif
        </div>
    </nav>
@endif
