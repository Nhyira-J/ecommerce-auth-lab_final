document.addEventListener('DOMContentLoaded', () => {
    // Update quantity
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', async function() {
            const cart_id = this.dataset.cartId;
            const qty = parseInt(this.value);
            const price = parseFloat(this.dataset.price);

            if (qty < 1) {
                alert('Quantity must be at least 1');
                this.value = 1;
                return;
            }

            try {
                const response = await fetch('../actions/update_quantity_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `cart_id=${cart_id}&qty=${qty}`
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Update item subtotal
                    const row = this.closest('tr');
                    const subtotalCell = row.querySelector('.item-subtotal');
                    subtotalCell.textContent = 'GH₵' + (price * qty).toFixed(2);

                    // Recalculate totals
                    updateCartTotals();
                } else {
                    alert(data.message || 'Failed to update quantity');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        });
    });

    // Remove item
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Remove this item from cart?')) return;

            const cart_id = this.dataset.cartId;

            try {
                const response = await fetch('../actions/remove_from_cart_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `cart_id=${cart_id}`
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Remove row from table
                    const row = this.closest('tr');
                    row.remove();

                    // Update cart count
                    document.getElementById('cart-count').textContent = data.cart_count;

                    // Recalculate totals
                    updateCartTotals();

                    // Check if cart is now empty
                    const tableBody = document.querySelector('.cart-table tbody');
                    if (tableBody.children.length === 0) {
                        location.reload();
                    }
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        });
    });

    // Empty cart
    const emptyCartBtn = document.getElementById('empty-cart-btn');
    if (emptyCartBtn) {
        emptyCartBtn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to empty your cart?')) return;

            try {
                const response = await fetch('../actions/empty_cart_action.php', {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to empty cart');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        });
    }

    // Function to update cart totals
    function updateCartTotals() {
        let subtotal = 0;

        document.querySelectorAll('.cart-table tbody tr').forEach(row => {
            const qtyInput = row.querySelector('.qty-input');
            const price = parseFloat(qtyInput.dataset.price);
            const qty = parseInt(qtyInput.value);
            subtotal += price * qty;
        });

        const tax = subtotal * 0.05;
        const total = subtotal + tax;

        document.getElementById('summary-subtotal').textContent = 'GH₵' + subtotal.toFixed(2);
        document.getElementById('summary-tax').textContent = 'GH₵' + tax.toFixed(2);
        document.getElementById('summary-total').textContent = 'GH₵' + total.toFixed(2);
    }
});