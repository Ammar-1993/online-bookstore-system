<section id="reviews" class="mt-10">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">التقييمات والمراجعات</h2>

    @foreach (['success' => 'green', 'error' => 'red'] as $key => $color)
        @if (session($key))
            <div x-data="{show:true}" x-init="setTimeout(()=>show=false,10000)" x-show="show"
                 class="mb-3 rounded border border-{{ $color }}-200 bg-{{ $color }}-50 text-{{ $color }}-800 dark:bg-{{ $color }}-500/10 dark:text-{{ $color }}-300 dark:border-{{ $color }}-500/20 px-3 py-2">
                {{ session($key) }}
            </div>
        @endif
    @endforeach

    @php $roundedAvg = isset($avgRating) ? round((float) $avgRating) : 0; @endphp
    @if(isset($ratingsCount) && (int) $ratingsCount > 0)
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            {{-- الكارد الأساسي لمتوسط التقييم --}}
            <div class="shrink-0 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-white/10 p-4 w-full md:w-64">
                <div class="text-sm text-gray-600 dark:text-gray-400">المتوسط</div>
                <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $avgRating, 1) }}</div>
                <div class="mt-2 flex items-center gap-1">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $roundedAvg ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z"/>
                            </svg>
                        @endfor
                    </div>
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ (int) $ratingsCount }} مراجعة</div>
            </div>

            {{-- توزيع التقييمات 1..5 --}}
            <div class="flex-1 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-white/10 p-4">
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">توزيع التقييمات</div>

                @php
                    $dist  = $starsDist   ?? [];
                    $pct   = $starsPercent ?? [];
                @endphp

                <div class="space-y-2">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = (int) ($dist[$star] ?? 0);
                            $per   = (int) ($pct[$star]  ?? 0);
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-16 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $star }} نجوم
                            </div>
                            <div class="flex-1">
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-2 bg-yellow-400 rounded-full" style="width: {{ $per }}%"></div>
                                </div>
                            </div>
                            <div class="w-24 flex items-center justify-end gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <span class="tabular-nums">{{ $per }}%</span>
                                <span class="text-gray-400 dark:text-gray-500">({{ $count }})</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-4">
                <div class="flex items-center justify-between">
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $review->user->name }}</div>
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                 viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z"/>
                            </svg>
                        @endfor
                    </div>
                </div>

                @if($review->comment)
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-2 whitespace-pre-line">{{ $review->comment }}</p>
                @endif

                @auth
                    @if(auth()->id() === $review->user_id || auth()->user()->hasRole('Admin'))
                        <div class="mt-3">
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 dark:text-red-400 text-sm hover:underline">حذف</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد مراجعات بعد.</p>
        @endforelse
    </div>

    @if($reviews instanceof \Illuminate\Pagination\AbstractPaginator && $reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->onEachSide(1)->fragment('reviews')->links() }}
        </div>
    @endif

    @auth
        @if(auth()->user()->hasVerifiedEmail())
            @php
                $myReview  = \App\Models\Review::where('book_id', $book->id)->where('user_id', auth()->id())->first();
                $myRating  = old('rating', $myReview->rating ?? 5);
                $myComment = old('comment', $myReview->comment ?? '');
            @endphp

            <div class="mt-8">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $myReview ? 'حدّث تقييمك' : 'اكتب تقييمك' }}</h3>
                <form class="space-y-3" method="POST" action="{{ route('reviews.store', $book) }}">
                    @csrf

                    <div class="flex items-center gap-4">
                        <label class="text-sm text-gray-700 dark:text-gray-300">التقييم:</label>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <label>
                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only" {{ (int) $myRating === $i ? 'checked' : '' }}>
                                    <svg class="w-6 h-6 cursor-pointer {{ (int) $myRating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>
                        @error('rating') <div class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</div> @enderror
                    </div>

                    <textarea name="comment" rows="4" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" placeholder="اكتب رأيك (اختياري)">{{ $myComment }}</textarea>
                    @error('comment') <div class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</div> @enderror

                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                        {{ $myReview ? 'تحديث' : 'حفظ' }}
                    </button>
                </form>
            </div>
        @else
            <div class="mt-6 text-sm text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/20 rounded-xl px-3 py-2">
                يرجى تفعيل بريدك الإلكتروني قبل إضافة مراجعة.
            </div>
        @endif
    @else
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
            تحتاج لتسجيل الدخول لإضافة مراجعة.
        </div>
    @endauth
</section>
