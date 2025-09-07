// resources/js/plugins/liveSearch.js
export default function setupLiveSearch() {
  const form = document.getElementById('filtersForm');
  const results = document.getElementById('results');
  if (!form || !results) return;

  const apply = async () => {
    const params = new URLSearchParams(new FormData(form));
    params.set('partial', '1');
    const url = form.action.replace(/\/books$/, '/books/search') + '?' + params.toString();

    results.setAttribute('aria-busy', 'true');
    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const html = await res.text();
      results.innerHTML = html;
      window.scrollTo({ top: form.offsetTop, behavior: 'smooth' });
    } finally {
      results.removeAttribute('aria-busy');
    }
  };

  // على التغيير
  form.addEventListener('change', (e) => {
    // تخطي الضغط على زر "إعادة ضبط"
    if (e.target.closest('a')) return;
    apply();
  });

  // على الإرسال
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    apply();
  });
}
