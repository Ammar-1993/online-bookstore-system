@props([
  'label' => '',
  'value' => '',
  'icon'  => '📦',
  'tone'  => 'slate',   // indigo | emerald | amber | rose | slate | sky | violet
  'href'  => null,      // لو مررت رابط يصير الكارد قابل للنقر
])

@php
  $tones = [
    'indigo'  => ['bg'=>'bg-indigo-50 dark:bg-indigo-500/10',  'ring'=>'ring-indigo-600/20',  'text'=>'text-indigo-600 dark:text-indigo-400'],
    'emerald' => ['bg'=>'bg-emerald-50 dark:bg-emerald-500/10','ring'=>'ring-emerald-600/20','text'=>'text-emerald-600 dark:text-emerald-400'],
    'amber'   => ['bg'=>'bg-amber-50 dark:bg-amber-500/10',    'ring'=>'ring-amber-600/20',   'text'=>'text-amber-600 dark:text-amber-400'],
    'rose'    => ['bg'=>'bg-rose-50 dark:bg-rose-500/10',      'ring'=>'ring-rose-600/20',    'text'=>'text-rose-600 dark:text-rose-400'],
    'sky'     => ['bg'=>'bg-sky-50 dark:bg-sky-500/10',        'ring'=>'ring-sky-600/20',     'text'=>'text-sky-600 dark:text-sky-400'],
    'violet'  => ['bg'=>'bg-violet-50 dark:bg-violet-500/10',  'ring'=>'ring-violet-600/20',  'text'=>'text-violet-600 dark:text-violet-400'],
    'slate'   => ['bg'=>'bg-slate-50 dark:bg-slate-500/10',    'ring'=>'ring-slate-600/20',   'text'=>'text-slate-600 dark:text-slate-300'],
  ];
  $t = $tones[$tone] ?? $tones['slate'];

  $card   = "group rounded-2xl p-4 bg-white dark:bg-gray-900 shadow ring-1 ring-inset ring-black/5 dark:ring-white/10 transition
             hover:-translate-y-0.5 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
             focus-visible:ring-indigo-500";
  $inner  = "flex items-center gap-3";
  $iconBx = "h-10 w-10 rounded-xl grid place-items-center {$t['bg']} ring-1 {$t['ring']}";
  $labelC = "text-sm text-gray-600 dark:text-gray-300";
  $valueC = "text-2xl font-display font-semibold text-gray-900 dark:text-gray-100";
@endphp

@if($href)
  <a href="{{ $href }}" class="{{ $card }} cursor-pointer" data-ripple data-loader>
    <div class="{{ $inner }}">
      <div class="{{ $iconBx }}"><span class="{{ $t['text'] }} text-lg">{{ $icon }}</span></div>
      <div class="min-w-0">
        <div class="{{ $labelC }}">{{ $label }}</div>
        <div class="{{ $valueC }} tabular-nums">{{ $value }}</div>
      </div>
    </div>
  </a>
@else
  <div class="{{ $card }}">
    <div class="{{ $inner }}">
      <div class="{{ $iconBx }}"><span class="{{ $t['text'] }} text-lg">{{ $icon }}</span></div>
      <div class="min-w-0">
        <div class="{{ $labelC }}">{{ $label }}</div>
        <div class="{{ $valueC }} tabular-nums">{{ $value }}</div>
      </div>
    </div>
  </div>
@endif
