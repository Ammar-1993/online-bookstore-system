@props(['duration' => 10000]) {{-- ملّي ثانية، افتراضي 10 ثواني --}}

@php
    // اسحب المكدّس الموحد لو كنت تستخدم session()->push('flash', [...])
    $stack = session()->pull('flash', []);

    // دعم المفاتيح المنفردة also:
    $singles = [];
    if (session()->has('success')) $singles[] = ['type' => 'success', 'message' => session()->pull('success')];
    if (session()->has('error'))   $singles[] = ['type' => 'error',   'message' => session()->pull('error')];
    if (session()->has('info'))    $singles[] = ['type' => 'info',    'message' => session()->pull('info')];

    $items = array_merge($stack, $singles);
@endphp

@if(count($items))
  <div id="flash-stack" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 space-y-2 w-[95%] sm:w-auto" data-duration="{{ (int) $duration }}">
    @foreach($items as $i => $item)
      @php
        $type = $item['type'] ?? 'info';
        $msg  = $item['message'] ?? (string) $item;
        $colors = match($type) {
            'success' => 'bg-green-100 text-green-900 border-green-300',
            'error'   => 'bg-red-100 text-red-900 border-red-300',
            default   => 'bg-blue-100 text-blue-900 border-blue-300',
        };
      @endphp
      <div class="flash-item border {{ $colors }} rounded px-4 py-2 shadow" role="alert">
        <div class="flex items-start gap-3">
          <strong class="capitalize">@lang($type === 'success' ? 'تم' : ($type === 'error' ? 'خطأ' : 'تنبيه'))</strong>
          <div class="flex-1">{{ $msg }}</div>
          <button type="button" class="close-btn text-sm opacity-60 hover:opacity-100">×</button>
        </div>
      </div>
    @endforeach
  </div>

  <script>
    (() => {
      const wrap = document.getElementById('flash-stack');
      if (!wrap) return;

      const dur = parseInt(wrap.dataset.duration || '10000', 10);

      const close = (el) => {
        el.style.transition = 'opacity .25s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 250);
      };

      wrap.querySelectorAll('.flash-item').forEach((item, idx) => {
        // إغلاق تلقائي بعد المدة المحددة
        setTimeout(() => close(item), dur + (idx * 150)); // فرق بسيط بين العناصر
        // إغلاق يدوي
        item.querySelector('.close-btn')?.addEventListener('click', () => close(item));
      });
    })();
  </script>
@endif
