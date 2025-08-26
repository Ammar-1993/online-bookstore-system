@extends('admin.layouts.app')

@section('title', 'الطلبات')

@section('content')
<div class="p-4">
  <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-gray-100 mb-4">الطلبات</h1>

  {{-- نموذج الفلاتر --}}
  @php
    $sort = $sort ?? request('sort','created_at');
    $dir  = $dir  ?? request('dir','desc');

    // مولّد رابط الفرز مع تبديل الاتجاه والحفاظ على الاستعلام
    $sortLink = function (string $field, string $label) use ($sort, $dir) {
        $is   = $sort === $field;
        $next = $is && $dir === 'asc' ? 'desc' : 'asc';
        $url  = request()->fullUrlWithQuery(['sort' => $field, 'dir' => $next, 'page' => null]);
        $arrow = $is
          ? ($dir === 'asc'
              ? '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M8 15l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
              : '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M16 9l-4 4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>')
          : '';
        return '<a href="'.$url.'" class="inline-flex items-center hover:underline">'.$label.$arrow.'</a>';
    };

    $filters = $filters ?? [];
  @endphp

  <form method="GET" action="{{ route('admin.orders.index') }}"
        class="bg-white dark:bg-gray-900 shadow rounded-2xl p-4 space-y-3 mb-4 ring-1 ring-black/5 dark:ring-white/10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      {{-- حالة الطلب --}}
      <div>
        <label class="block text-sm mb-1">حالة الطلب</label>
        <select name="status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
          <option value="">الكل</option>
          @foreach(['pending'=>'pending','processing'=>'processing','shipped'=>'shipped','cancelled'=>'cancelled'] as $val => $label)
            <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- حالة الدفع --}}
      <div>
        <label class="block text-sm mb-1">حالة الدفع</label>
        <select name="payment_status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
          <option value="">الكل</option>
          @foreach(['unpaid'=>'unpaid','paid'=>'paid','refunded'=>'refunded'] as $val => $label)
            <option value="{{ $val }}" @selected(($filters['payment_status'] ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- بريد العميل --}}
      <div>
        <label class="block text-sm mb-1">بريد العميل</label>
        <input type="text" name="email" value="{{ $filters['email'] ?? '' }}"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900"
               placeholder="example@email.com">
      </div>

      {{-- من تاريخ --}}
      <div>
        <label class="block text-sm mb-1">من تاريخ</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
      </div>

      {{-- إلى تاريخ --}}
      <div>
        <label class="block text-sm mb-1">إلى تاريخ</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
      </div>
    </div>

    {{-- الحفاظ على الفرز أثناء تطبيق الفلاتر --}}
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir"  value="{{ $dir }}">

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700" data-ripple>تطبيق الفلاتر</button>
      <a href="{{ route('admin.orders.index', ['sort'=>$sort,'dir'=>$dir]) }}"
         class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700" >
        إعادة الضبط
      </a>
    </div>
  </form>

  {{-- الشارات (Badges) للفلاتر المفعّلة --}}
  @php $qs = request()->query(); @endphp
  @if(($filters['status'] ?? null) || ($filters['payment_status'] ?? null) || ($filters['email'] ?? null) || ($filters['from'] ?? null) || ($filters['to'] ?? null))
    <div class="mb-4 flex flex-wrap items-center gap-2">
      <span class="text-sm text-gray-600 dark:text-gray-300">فلاتر مفعّلة:</span>

      @if(!empty($filters['status']))
        @php $q = $qs; unset($q['status']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
          حالة: {{ $filters['status'] }} <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['payment_status']))
        @php $q = $qs; unset($q['payment_status']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
          الدفع: {{ $filters['payment_status'] }} <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['email']))
        @php $q = $qs; unset($q['email']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
          البريد: {{ $filters['email'] }} <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['from']))
        @php $q = $qs; unset($q['from']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
          من: {{ $filters['from'] }} <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['to']))
        @php $q = $qs; unset($q['to']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
          إلى: {{ $filters['to'] }} <span class="text-gray-500">×</span>
        </a>
      @endif
    </div>
  @endif

  <div class="bg-white dark:bg-gray-900 shadow rounded-2xl overflow-x-auto ring-1 ring-black/5 dark:ring-white/10">
    <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
      <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
        <tr class="text-right text-gray-700 dark:text-gray-200">
          <th class="p-3 w-24 font-medium">{!! $sortLink('id', 'رقم الطلب') !!}</th>
          <th class="p-3 font-medium">{!! $sortLink('user', 'العميل') !!}</th>
          <th class="p-3 font-medium text-center">{!! $sortLink('payment_status', 'الدفع') !!}</th>
          <th class="p-3 font-medium text-center">{!! $sortLink('status', 'حالة الطلب') !!}</th>
          <th class="p-3 font-medium text-right">{!! $sortLink('created_at', 'التاريخ') !!}</th>
          <th class="p-3 font-medium text-right">…</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @php
          $statusBadge = fn($s) => match($s){
            'pending'    => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
            'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300',
            'shipped'    => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
            'cancelled'  => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
            default      => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300'
          };
          $payBadge = fn($s) => match($s){
            'paid'     => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
            'refunded' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
            'unpaid'   => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300',
            default    => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300'
          };
        @endphp

        @forelse($orders as $order)
          <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
            <td class="p-3 tabular-nums">
              <a class="text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('admin.orders.show', $order) }}">
                {{ method_exists($order,'getNumberAttribute') ? $order->number : $order->id }}
              </a>
            </td>
            <td class="p-3">
              <div class="min-w-0">
                <div class="font-medium truncate">{{ $order->user->name ?? '—' }}</div>
                @if($order->user?->email)
                  <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate" dir="ltr">{{ $order->user->email }}</div>
                @endif
              </div>
            </td>
            <td class="p-3 text-center">
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $payBadge($order->payment_status ?? 'unpaid') }}">
                {{ $order->payment_status ?? 'unpaid' }}
              </span>
            </td>
            <td class="p-3 text-center">
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $statusBadge($order->status) }}">
                {{ $order->status }}
              </span>
            </td>
            <td class="p-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap" title="{{ $order->created_at->format('Y-m-d H:i') }}">
              {{ $order->created_at->diffForHumans() }}
            </td>
            <td class="p-3">
              <a class="text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('admin.orders.show', $order) }}">عرض</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-6 text-center text-gray-500 dark:text-gray-300">لا توجد نتائج مطابقة للمرشّحات الحالية.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
