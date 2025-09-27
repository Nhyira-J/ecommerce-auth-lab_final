document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.querySelector('#categoryTable tbody');
  const addForm = document.getElementById('addCategoryForm');

  // Fetch categories
  async function loadCategories() {
    const res = await fetch('../actions/fetch_category_action.php');
    const data = await res.json();

    tableBody.innerHTML = '';
    if (data.status === 'success') {
      data.data.forEach(cat => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${cat.cat_id}</td>
          <td><input type="text" value="${cat.cat_name}" data-id="${cat.cat_id}"></td>
          <td>
            <button class="update-btn" data-id="${cat.cat_id}">Update</button>
            <button class="delete-btn" data-id="${cat.cat_id}">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }
  }

  // Add category
  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(addForm);

    const res = await fetch('../actions/add_category_action.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    alert(data.message);
    if (data.status === 'success') {
      addForm.reset();
      loadCategories();
    }
  });

  // Delegate update/delete events
  tableBody.addEventListener('click', async (e) => {
    const id = e.target.dataset.id;

    // Update
    if (e.target.classList.contains('update-btn')) {
      const input = e.target.closest('tr').querySelector('input');
      const newName = input.value;

      const formData = new FormData();
      formData.append('id', id);
      formData.append('name', newName);

      const res = await fetch('../actions/update_category_action.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      alert(data.message);
      if (data.status === 'success') loadCategories();
    }

    // Delete
    if (e.target.classList.contains('delete-btn')) {
      if (!confirm('Are you sure you want to delete this category?')) return;

      const formData = new FormData();
      formData.append('id', id);

      const res = await fetch('../actions/delete_category_action.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      alert(data.message);
      if (data.status === 'success') loadCategories();
    }
  });

  // Initial load
  loadCategories();
});
