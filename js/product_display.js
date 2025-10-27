// js/product_display.js
document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('productGrid');
  const pagination = document.getElementById('pagination');
  const searchInput = document.getElementById('searchInput');
  const filterCategory = document.getElementById('filterCategory');
  const filterBrand = document.getElementById('filterBrand');

  let page = 1;
  const limit = 12;
  let currentMode = 'list'; // list, search, cat, brand
  let lastQuery = '';

  // debounce helper
  function debounce(fn, wait=300){
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), wait); };
  }

  async function fetchAndRender(params = {}) {
    const url = new URL('../actions/product_actions.php', location.href);
    Object.keys(params).forEach(k => url.searchParams.set(k, params[k]));
    const res = await fetch(url);
    if (!res.ok) { console.error('Fetch failed', res.status); return; }
    const json = await res.json();
    if (json.status !== 'success') { grid.innerHTML = `<p>${json.message || 'No results'}</p>`; pagination.innerHTML = ''; return; }
    renderProducts(json.data || []);
    renderPagination(json.total || 0, json.page || 1, json.limit || limit);
  }

  function renderProducts(items) {
    if (!items || items.length === 0) { grid.innerHTML = '<p>No products found.</p>'; return; }
    grid.innerHTML = items.map(p => `
      <div class="product-card">
        <a href="single_product.php?id=${p.product_id}">
          <div class="product-image">${p.product_image ? `<img src="${p.product_image}" alt="${escapeHtml(p.product_title)}">` : '<div class="placeholder">No image</div>'}</div>
          <div class="product-body">
            <h3>${escapeHtml(p.product_title)}</h3>
            <p class="meta">${escapeHtml(p.brand_name || '')} • ${escapeHtml(p.cat_name || '')}</p>
            <p class="price">$${Number(p.product_price).toFixed(2)}</p>
          </div>
        </a>
      </div>`).join('');
  }

  function renderPagination(total, currentPage, perPage) {
    pagination.innerHTML = '';
    const pages = Math.max(1, Math.ceil(total / perPage));
    if (pages <= 1) return;
    for (let i = 1; i <= pages; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      btn.className = (i === currentPage) ? 'page current' : 'page';
      btn.addEventListener('click', () => {
        page = i;
        load();
      });
      pagination.appendChild(btn);
    }
  }

  function escapeHtml(s){ return s? s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''; }

  async function load() {
    const cat = filterCategory.value;
    const brand = filterBrand.value;
    const q = searchInput.value.trim();

    if (q) {
      currentMode = 'search';
      lastQuery = q;
      await fetchAndRender({ action: 'search', q: q, page: page, limit });
      return;
    }
    if (cat) {
      currentMode = 'cat';
      await fetchAndRender({ action: 'filter_cat', cat_id: cat, page: page, limit });
      return;
    }
    if (brand) {
      currentMode = 'brand';
      await fetchAndRender({ action: 'filter_brand', brand_id: brand, page: page, limit });
      return;
    }
    currentMode = 'list';
    await fetchAndRender({ action: 'list', page: page, limit });
  }

  const debouncedLoad = debounce(() => { page = 1; load(); }, 350);
  searchInput.addEventListener('input', debouncedLoad);
  filterCategory.addEventListener('change', () => { filterBrand.value=''; page=1; load(); });
  filterBrand.addEventListener('change', () => { filterCategory.value=''; page=1; load(); });

  // initial load
  load();
});
