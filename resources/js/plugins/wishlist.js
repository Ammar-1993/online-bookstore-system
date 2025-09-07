export default function setupWishlistToggle() {
  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = tokenMeta ? tokenMeta.content : null;

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
      const active = !!data.active;

      // تحديث مظهر الأيقونة وحالة aria
      const svg = btn.querySelector('svg');
      if (svg) {
        svg.classList.toggle('fill-rose-600', active);
        svg.classList.toggle('fill-transparent', !active);
      }
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      btn.title = active ? 'إزالة من المفضّلة' : 'إضافة إلى المفضّلة';
    } catch (_e) {
      // يمكن لاحقاً إضافة إشعار خطأ
    } finally {
      btn.disabled = false;
    }
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.wishlist-toggle');
    if (!btn) return;
    e.preventDefault();
    onClick(btn);
  });
}
