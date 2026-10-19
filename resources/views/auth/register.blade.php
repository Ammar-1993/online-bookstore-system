<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
            <div class="mt-3 text-center">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">إنشاء حساب</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">أهلاً بك 👋</p>
            </div>
        </x-slot>

        {{-- أخطاء التحقق العامة --}}
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" dir="rtl" data-register novalidate>
            @csrf

            {{-- الاسم --}}
            <div>
                <x-label for="name" value="الاسم" class="mb-1 block text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="name"
                        class="block mt-1 w-full ps-12 pe-4 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="text" name="name" :value="old('name')" required autofocus
                        placeholder="الاسم الكامل"
                        aria-describedby="@error('name') name_error @enderror"
                        aria-invalid="@error('name')true@enderror" />
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                         style="inset-inline-start:0.75rem" aria-hidden="true">
                        {{-- user icon --}}
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                            <path d="M4 20a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                </div>
                @error('name')
                    <p id="name_error" class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- البريد الإلكتروني --}}
            <div class="mt-4">
                <x-label for="email" value="البريد الإلكتروني" class="mb-1 block text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="email"
                        class="block mt-1 w-full ps-12 pe-4 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="email" name="email" :value="old('email')" required
                        autocomplete="username" inputmode="email" autocapitalize="off" spellcheck="false"
                        dir="ltr" placeholder="name@domain.com"
                        aria-describedby="email_hint @error('email') email_error @enderror"
                        aria-invalid="@error('email')true@enderror" />
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                         style="inset-inline-start:0.75rem" aria-hidden="true">
                        {{-- mail icon --}}
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

            {{-- كلمة المرور --}}
            <div class="mt-4">
                <x-label for="password" value="كلمة المرور" class="mb-1 block text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="password"
                        class="block mt-1 w-full ps-12 pe-12 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="password" name="password" required autocomplete="new-password"
                        placeholder="••••••••"
                        aria-describedby="caps_hint @error('password') password_error @enderror"
                        aria-invalid="@error('password')true@enderror" />
                    {{-- أيقونة القفل في بداية الحقل --}}
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                         style="inset-inline-start:0.75rem" aria-hidden="true">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    {{-- زر إظهار/إخفاء كلمة المرور في نهاية الحقل --}}
                    <button type="button"
                        class="absolute top-1/2 -translate-y-1/2 w-9 h-9 grid place-items-center rounded-lg
                               text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none focus-visible:ring-2
                               focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                        style="inset-inline-end:0.5rem"
                        aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false"
                        data-toggle-password="#password">
                        <svg data-eye-on class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3" fill="currentColor"/>
                        </svg>
                        <svg data-eye-off class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12s3.5-7 10-7c2.1 0 3.9.6 5.4 1.5M22 12s-3.5 7-10 7c-2.1 0-3.9-.6-5.4-1.5"
                                  stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
                <p id="caps_hint" class="mt-1 text-xs text-amber-600 hidden">تنبيه: زر «Caps Lock» مُفعّل</p>
                @error('password')
                    <p id="password_error" class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- تأكيد كلمة المرور --}}
            <div class="mt-4">
                <x-label for="password_confirmation" value="تأكيد كلمة المرور" class="mb-1 block text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="password_confirmation"
                        class="block mt-1 w-full ps-12 pe-12 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="••••••••" />
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                         style="inset-inline-start:0.75rem" aria-hidden="true">
                        {{-- lock-check --}}
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6z" stroke="currentColor" stroke-width="2"/>
                            <path d="m9 15 2 2 4-4" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <button type="button"
                        class="absolute top-1/2 -translate-y-1/2 w-9 h-9 grid place-items-center rounded-lg
                               text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none focus-visible:ring-2
                               focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                        style="inset-inline-end:0.5rem"
                        aria-label="إظهار/إخفاء تأكيد كلمة المرور" aria-pressed="false"
                        data-toggle-password="#password_confirmation">
                        <svg data-eye-on class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3" fill="currentColor"/>
                        </svg>
                        <svg data-eye-off class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12s3.5-7 10-7c2.1 0 3.9.6 5.4 1.5M22 12s-3.5 7-10 7c-2.1 0-3.9-.6-5.4-1.5"
                                  stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <label for="terms" class="inline-flex items-start gap-2 text-sm">
                        <x-checkbox name="terms" id="terms" required />
                        <span class="text-gray-700 dark:text-gray-300">
                            أوافق على
                            <a target="_blank" href="{{ route('terms.show') }}"
                               class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                شروط الخدمة
                            </a>
                            و
                            <a target="_blank" href="{{ route('policy.show') }}"
                               class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                سياسة الخصوصية
                            </a>
                        </span>
                    </label>
                </div>
            @endif

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   href="{{ route('login') }}">
                    لديك حساب بالفعل؟
                </a>

                <x-button type="submit" data-submit
                    class="w-full sm:w-auto ms-4 inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 min-w-[10rem]
                           bg-indigo-600 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 disabled:bg-indigo-600/80 disabled:cursor-not-allowed
                           text-white dark:text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
                    <svg class="w-5 h-5 hidden motion-safe:inline" viewBox="0 0 24 24" fill="none" data-spinner aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" opacity=".25" />
                        <path d="M21 12a9 9 0 0 1-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span>تسجيل</span>
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    {{-- سكربتات صغيرة: إظهار/إخفاء كلمة المرور + تنبيه CapsLock + تعطيل زر الإرسال --}}
    <script>
        (() => {
            // تبديل إظهار/إخفاء كلمة المرور (لعدّة أزرار)
            document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = document.querySelector(btn.getAttribute('data-toggle-password'));
                    if (!input) return;

                    const isPwd = input.type === 'password';
                    input.type = isPwd ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', isPwd ? 'true' : 'false');

                    const on  = btn.querySelector('[data-eye-on]');
                    const off = btn.querySelector('[data-eye-off]');
                    if (on && off) {
                        on.classList.toggle('hidden', !isPwd);
                        off.classList.toggle('hidden', isPwd);
                    }
                    input.focus();
                }, { passive: true });
            });

            // تنبيه Caps Lock لحقل كلمة المرور
            const pwd = document.getElementById('password');
            const caps = document.getElementById('caps_hint');
            if (pwd && caps) {
                const updateCaps = (e) => caps.classList.toggle('hidden', !(e.getModifierState && e.getModifierState('CapsLock')));
                pwd.addEventListener('keydown', updateCaps, { passive: true });
                pwd.addEventListener('keyup', updateCaps, { passive: true });
                pwd.addEventListener('blur', () => caps.classList.add('hidden'), { passive: true });
            }

            // تعطيل زر الإرسال وإظهار مؤشّر دوران
            const form = document.querySelector('form[data-register]');
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
