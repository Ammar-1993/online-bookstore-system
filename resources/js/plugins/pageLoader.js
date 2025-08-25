// resources/js/plugins/pageLoader.js
(function () {
  const byId = (id) => document.getElementById(id);
  const overlay = () => byId('app-page-loader');
  const labelEl = () => byId('app-page-loader-label');

  let counter = 0;             // لمنع الإخفاء المبكر مع الطلبات المتداخلة
  let fetchPending = 0;        // لمراقبة fetch إن استُخدم
  const body = document.body;

  const _applyBusy = () => {
    body.setAttribute('aria-busy', 'true');
    body.style.cursor = 'progress';
    body.style.overflow = 'hidden'; // إيقاف التمرير
  };
  const _clearBusy = () => {
    body.removeAttribute('aria-busy');
    body.style.cursor = '';
    body.style.overflow = ''; // إعادة التمرير
  };

  const _ensureVisible = () => {
    const el = overlay();
    if (!el) return;
    el.classList.remove('hidden');
    _applyBusy();
  };
  const _ensureHidden = () => {
    const el = overlay();
    if (!el) return;
    el.classList.add('hidden');
    _clearBusy();
    // إعادة الحالة للنمط غير المحدّد
    el.dataset.mode = 'indeterminate';
    el.style.removeProperty('--progress');
  };

  const show = () => { counter++; _ensureVisible(); };
  const hide = () => {
    counter = Math.max(0, counter - 1);
    if (counter === 0 && fetchPending === 0) _ensureHidden();
  };

  // API عام بسيط
  const setLabel = (text) => { const l = labelEl(); if (l && typeof text === 'string') l.textContent = text; };
  const setProgress = (value /* 0..100 */) => {
    const el = overlay(); if (!el) return;
    let v = Number(value);
    if (Number.isNaN(v)) return;
    v = Math.min(100, Math.max(0, v));
    el.dataset.mode = 'determinate';
    el.style.setProperty('--progress', (v / 100).toString());
  };
  const isVisible = () => !!overlay() && !overlay().classList.contains('hidden');

  // متاح للاستدعاء اليدوي والأحداث الموروثة لديك
  window.PageLoader = { show, hide, setLabel, setProgress, isVisible };

  window.addEventListener('loader:show', show);
  window.addEventListener('loader:hide', hide);
  window.addEventListener('loader:label', (e) => setLabel(e.detail));
  window.addEventListener('loader:progress', (e) => setProgress(e.detail));

  // نماذج: أي submit (إلا المعطّل/المستثنى)
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.hasAttribute('data-no-loader') || form.closest('[data-no-loader]')) return;

    // عطّل زر الإرسال الأول (اختياري)
    const btn = form.querySelector('button[type="submit"], [type="submit"]');
    if (btn && !btn.hasAttribute('data-no-disable')) {
      btn.setAttribute('aria-busy', 'true');
      btn.classList.add('opacity-70','cursor-not-allowed');
      btn.disabled = true;
    }
    show();
  }, true);

  // أزرار/روابط تحمل data-loader
  document.addEventListener('click', (e) => {
    const trigger = e.target && e.target.closest && e.target.closest('[data-loader]');
    if (!trigger) return;

    // لا تُظهر اللودر للروابط في نافذة جديدة/تحميل ملف/المعطلة/المستثناة
    if (
      (trigger.tagName === 'A' && trigger.getAttribute('target') === '_blank') ||
      trigger.hasAttribute('download') ||
      trigger.hasAttribute('data-no-loader')
    ) return;

    show();
  }, { passive: true });

  // Ripple لأي عنصر يحمل data-ripple (كما في نسختك)
  document.addEventListener('click', (e) => {
    const el = e.target && e.target.closest && e.target.closest('[data-ripple]');
    if (!el) return;
    try {
      const rect = el.getBoundingClientRect();
      const diameter = Math.max(rect.width, rect.height);
      const x = (e.clientX || (rect.left + rect.width/2)) - rect.left - diameter/2;
      const y = (e.clientY || (rect.top + rect.height/2)) - rect.top - diameter/2;
      const circle = document.createElement('span');
      circle.className = 'ripple';
      circle.style.width = circle.style.height = `${diameter}px`;
      circle.style.left = `${x}px`;
      circle.style.top = `${y}px`;
      el.appendChild(circle);
      setTimeout(() => circle.remove(), 650);
    } catch (_) {}
  }, { passive: true });

  // تكامل مع Livewire (إن وُجد)
  document.addEventListener('livewire:load', () => {
    try {
      if (window.Livewire && window.Livewire.hook) {
        window.Livewire.hook('message.sent', () => show());
        window.Livewire.hook('message.processed', () => hide());
        window.Livewire.hook('message.failed', () => hide());
      }
    } catch (_) {}
  });

  // تكامل مع Turbo/Hotwire (لو موجود)
  document.addEventListener('turbo:visit', show);
  document.addEventListener('turbo:load', hide);

  // تكامل مع Inertia.js (لو موجود)
  window.addEventListener('inertia:start', show);
  window.addEventListener('inertia:finish', hide);
  window.addEventListener('inertia:error', hide);

  // Axios interceptors (لو محمّل في bootstrap)
  try {
    if (window.axios && window.axios.interceptors) {
      window.axios.interceptors.request.use((config) => {
        if (!config?.headers?.['X-No-Loader']) show();
        return config;
      }, (error) => { hide(); return Promise.reject(error); });

      window.axios.interceptors.response.use((resp) => {
        if (!resp?.config?.headers?.['X-No-Loader']) hide();
        return resp;
      }, (error) => { hide(); return Promise.reject(error); });
    }
  } catch(_) {}

  // تغليف fetch بشكل خفيف
  try {
    const _fetch = window.fetch;
    window.fetch = function(input, init) {
      const headers = (init && init.headers) || (input && input.headers);
      const noLoader = headers && (headers.get?.('X-No-Loader') || headers['X-No-Loader']);
      if (!noLoader) { fetchPending++; show(); }
      return _fetch(input, init)
        .finally(() => {
          if (!noLoader) { fetchPending = Math.max(0, fetchPending - 1); hide(); }
        });
    };
  } catch(_) {}

  // تحسين تجربة الانتقال/الرجوع من الكاش
  window.addEventListener('beforeunload', show);
  window.addEventListener('pageshow', hide);
})();
