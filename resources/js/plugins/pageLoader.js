// resources/js/plugins/pageLoader.js

// دالة التفعيل (Named Export)
export function setupPageLoader() {
  // يتوقع وجود عنصر من <x-page-loader /> يحمل هذا المعرّف
  const loader = document.getElementById('page-loader');
  if (!loader) return;

  // helpers
  const show = () => {
    loader.classList.remove('hidden', 'opacity-0');
  };
  const hide = () => {
    // إخفاء تدريجي (لو عندك transition على opacity)
    loader.classList.add('opacity-0');
    // إن رغبت: أخفِه نهائيًا بعد انتهاء الانتقال
    // setTimeout(() => loader.classList.add('hidden'), 200);
  };

  // أظهر اللودر عند النقر على أي عنصر معلّم بـ data-loader أو رابط/زر ينقل صفحة
  document.addEventListener('click', (e) => {
    const el = e.target.closest('a, button, [data-loader]');
    if (!el) return;

    // تجاهل الروابط التي تفتح تبويب جديد أو anchors داخلية
    const isNewTab = el.target === '_blank' || e.metaKey || e.ctrlKey;
    const href = el.getAttribute?.('href') || '';
    if (isNewTab || href.startsWith('#')) return;

    // تجاهل العناصر التي تحمل data-no-loader
    if (el.hasAttribute('data-no-loader')) return;

    show();
  });

  // أخفِ اللودر عند اكتمال التحميل/الرجوع من الكاش
  window.addEventListener('load', hide);
  window.addEventListener('pageshow', hide);
}

// Default Export أيضاً (لتوافق الاستيرادين)
export default setupPageLoader;
