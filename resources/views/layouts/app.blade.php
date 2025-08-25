<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts (Arabic UI + Display) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- UI text: IBM Plex Sans Arabic --}}
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans-arabic:300,400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Headings: Readex Pro --}}
    <link href="https://fonts.bunny.net/css?family=readex-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles from Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire styles -->
    @livewireStyles

    <!-- Arabic font setup & readability tweaks -->
    <style>
      :root{
        --font-ui: "IBM Plex Sans Arabic", "Noto Sans Arabic", system-ui, -apple-system, "Segoe UI", Tahoma, Arial, sans-serif;
        --font-display: "Readex Pro", var(--font-ui);
      }

      html[dir="rtl"] body{ letter-spacing: 0; } /* لا نباعد الحروف بالعربية */

      /* اجعل الخط العربي هو الافتراضي للتطبيق كله */
      body{
        font-family: var(--font-ui) !important;  /* يغلّب على font-sans من Tailwind */
        line-height: 1.7;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
      }

      /* عناوين جذّابة */
      h1, h2, h3, .font-display{
        font-family: var(--font-display);
        font-weight: 600;
        line-height: 1.3;
      }

      /* أرقام منسّقة للجداول عند الحاجة */
      .tabular-nums{ font-variant-numeric: tabular-nums; }

      /* كود */
      code, pre{
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
      }

      /* تحسين تمييز التحديد */
      ::selection{ background: rgba(59,130,246,.18); }
      @media (prefers-color-scheme: dark){
        ::selection{ background: rgba(59,130,246,.28); }
      }
    </style>
</head>

<body class="antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 font-display">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <x-flash-stack />
            @isset($slot)
                {{ $slot }} {{-- يُستخدم عند <x-app-layout> --}}
            @else
                @yield('content') {{-- يُستخدم عند @extends(...) --}}
            @endisset
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    {{-- داخل الـ body قبل إغلاقه مثلاً --}}
    <x-page-loader />
</body>
</html>
