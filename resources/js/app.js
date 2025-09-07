import './bootstrap';
import { setupPageLoader } from './plugins/pageLoader';
import setupLiveSearch from './plugins/liveSearch';
import setupWishlistToggle from './plugins/wishlist';

document.addEventListener('DOMContentLoaded', () => {
  setupPageLoader?.();
  setupLiveSearch?.();
  setupWishlistToggle?.();
});
