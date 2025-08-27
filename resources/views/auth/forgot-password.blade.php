<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
            <div class="mt-3 text-center">
                <h1 class="text-xl font-semibold text-gray-900">استعادة كلمة المرور</h1>
                <p class="text-sm text-gray-500 mt-1">أدخل بريدك الإلكتروني لإرسال رابط إعادة التعيين</p>
            </div>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 text-right">
            نسيت كلمة المرور؟ لا مشكلة. فقط أخبرنا بعنوان بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة المرور.
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-emerald-600" role="status">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" dir="rtl" data-forgot novalidate>
            @csrf

            {{-- البريد الإلكتروني --}}
            <div class="block">
                <x-label for="email" value="البريد الإلكتروني" class="mb-1 block text-black text-right" />
                <div class="relative">
                    <x-input id="email"
                        class="block mt-1 w-full ps-12 pe-4 rounded-xl bg-white text-gray-900 placeholder-gray-400
                               ring-1 ring-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="email" name="email" :value="old('email')" required autofocus
                        autocomplete="username" inputmode="email" autocapitalize="off" spellcheck="false"
                        dir="ltr" placeholder="name@domain.com"
                        aria-describedby="email_hint @error('email') email_error @enderror"
                        aria-invalid="@error('email')true@enderror" />

                    {{-- أيقونة البريد في بداية الحقل (تدعم RTL/LTR) --}}
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400"
                         style="inset-inline-start:0.75rem" aria-hidden="true">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="2" />
                            <path d="m22 6-10 7L2 6" stroke="currentColor" stroke-width="2" fill="none" />
                        </svg>
                    </div>
                </div>
                @error('email')
                    <p id="email_error" class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- الإجراءات --}}
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('login'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       href="{{ route('login') }}">
                        الرجوع لتسجيل الدخول
                    </a>
                @endif

                <x-button type="submit" data-submit
                    class="w-full sm:w-auto ms-4 inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 min-w-[12rem]
                           bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-600/80 disabled:cursor-not-allowed
                           text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
                    <svg class="w-5 h-5 hidden motion-safe:inline" viewBox="0 0 24 24" fill="none" data-spinner aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" opacity=".25" />
                        <path d="M21 12a9 9 0 0 1-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span>إرسال رابط إعادة التعيين</span>
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    {{-- سكربت بسيط لتعطيل الزر وإظهار المؤشر أثناء الإرسال --}}
    <script>
        (() => {
            const form = document.querySelector('form[data-forgot]');
            if (form) {
                form.addEventListener('submit', () => {
                    const btn = form.querySelector('[data-submit]');
                    if (!btn) return;
                    btn.disabled = true;
                    btn.setAttribute('aria-busy', 'true');
                    const spinner = btn.querySelector('[data-spinner]');
                    if (spinner) spinner.classList.remove('hidden');
                }, { passive: true });
            }
        })();
    </script>
</x-guest-layout>
