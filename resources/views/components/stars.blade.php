@props([
    'value' => 0,   /* يقبل float أو int */
    'size'  => 'md', /* sm|md|lg */
    'readonly' => true,
])

@php
    $val = (float) $value;
    $full = floor($val);
    $half = ($val - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
    $cls = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->class('flex items-center gap-0.5 select-none') }}>
    @for($i=0; $i<$full; $i++)
        <svg class="{{ $cls }} text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z" />
        </svg>
    @endfor

    @if($half)
        <svg class="{{ $cls }} text-yellow-400" viewBox="0 0 24 24" aria-hidden="true">
            <defs>
                <linearGradient id="half-grad">
                    <stop offset="50%" stop-color="currentColor"/>
                    <stop offset="50%" stop-color="transparent"/>
                </linearGradient>
            </defs>
            <path fill="url(#half-grad)"
                  d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.786 1.402 8.168L12 18.896l-7.336 3.868 1.402-8.168L.132 9.21l8.2-1.192z"/>
        </svg>
    @endif

    @for($i=0; $i<$empty; $i++)
        <svg class="{{ $cls }} text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z" />
        </svg>
    @endfor
</div>
