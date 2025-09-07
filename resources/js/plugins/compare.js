export default function setupCompareToggle() {
  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = tokenMeta ? tokenMeta.content : null;
  const badge = document.getElementById('compare-count');

  const updateBadge = (count) => {
    if (!badge) return;
    if (count > 0) {
      badge.textContent = String(count);
      badge.classList.remove('hidden');
    } else {
      badge.classList.add('hidden');
      badge.textContent = '';
    }
  };

  const onClick = async (btn) => {
    const url = btn.getAttribute('data-url');
    if (!url) return;

    btn.disabled = true;
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
          'Accept': 'application/json'
        }
      });

      const data = await res.json();

      if (!res.ok && data?.reason === 'limit') {
        // حد أقصى 4
        // يمكنك لاحقًا إظهار Toast، الآن نكتفي بوميض بسيط
        btn.classList.add('ring-2','ring-rose-500');
        setTimeout(() => btn.classList.remove('ring-2','ring-rose-500'), 600);
        return;
      }

      const active = !!data.active;
      const svg = btn.querySelector('svg');
      if (svg) {
        svg.classList.toggle('fill-indigo-600', active);
        svg.classList.toggle('fill-transparent', !active);
      }
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');

      if (typeof data.count === 'number') updateBadge(data.count);
    } catch (_e) {
      // تجاهل الآن أو أظهر إشعار
    } finally {
      btn.disabled = false;
    }
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.compare-toggle');
    if (!btn) return;
    e.preventDefault();
    onClick(btn);
  });
}
