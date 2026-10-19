<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex flex-col items-center gap-2">
                <x-authentication-card-logo />
                <div class="text-center">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">تسجيل الدخول</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">مرحبًا بك مجددًا 👋</p>
                </div>
            </div>
        </x-slot>

        {{-- أخطاء التحقق العامة --}}
        <x-validation-errors class="mb-4" />

        {{-- رسائل الحالة --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-400" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" data-login novalidate>
            @csrf

            {{-- البريد الإلكتروني --}}
            <div>
                <x-label for="email" value="البريد الإلكتروني" class="mb-1 text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="email"   class="block mt-1 w-full ps-12 pe-4 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="email" name="email" :value="old('email')" required
                        autocomplete="username" inputmode="email" autocapitalize="off" spellcheck="false"
                        dir="ltr" placeholder="name@domain.com"
                        aria-describedby="email_hint @error('email') email_error @enderror"
                        aria-invalid="@error('email')true@enderror" />

                    {{-- أيقونة البريد: ملاصقة لبداية الحقل مع مسافة داخلية ps-12 --}}
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                        style="inset-inline-start:0.75rem" aria-hidden="true">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="2" />
                            <path d="m22 6-10 7L2 6" stroke="currentColor" stroke-width="2" fill="none" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- كلمة المرور --}}
            <div class="mt-4">
                <x-label for="password" value="كلمة المرور" class="mb-1 text-gray-700 dark:text-gray-200 text-right" />
                <div class="relative">
                    <x-input id="password" class="block mt-1 w-full ps-12 pe-12 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                               ring-1 ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        type="password" name="password" required autocomplete="new-password"
                        placeholder="••••••••"
                        aria-describedby="caps_hint @error('password') password_error @enderror"
                        aria-invalid="@error('password')true@enderror" />

                    {{-- أيقونة ثابتة لبداية الحقل (قفل) + مسافة داخلية ps-12 --}}
                    <div class="pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                        style="inset-inline-start:0.75rem" aria-hidden="true">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>

                    {{-- زر إظهار/إخفاء عند نهاية الحقل + حشوة pe-12 --}}
                    <button type="button" class="absolute top-1/2 -translate-y-1/2 my-auto w-9 h-9 grid place-items-center rounded-lg
                   text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200
                   focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                        style="inset-inline-end:0.5rem" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false"
                        data-toggle-password="#password">
                        {{-- on --}}
                        <svg data-eye-on class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor"
                                stroke-width="2" />
                            <circle cx="12" cy="12" r="3" fill="currentColor" />
                        </svg>
                        {{-- off --}}
                        <svg data-eye-off class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" />
                            <path d="M2 12s3.5-7 10-7c2.1 0 3.9.6 5.4 1.5M22 12s-3.5 7-10 7c-2.1 0-3.9-.6-5.4-1.5"
                                stroke="currentColor" stroke-width="2" />
                            <circle cx="12" cy="12" r="3" fill="currentColor" />
                        </svg>
                    </button>
                </div>
            </div>


            {{-- تذكّرني + الروابط --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mt-5">
                <label class="inline-flex items-center gap-2 text-sm select-none">
                    <input id="remember" name="remember" type="checkbox"
                        class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-gray-700 dark:text-gray-300">تذكّرني</span>
                </label>

                <div class="text-sm">
                    @if (Route::has('password.request'))
                        <a class="underline underline-offset-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            href="{{ route('password.request') }}">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <span class="mx-2 text-gray-400">•</span>
                        <a class="underline underline-offset-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            href="{{ route('register') }}">
                            إنشاء حساب جديد
                        </a>
                    @endif
                </div>
            </div>

            {{-- زر الإرسال --}}
            <div class="mt-6">
                <x-button type="submit" data-submit
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 min-w-[10rem]
                           bg-indigo-600 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 disabled:bg-indigo-600/80 disabled:cursor-not-allowed
                           text-white dark:text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                    data-ripple>
                    <svg class="w-5 h-5 hidden motion-safe:inline" viewBox="0 0 24 24" fill="none" data-spinner
                        aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" opacity=".25" />
                        <path d="M21 12a9 9 0 0 1-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span>تسجيل الدخول</span>
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    {{-- سكربتات تحسين التجربة: إظهار/إخفاء كلمة المرور + تنبيه CapsLock + منع النقرات المتعددة --}}
    <script>
        (() => {
            // إظهار/إخفاء كلمة المرور مع تبديل الأيقونات
            document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = document.querySelector(btn.getAttribute('data-toggle-password'));
                    if (!input) return;

                    const isPwd = input.type === 'password';
                    input.type = isPwd ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', isPwd ? 'true' : 'false');

                    const eyeOn = btn.querySelector('[data-eye-on]');
                    const eyeOff = btn.querySelector('[data-eye-off]');
                    if (eyeOn && eyeOff) {
                        eyeOn.classList.toggle('hidden', !isPwd);
                        eyeOff.classList.toggle('hidden', isPwd);
                    }
                    input.focus();
                }, { passive: true });
            });

            // تنبيه Caps Lock عند الكتابة في كلمة المرور
            const pwdInput = document.getElementById('password');
            const capsHint = document.getElementById('caps_hint');
            if (pwdInput && capsHint) {
                const updateCaps = (e) => {
                    const on = e.getModifierState && e.getModifierState('CapsLock');
                    capsHint.classList.toggle('hidden', !on);
                };
                pwdInput.addEventListener('keydown', updateCaps, { passive: true });
                pwdInput.addEventListener('keyup', updateCaps, { passive: true });
                pwdInput.addEventListener('blur', () => capsHint.classList.add('hidden'), { passive: true });
            }

            // تعطيل زر الإرسال وإظهار مؤشّر دوران (منع إرسال متعدد)
            const form = document.querySelector('form[data-login]');
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