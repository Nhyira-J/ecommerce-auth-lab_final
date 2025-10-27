document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.querySelector('#brandTable tbody');
  const addForm = document.getElementById('addBrandForm');

  async function loadBrands() {
    try {
      const res = await fetch('../actions/fetch_brand_action.php');
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      console.log('loadBrands →', data);
      tableBody.innerHTML = '';
      if (data.status === 'success') {
        data.data.forEach(b => {
          const r = document.createElement('tr');
          r.innerHTML = `
            <td>${b.brand_id}</td>
            <td><input data-id="${b.brand_id}" class="brand-name" value="${escapeHtml(b.brand_name)}"></td>
            <td>
              <select data-id="${b.brand_id}" class="brand-cat">
                ${makeCategoryOptions(b.cat_id)}
              </select>
            </td>
            <td>
              <button class="update-btn" data-id="${b.brand_id}">Update</button>
              <button class="delete-btn" data-id="${b.brand_id}">Delete</button>
            </td>`;
          tableBody.appendChild(r);
        });
      } else {
        console.warn('No brands or error:', data.message);
      }
    } catch (err) {
      console.error('Error loading brands', err);
      alert('Failed to load brands (see console).');
    }
  }

  function escapeHtml(s) {
    return s ? s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : '';
  }

  function makeCategoryOptions(selectedId) {
    const catSelect = document.querySelector('#addBrandForm select[name="cat_id"]');
    if (!catSelect) return '';
    return Array.from(catSelect.options).map(opt => {
      if (!opt.value) return '';
      return `<option value="${opt.value}" ${opt.value == selectedId ? 'selected' : ''}>${opt.text}</option>`;
    }).join('');
  }

  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(addForm);
    const name = (fd.get('name') || '').trim();
    const cat_id = fd.get('cat_id') || '';

    if (!name || !cat_id) { alert('Name and category are required'); return; }

    try {
      const res = await fetch('../actions/add_brand_action.php', { method: 'POST', body: fd });
      const data = await res.json();
      alert(data.message);
      if (data.status === 'success') { addForm.reset(); loadBrands(); }
    } catch (err) { console.error(err); alert('Add failed'); }
  });

  tableBody.addEventListener('click', async (e) => {
    const id = e.target.dataset.id;
    if (!id) return;

    // UPDATE
    if (e.target.classList.contains('update-btn')) {
      const row = e.target.closest('tr');
      const name = (row.querySelector('.brand-name').value || '').trim();
      const cat_id = row.querySelector('.brand-cat').value;

      if (!name || !cat_id) { alert('Name and category required'); return; }

      const fd = new FormData();
      fd.append('id', id);
      fd.append('name', name);
      fd.append('cat_id', cat_id);

      try {
        console.log('update request', id, name, cat_id);
        const res = await fetch('../actions/update_brand_action.php', { method: 'POST', body: fd });
        const data = await res.json();
        console.log('update response', data);
        alert(data.message);
        if (data.status === 'success') loadBrands();
      } catch (err) {
        console.error('Update failed', err);
        alert('Update failed (see console).');
      }
    }

    // DELETE
    if (e.target.classList.contains('delete-btn')) {
      if (!confirm('Delete brand?')) return;
      const fd = new FormData();
      fd.append('id', id);
      try {
        const res = await fetch('../actions/delete_brand_action.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message);
        if (data.status === 'success') loadBrands();
      } catch (err) { console.error(err); alert('Delete failed'); }
    }
  });

  loadBrands();
});
