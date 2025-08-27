{{-- resources/views/components/flash-stack.blade.php --}}
@props([
  'duration' => 5000,  // ملّي ثانية: مدة الظهور الافتراضية
  'max'      => 8,      // أقصى عدد عناصر معروضة في آنٍ واحد
])

@php
    // اسحب المكدّس الموحد لو كنت تستخدم session()->push('flash', [...])
    $stack = session()->pull('flash', []);

    // دعم المفاتيح المنفردة أيضاً:
    $singles = [];
    if (session()->has('success')) $singles[] = ['type' => 'success', 'message' => session()->pull('success')];
    if (session()->has('error'))   $singles[] = ['type' => 'error',   'message' => session()->pull('error')];
    if (session()->has('warning')) $singles[] = ['type' => 'warning', 'message' => session()->pull('warning')];
    if (session()->has('info'))    $singles[] = ['type' => 'info',    'message' => session()->pull('info')];
    if (session()->has('status'))  $singles[] = ['type' => 'success', 'message' => session()->pull('status')]; // نمط لارافيل الافتراضي

    $items = array_merge($stack, $singles);
@endphp

<div id="flash-stack"
     class="fixed inset-x-0 top-4 z-[9998] flex flex-col items-center space-y-2 px-3 sm:px-0 pointer-events-none"
     role="region" aria-label="تنبيهات" data-duration="{{ (int)$duration }}" data-max="{{ (int)$max }}">

  {{-- العناصر المولّدة من الـ session --}}
  @foreach($items as $i => $item)
    @php
      $type = $item['type'] ?? 'info';
      $msg  = $item['message'] ?? (string) $item;

      // ألوان / أيقونات حسب النوع
      $palette = match($type) {
          'success' => ['bg' => 'bg-emerald-600', 'ring' => 'ring-emerald-600/20', 'text' => 'text-emerald-700 dark:text-emerald-300', 'icon' => 'check'],
          'error'   => ['bg' => 'bg-rose-600',    'ring' => 'ring-rose-600/20',    'text' => 'text-rose-700 dark:text-rose-300',       'icon' => 'x'],
          'warning' => ['bg' => 'bg-amber-500',   'ring' => 'ring-amber-500/20',   'text' => 'text-amber-700 dark:text-amber-200',     'icon' => 'warn'],
          default   => ['bg' => 'bg-blue-600',    'ring' => 'ring-blue-600/20',    'text' => 'text-blue-700 dark:text-blue-300',       'icon' => 'info'],
      };
    @endphp

    <div class="flash-item pointer-events-auto relative w-full sm:w-auto max-w-[92vw] sm:max-w-md rounded-2xl border border-black/5 shadow-lg ring-1 {{ $palette['ring'] }} bg-white/95 dark:bg-zinc-900/95 backdrop-blur supports-[backdrop-filter]:bg-white/75"
         data-type="{{ $type }}" data-timeout="{{ (int)$duration }}" data-server-rendered="1" role="status" aria-live="polite">
      <div class="absolute left-0 top-0 h-full w-1.5 rounded-l-2xl {{ $palette['bg'] }}"></div>

      <div class="flex items-start gap-3 p-4 pr-10 {{ $palette['text'] }}">
        {{-- أيقونة --}}
        <div class="shrink-0 mt-0.5" aria-hidden="true">
          @if($palette['icon'] === 'check')
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M20 7L10 17l-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          @elseif($palette['icon'] === 'x')
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          @elseif($palette['icon'] === 'warn')
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          @else
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8h.01M11 12h2v6h-2z" fill="currentColor"/></svg>
          @endif
        </div>

        <div class="flex-1 leading-relaxed">
          {!! is_string($msg) ? e($msg) : e(json_encode($msg, JSON_UNESCAPED_UNICODE)) !!}
        </div>

        {{-- زر الإغلاق --}}
        <button type="button" class="close-btn absolute top-2.5 right-2.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500"
                aria-label="إغلاق">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>

      {{-- شريط التقدّم --}}
      <div class="progress absolute bottom-0 left-0 h-1 w-full opacity-60 {{ $palette['bg'] }}"></div>
    </div>
  @endforeach
</div>

<style>
/* ------- الأساسيات والأنيميشن ------- */
#flash-stack .flash-item{
  /* دخول */
  transform: translateY(-8px) scale(.98);
  opacity: 0;
  animation: flash-in .28s cubic-bezier(.22,1,.36,1) forwards;
  /* ظل خفيف لطيف */
  box-shadow: 0 10px 25px rgba(0,0,0,.08);
}
@keyframes flash-in{
  to{ transform: translateY(0) scale(1); opacity: 1; }
}
#flash-stack .flash-item .progress{
  transform-origin: left center;
  animation: flash-progress var(--dur, 10s) linear forwards;
}
@keyframes flash-progress{
  from{ transform: scaleX(1); }
  to  { transform: scaleX(0); }
}

