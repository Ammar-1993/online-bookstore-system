@props([
  'label' => '',
  'value' => '0',
  'icon'  => '📌',
  'tone'  => 'slate', // indigo | emerald | amber | rose | slate
])

@php
$tones = [
  'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-100'],
  'emerald'=> ['bg' => 'bg-emerald-50','text' => 'text-emerald-700','ring'=>'ring-emerald-100'],
  'amber'  => ['bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'ring'=>'ring-amber-100'],
  'rose'   => ['bg' => 'bg-rose-50',   'text' => 'text-rose-700',   'ring'=>'ring-rose-100'],
  'slate'  => ['bg' => 'bg-slate-50',  'text' => 'text-slate-700',  'ring'=>'ring-slate-100'],
];
$t = $tones[$tone] ?? $tones['slate'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border rounded-2xl p-4 hover:shadow-sm transition']) }}>
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm text-gray-500 mb-1">{{ $label }}</div>
      <div class="text-2xl font-bold">{{ $value }}</div>
    </div>
    <div class="size-12 rounded-2xl {{ $t['bg'] }} {{ $t['ring'] }} ring-1 flex items-center justify-center">
      <span class="{{ $t['text'] }} text-xl">{{ $icon }}</span>
    </div>
  </div>
</div>
