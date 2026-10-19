import './bootstrap';
import { setupPageLoader } from './plugins/pageLoader';
import setupLiveSearch from './plugins/liveSearch';
import setupWishlistToggle from './plugins/wishlist';
import setupCompareToggle from './plugins/compare';

document.addEventListener('DOMContentLoaded', () => {
  setupPageLoader?.();
  setupLiveSearch?.();
  setupWishlistToggle?.();
  setupCompareToggle?.();
  window.__syncThemeUI?.();
});