/* إيقاف عند المرور */
#flash-stack .flash-item:hover .progress{ animation-play-state: paused; }

/* وضع تقليل الحركة */
@media (prefers-reduced-motion: reduce){
  #flash-stack .flash-item { animation-duration: 0ms !important; }
  #flash-stack .flash-item .progress{ animation-duration: 0ms !important; }
}

/* دعم RTL افتراضياً (العربية)، لا حاجة لتغيير كبير لأن التخطيط حيادي الاتجاه */
</style>

<script>
(() => {
  // امنع تكرار التهيئة عند تضمين الكمبوننت أكثر من مرة (Livewire/partials)
  if (window.__flashStackInit) return; window.__flashStackInit = true;

  const stackEl = document.getElementById('flash-stack');
  if (!stackEl) return;

  const DEFAULT_DURATION = parseInt(stackEl.dataset.duration || '5000', 10);
  const MAX_COUNT = parseInt(stackEl.dataset.max || '8', 10);

  // أدوات
  const now = () => (new Date()).getTime();

  const closeAnimated = (item, removeDelay = 220) => {
    if (!item || item.__closing) return;
    item.__closing = true;
    item.style.transition = 'transform .22s ease, opacity .22s ease';
    item.style.transform = 'translateY(-6px) scale(.98)';
    item.style.opacity = '0';
    setTimeout(() => item.remove(), removeDelay);
  };

  // إدارة المؤقّت مع توقّف عند hover
  const armAutoClose = (item, timeout) => {
    let start = now(), remaining = timeout, timerId = null;

    const startTimer = () => {
      item.style.setProperty('--dur', remaining + 'ms');
      item.__timerId = setTimeout(() => closeAnimated(item), remaining);
    };
    const clearTimer = () => { if (item.__timerId) clearTimeout(item.__timerId); item.__timerId = null; };

    item.addEventListener('mouseenter', () => {
      if (item.__timerId) {
        clearTimer();
        remaining = remaining - (now() - start);
        // أوقف شريط التقدم
        const bar = item.querySelector('.progress');
        if (bar) bar.style.animationPlayState = 'paused';
      }
    });
    item.addEventListener('mouseleave', () => {
      if (!item.__timerId) {
        start = now();
        const bar = item.querySelector('.progress');
        if (bar) {
          // أعِد تشغيل الأنيميشن من الحالة الحالية عبر إعادة تعيينها
          bar.style.animation = 'none'; void bar.offsetWidth; // reflow
          bar.style.animation = `flash-progress ${remaining}ms linear forwards`;
        }
        startTimer();
      }
    });

    startTimer();
  };

  // سحب للإغلاق (drag-to-dismiss)
  const attachDragToDismiss = (item) => {
    let active = false, startX = 0, currentX = 0;

    const onPointerDown = (e) => {
      active = true;
      startX = e.clientX || (e.touches && e.touches[0]?.clientX) || 0;
      item.setPointerCapture?.(e.pointerId || 1);
      item.style.transition = 'none';
    };
    const onPointerMove = (e) => {
      if (!active) return;
      currentX = e.clientX || (e.touches && e.touches[0]?.clientX) || 0;
      const dx = (currentX - startX);
      item.style.transform = `translateX(${dx}px)`;
      item.style.opacity = String(Math.max(0.2, 1 - Math.abs(dx)/220));
    };
    const onPointerUp = () => {
      if (!active) return;
      active = false;
      const dx = (currentX - startX);
      if (Math.abs(dx) > 120) {
        closeAnimated(item, 120);
      } else {
        item.style.transition = 'transform .18s ease, opacity .18s ease';
        item.style.transform = 'translateX(0)'; item.style.opacity = '1';
      }
    };

    item.addEventListener('pointerdown', onPointerDown);
    item.addEventListener('pointermove', onPointerMove);
    item.addEventListener('pointerup', onPointerUp);
    item.addEventListener('pointercancel', onPointerUp);
    item.addEventListener('touchstart', onPointerDown, {passive: true});
    item.addEventListener('touchmove', onPointerMove, {passive: true});
    item.addEventListener('touchend', onPointerUp);
  };

  // إغلاق بزر × أو بالكيبورد (Escape يغلق أحدث عنصر)
  const wireCloseButtons = (item) => {
    item.querySelector('.close-btn')?.addEventListener('click', () => closeAnimated(item));
  };
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const items = [...stackEl.querySelectorAll('.flash-item')];
      const last = items.at(-1);
      if (last) closeAnimated(last);
    }
  });

  // تكديس لطيف: حد أقصى وعدّ العناصر
  const enforceMax = () => {
    const items = stackEl.querySelectorAll('.flash-item');
    const extra = items.length - MAX_COUNT;
    if (extra > 0) {
      [...items].slice(0, extra).forEach((el) => closeAnimated(el, 100));
    }
  };

  // تهيئة العناصر المولّدة من السيرفر
  const bootExisting = () => {
    const svr = stackEl.querySelectorAll('.flash-item[data-server-rendered="1"]');
    svr.forEach((item, idx) => {
      // لكل عنصر: شريط التقدّم ومدته (مع فرق بسيط بين العناصر)
      const timeout = parseInt(item.dataset.timeout || DEFAULT_DURATION, 10) + (idx * 150);
      const bar = item.querySelector('.progress');
      if (bar) bar.style.setProperty('--dur', timeout + 'ms');

      armAutoClose(item, timeout);
      attachDragToDismiss(item);
      wireCloseButtons(item);
    });
    enforceMax();
  };

  // واجهة برمجية عامة للإضافة أثناء التشغيل:
  // window.flashAdd({ message, type='info', duration })
  // أو: window.dispatchEvent(new CustomEvent('flash:add', { detail: { message, type, duration } }));
  const iconSvg = (type) => {
    if (type === 'success') return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M20 7L10 17l-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if (type === 'error')   return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if (type === 'warning') return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8h.01M11 12h2v6h-2z" fill="currentColor"/></svg>';
  };
  const tone = (type) => {
    switch(type){
      case 'success': return { bar:'bg-emerald-600', ring:'ring-emerald-600/20', text:'text-emerald-700 dark:text-emerald-300' };
      case 'error'  : return { bar:'bg-rose-600',    ring:'ring-rose-600/20',    text:'text-rose-700 dark:text-rose-300' };
      case 'warning': return { bar:'bg-amber-500',   ring:'ring-amber-500/20',   text:'text-amber-700 dark:text-amber-200' };
      default       : return { bar:'bg-blue-600',    ring:'ring-blue-600/20',    text:'text-blue-700 dark:text-blue-300' };
    }
  };

  const addFlash = ({ message, type = 'info', duration = DEFAULT_DURATION } = {}) => {
    if (!message) return;
    const t = tone(type);
    const wrap = document.createElement('div');
    wrap.className = `flash-item pointer-events-auto relative w-full sm:w-auto max-w-[92vw] sm:max-w-md rounded-2xl border border-black/5 shadow-lg ring-1 ${t.ring} bg-white/95 dark:bg-zinc-900/95 backdrop-blur`;
    wrap.setAttribute('data-type', type);
    wrap.setAttribute('role', 'status');
    wrap.style.setProperty('--dur', duration + 'ms');

    wrap.innerHTML = `
      <div class="absolute left-0 top-0 h-full w-1.5 rounded-l-2xl ${t.bar}"></div>
      <div class="flex items-start gap-3 p-4 pr-10 ${t.text}">
        <div class="shrink-0 mt-0.5" aria-hidden="true">${iconSvg(type)}</div>
        <div class="flex-1 leading-relaxed"></div>
        <button type="button" class="close-btn absolute top-2.5 right-2.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500" aria-label="إغلاق">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="progress absolute bottom-0 left-0 h-1 w-full opacity-60 ${t.bar}"></div>
    `;
    wrap.querySelector('.flex-1').textContent = String(message);

    // إدراج وتفعيل
    stackEl.appendChild(wrap);
    // تفعيل دخول
    requestAnimationFrame(() => {
      wrap.style.animation = 'flash-in .28s cubic-bezier(.22,1,.36,1) forwards';
    });

    armAutoClose(wrap, duration);
    attachDragToDismiss(wrap);
    wireCloseButtons(wrap);
    enforceMax();
    return wrap;
  };

  // أحداث عامة
  window.flashAdd = addFlash;
  window.addEventListener('flash:add', (e) => addFlash(e.detail || {}));

  // تكامل مع Livewire (اختياري): Livewire.emit('flash', {type:'success',message:'تم الحفظ',duration:5000})
  document.addEventListener('livewire:load', () => {
    try {
      if (window.Livewire?.on) {
        window.Livewire.on('flash', (payload) => addFlash(payload || {}));
      }
    } catch(_) {}
  });

  // إغلاق بالضغط على الخلفية؟ (متروك مُعطّل لأن الستانك في أعلى الصفحة وقد يسبب تداخل)
  // stackEl.addEventListener('click', (e) => { if (e.target === stackEl) ... });

  // فعّل العناصر القادمة من السيرفر
  bootExisting();
})();
</script>
