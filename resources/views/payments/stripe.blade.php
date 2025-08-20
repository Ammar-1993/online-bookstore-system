<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>الدفع عبر Stripe</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">
      الدفع للطلب {{ method_exists($order,'getNumberAttribute') ? $order->number : ('#'.$order->id) }}
    </h1>

    <div class="bg-white p-4 rounded-2xl shadow mb-4">
      <div class="text-sm text-gray-600">الإجمالي</div>
      <div class="text-xl font-bold">
        {{ number_format(method_exists($order,'computedTotal') ? $order->computedTotal() : $order->items->sum('total_price'), 2) }}
        {{ $order->currency ?: config('app.currency','USD') }}
      </div>
    </div>

    <form id="payment-form" class="bg-white p-4 rounded-2xl shadow space-y-3">
      <div id="card-element" class="p-3 border rounded-xl"></div>
      <button id="pay-btn" type="submit"
              class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition disabled:opacity-60">
        ادفع الآن
      </button>
      <div id="msg" class="text-sm mt-2"></div>
    </form>

    <div class="mt-4">
      <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">العودة للطلب</a>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const stripe = Stripe(@json($publishableKey));
    const elements = stripe.elements();
    const card = elements.create('card', { hidePostalCode: true });
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const btn  = document.getElementById('pay-btn');
    const msg  = document.getElementById('msg');

    function showMsg(text, ok = false){
      msg.textContent = text || '';
      msg.className   = 'text-sm mt-2 ' + (ok ? 'text-emerald-700' : 'text-red-600');
    }
    function setLoading(v){
      btn.disabled = v;
      btn.textContent = v ? 'جارِ المعالجة…' : 'ادفع الآن';
    }

    const intentUrl = `/payments/stripe/{{ $order->id }}/intent`;
    const statusUrl = `/orders/{{ $order->id }}/status`;

    async function createIntent(){
      try{
        const res = await fetch(intentUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': @json(csrf_token()),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({})
        });

        if(!res.ok){
          const txt = await res.text();
          if(res.status === 419) throw new Error('انتهت الجلسة (CSRF). حدّث الصفحة ثم جرّب مرة أخرى.');
          try { const j = JSON.parse(txt); throw new Error(j.message || 'تعذر إنشاء عملية الدفع.'); }
          catch { throw new Error('تعذر إنشاء عملية الدفع.'); }
        }

        const data = await res.json();
        if(!data.clientSecret) throw new Error('لم نستلم clientSecret من الخادم.');
        return data.clientSecret;

      } catch(err){
        showMsg(err.message || 'خطأ غير متوقع عند تهيئة الدفع.');
        return null;
      }
    }

    async function waitUntilPaid(maxMs = 20000, stepMs = 1500){
      const started = Date.now();
      while (Date.now() - started < maxMs) {
        try {
          const r = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
          if (r.ok) {
            const j = await r.json();
            if (j.paid) return true;
          }
        } catch (_) {}
        await new Promise(res => setTimeout(res, stepMs));
      }
      return false;
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      showMsg('');
      setLoading(true);

      const clientSecret = await createIntent();
      if(!clientSecret){ setLoading(false); return; }

      const { error } = await stripe.confirmCardPayment(clientSecret, { payment_method: { card } });

      if(error){
        showMsg(error.message || 'فشل الدفع.');
        setLoading(false);
        return;
      }

      // تم تأكيد البطاقة بنجاح — انتظر حتى يصل الويب هوك ويُحدّث الطلب ثم وجّه
      showMsg('تم الدفع بنجاح. جارِ تأكيد الطلب…', true);

      const ok = await waitUntilPaid();
      window.location.href = @json(route('orders.show', $order)) + (ok ? '' : '?refresh=1');
    });
  });
  </script>
</body>
</html>
