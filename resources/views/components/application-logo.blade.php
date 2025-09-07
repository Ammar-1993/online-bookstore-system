<a href="/" aria-label=" المتجر الإلكتروني للكتب">
  <svg class="size-16" viewBox="0 0 48 48" role="img" xmlns="http://www.w3.org/2000/svg">
    <title> المتجر الإلكتروني للكتب</title>
    <defs>
      <!-- تدرّج الخلفية -->
      <linearGradient id="obs-g1" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#4F46E5"/>
        <stop offset="100%" stop-color="#8B5CF6"/>
      </linearGradient>
      <!-- تدرّج لمعان خفيف فوق الخلفية -->
      <linearGradient id="obs-g2" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#FFFFFF" stop-opacity=".18"/>
        <stop offset="1" stop-color="#FFFFFF" stop-opacity="0"/>
      </linearGradient>
      <!-- ظل خفيف للصفحات -->
      <filter id="obs-soft" x="-20%" y="-20%" width="140%" height="140%">
        <feGaussianBlur in="SourceAlpha" stdDeviation="0.8"/>
        <feOffset dy="0.6"/>
        <feColorMatrix type="matrix"
          values="0 0 0 0 0
                  0 0 0 0 0
                  0 0 0 0 0
                  0 0 0 .18 0"/>
        <feMerge>
          <feMergeNode/>
          <feMergeNode in="SourceGraphic"/>
        </feMerge>
      </filter>
    </defs>

    <!-- دائرة الخلفية -->
    <circle cx="24" cy="24" r="22" fill="url(#obs-g1)"/>
    <!-- لمعان علوي خفيف -->
    <path d="M4 12a22 22 0 0 1 40 0v0c-6 2.5-13.2 3.8-20 3.8S10 14.5 4 12z" fill="url(#obs-g2)"/>

    <!-- كتاب مفتوح (صفحتان) -->
    <g filter="url(#obs-soft)">
      <!-- الصفحة اليسرى -->
      <path fill="#FFFFFF"
            d="M12 15h9.2c1.9 0 3.4 1.5 3.4 3.4v14.2c-2.3-1.7-5.2-2.6-8.6-2.6H12V15z"/>
      <!-- الصفحة اليمنى (انعكاس) -->
      <g transform="translate(48,0) scale(-1,1)">
        <path fill="#FFFFFF"
              d="M12 15h9.2c1.9 0 3.4 1.5 3.4 3.4v14.2c-2.3-1.7-5.2-2.6-8.6-2.6H12V15z"/>
      </g>
      <!-- حافة عمودية دقيقة بين الصفحات -->
      <rect x="23.5" y="18" width="1" height="15" fill="#E5E7EB" opacity=".85"/>
    </g>

    <!-- شريط إشارة/شارة (Book ribbon) كعنصر مميِّز -->
    <path d="M23.2 13.2h1.6a1 1 0 0 1 1 1v6.2l-1.8-1-1.8 1v-6.2a1 1 0 0 1 1-1z" fill="#10B981"/>

    <!-- لمسة ظل سفلية للكتاب -->
    <ellipse cx="24" cy="35.8" rx="10.8" ry="1.6" fill="#1F2937" opacity=".12"/>
  </svg>
</a>
