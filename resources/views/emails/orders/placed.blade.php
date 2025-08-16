<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"></head>
<body>
  <h2>شكرًا لطلبك!</h2>
  <p>رقم الطلب: <strong>#{{ sprintf('%06d', $order->id) }}</strong></p>
  <p>الحالة: {{ $order->status }} — حالة الدفع: {{ $order->payment_status }}</p>
  <p>الإجمالي: {{ number_format($order->total_amount,2) }} {{ $order->currency }}</p>

  <h3>العناصر</h3>
  <ul>
    @foreach($order->items as $it)
      <li>
        كتاب #{{ $it->book_id }} —
        {{ $it->qty }} × {{ number_format($it->unit_price,2) }}
        = {{ number_format($it->total_price,2) }} {{ $order->currency }}
      </li>
    @endforeach
  </ul>

  @php $ship = $order->shipping_address; @endphp
  @if(is_array($ship))
    <h3>الشحن</h3>
    <p>
      {{ $ship['name'] ?? '' }} — {{ $ship['phone'] ?? '' }}<br>
      {{ $ship['address'] ?? '' }}، {{ $ship['city'] ?? '' }}، {{ $ship['country'] ?? '' }}<br>
      {{ $ship['email'] ?? '' }}
    </p>
    @if(!empty($ship['notes'])) <p>ملاحظات: {{ $ship['notes'] }}</p> @endif
  @endif

  <p>سعداء بخدمتك!</p>
</body>
</html>
