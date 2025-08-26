{{-- resources/views/components/page-loader.blade.php --}}
<div id="app-page-loader"
     class="hidden fixed inset-0 z-[9999] flex items-center justify-center"
     role="status"
     aria-live="polite"
     aria-label="جارٍ التحميل"
     tabindex="-1"
     data-mode="indeterminate"
     data-respect-reduced-motion="false"> {{-- اجعلها true لو تريد احترام تقليل الحركة --}}
    <!-- خلفية مع ضبابية خفيفة -->
    <div class="absolute inset-0 bg-white/70 backdrop-blur-sm dark:bg-black/40"></div>

    <!-- دائرة التحميل الكبيرة -->
    <div class="relative flex flex-col items-center gap-4">
        <!-- توهج جمالي خافت -->
        <div class="absolute -inset-8 pointer-events-none blur-2xl opacity-50 dark:opacity-40"
             aria-hidden="true"
             style="background: radial-gradient(60% 60% at 50% 50%, rgba(59,130,246,.25), transparent 60%);">
        </div>

        <!-- الحلقة (SVG) -->
        <svg class="loader-ring text-blue-600 dark:text-blue-400"
             viewBox="0 0 100 100"
             aria-hidden="true">
            <!-- المسار الخلفي -->
            <circle class="track" cx="50" cy="50" r="42" fill="none" stroke-width="10" />
            <!-- المسار المتحرّك -->
            <circle class="spinner" cx="50" cy="50" r="42" fill="none" stroke-width="10"
                    stroke-linecap="round" />
        </svg>

        <!-- النص -->
        <div id="app-page-loader-label" class="text-sm text-gray-700 dark:text-gray-200 select-none">
            جارٍ التحميل…
        </div>
    </div>
</div>

<style>
/* ============= ضبط عام ============= */
#app-page-loader { --loader-size: clamp(128px, 22vmin, 240px); }
#app-page-loader .loader-ring {
  width: var(--loader-size); height: var(--loader-size);
  display:block;
  transform-origin: center;
  transform-box: fill-box;        /* مهم لدعم تدوير الـ SVG */
  will-change: transform;
}

/* لون المسار الخلفي */
#app-page-loader .track {
  stroke: rgba(99,102,241,.20);   /* fallback */
  stroke: color-mix(in oklab, currentColor 18%, transparent);
}

/* النمط الافتراضي: غير محدد (indeterminate) */
#app-page-loader[data-mode="indeterminate"] .loader-ring {
  animation: loader-rotate 1.15s linear infinite;
}
#app-page-loader .spinner {
  stroke: currentColor;
  /* محيط r=42 ≈ 263.89 */
  stroke-dasharray: 80 200;
  stroke-dashoffset: 0;
  animation: loader-dash 1.4s ease-in-out infinite;
  will-change: stroke-dasharray, stroke-dashoffset;
}

/* النمط المحدّد (determinate) عند ضبط progress عبر JS */
#app-page-loader[data-mode="determinate"] .loader-ring { animation: none; }
#app-page-loader[data-mode="determinate"] .spinner {
  animation: none;
  --circumference: 263.89;
  stroke-dasharray: calc(var(--circumference) * var(--progress, 0))
                    calc(var(--circumference) * (1 - var(--progress, 0)));
  stroke-dashoffset: 0;
}

/* Ripple (كما في نسختك) */
[data-ripple]{position:relative;overflow:hidden}
.ripple{
  position:absolute;border-radius:9999px;transform:scale(0);opacity:.25;
  pointer-events:none;background:currentColor;animation:ripple-anim 600ms linear;
}

/* حركات */
@keyframes loader-rotate { to { transform: rotate(360deg) } }
@keyframes loader-dash {
  0%   { stroke-dasharray: 1 300;   stroke-dashoffset: 0; }
  50%  { stroke-dasharray: 180 120; stroke-dashoffset: -70; }
  100% { stroke-dasharray: 1 300;   stroke-dashoffset: -250; }
}
@keyframes ripple-anim{to{transform:scale(4);opacity:0}}

/* احترام تقليل الحركة (اختياري) */
@media (prefers-reduced-motion: reduce) {
  #app-page-loader[data-respect-reduced-motion="true"] .loader-ring,
  #app-page-loader[data-respect-reduced-motion="true"] .spinner {
    animation-duration: 0ms !important;
  }
}

/* ============= دعم اللى موجود سابقاً (fallback) ============= */
#app-page-loader .lds-spinner{
  width:64px;height:64px;border-radius:9999px;
  border:4px solid rgba(59,130,246,.25);
  border-top-color:rgba(59,130,246,1);
  animation:lds-spin .8s linear infinite;
  box-sizing:border-box;background:transparent;position:relative;z-index:1;
}
@keyframes lds-spin{to{transform:rotate(360deg)}}
</style>
