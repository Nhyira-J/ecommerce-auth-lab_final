document.addEventListener('DOMContentLoaded', () => {
  const productForm = document.getElementById('productForm');
  const msg = document.getElementById('productMessage');
  const tableBody = document.querySelector('#productTable tbody');
  const catSelect = document.getElementById('product_cat');
  const brandSelect = document.getElementById('product_brand');

  function clearForm() {
    productForm.reset();
    productForm.product_id.value = '';
  }

  async function loadProducts() {
    try {
      const res = await fetch('../actions/fetch_product_action.php'); // we'll add this action shortly (or reuse existing)
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      tableBody.innerHTML = '';
      if (data.status === 'success') {
        data.data.forEach(p => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${p.product_id}</td>
            <td>${p.product_image ? `<img src="${p.product_image}" width="80">` : '—'}</td>
            <td>${escapeHtml(p.product_title)}</td>
            <td>${escapeHtml(p.brand_name)}</td>
            <td>${escapeHtml(p.cat_name)}</td>
            <td>${Number(p.product_price).toFixed(2)}</td>
            <td>
              <button class="edit-btn" data-id="${p.product_id}">Edit</button>
            </td>
          `;
          tableBody.appendChild(tr);
        });
      } else {
        tableBody.innerHTML = '<tr><td colspan="7">No products</td></tr>';
      }
    } catch (err) {
      console.error(err);
      alert('Failed to load products (see console).');
    }
  }

  function escapeHtml(s) {
    return s ? s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
  }

  // filter brands when category changes
  catSelect.addEventListener('change', () => {
    const catId = catSelect.value;
    Array.from(brandSelect.options).forEach(opt => {
      const dat = opt.dataset.cat || '';
      if (!opt.value) return;
      opt.style.display = (!catId || dat === catId) ? '' : 'none';
    });
  });

  // submit add/update
  productForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';
    const fd = new FormData(productForm);
    const productId = fd.get('product_id');

    try {
      // If no product_id => create
      if (!productId) {
        const res = await fetch('../actions/add_product_action.php', { method: 'POST', body: fd });
        const json = await res.json();
        if (json.status !== 'success') { msg.textContent = json.message || 'Add failed'; return; }

        // If image file provided, upload it with product_id
        const newId = json.product_id;
        const fileInput = document.getElementById('product_image');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
          const fd2 = new FormData();
          fd2.append('product_id', newId);
          fd2.append('image', fileInput.files[0]);
          const uploadRes = await fetch('../actions/upload_product_image_action.php', { method: 'POST', body: fd2 });
          const upJson = await uploadRes.json();
          if (upJson.status !== 'success') { msg.textContent = 'Product saved but image upload failed: ' + upJson.message; loadProducts(); return; }
        }

        msg.textContent = 'Product created';
        clearForm();
        loadProducts();
      } else {
        // update existing
        const res = await fetch('../actions/update_product_action.php', { method: 'POST', body: fd });
        const json = await res.json();
        if (json.status !== 'success') { msg.textContent = json.message || 'Update failed'; return; }

        // optional image replace
        const fileInput = document.getElementById('product_image');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
          const fd2 = new FormData();
          fd2.append('product_id', productId);
          fd2.append('image', fileInput.files[0]);
          const uploadRes = await fetch('../actions/upload_product_image_action.php', { method: 'POST', body: fd2 });
          const upJson = await uploadRes.json();
          if (upJson.status !== 'success') { msg.textContent = 'Product updated but image upload failed: ' + upJson.message; loadProducts(); return; }
        }

        msg.textContent = 'Product updated';
        clearForm();
        loadProducts();
      }
    } catch (err) {
      console.error(err);
      msg.textContent = 'Network/server error';
    }
  });

  // Edit button delegation
  tableBody.addEventListener('click', async (e) => {
    if (e.target.classList.contains('edit-btn')) {
      const id = e.target.dataset.id;
      try {
        // fetch product by id
        const res = await fetch(`../actions/get_product_action.php?product_id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
          const p = json.data;
          productForm.product_id.value = p.product_id;
          productForm.product_cat.value = p.product_cat;
          // trigger brand filter then set brand
          catSelect.dispatchEvent(new Event('change'));
          productForm.product_brand.value = p.product_brand;
          productForm.product_title.value = p.product_title;
          productForm.product_price.value = p.product_price;
          productForm.product_desc.value = p.product_desc;
          productForm.product_keywords.value = p.product_keywords;
          msg.textContent = 'Loaded product for edit';
        } else {
          alert(json.message || 'Could not fetch product');
        }
      } catch (err) {
        console.error(err);
        alert('Failed to fetch product');
      }
    }
  });

  // initial
  loadProducts();
});
