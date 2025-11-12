document.addEventListener('DOMContentLoaded', () => {
    const paymentBtn = document.getElementById('payment-btn');
    const paymentModal = document.getElementById('payment-modal');
    const successModal = document.getElementById('success-modal');
    const confirmPaymentBtn = document.getElementById('confirm-payment');
    const cancelPaymentBtn = document.getElementById('cancel-payment');

    // Show payment modal
    paymentBtn.addEventListener('click', () => {
        paymentModal.style.display = 'block';
    });

    // Cancel payment
    cancelPaymentBtn.addEventListener('click', () => {
        paymentModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === paymentModal) {
            paymentModal.style.display = 'none';
        }
    });

    // Confirm payment and process checkout
    confirmPaymentBtn.addEventListener('click', async () => {
        // Disable button to prevent double-click
        confirmPaymentBtn.disabled = true;
        confirmPaymentBtn.textContent = 'Processing...';

        try {
            const response = await fetch('../actions/process_checkout_action.php', {
                method: 'POST'
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Hide payment modal
                paymentModal.style.display = 'none';

                // Show success modal with order details
                document.getElementById('order-reference').textContent = data.invoice_no;
                document.getElementById('total-paid').textContent = 'GH₵' + data.total;
                successModal.style.display = 'block';
            } else {
                alert(data.message || 'Checkout failed');
                confirmPaymentBtn.disabled = false;
                confirmPaymentBtn.textContent = 'Yes, I\'ve Paid';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error occurred');
            confirmPaymentBtn.disabled = false;
            confirmPaymentBtn.textContent = 'Yes, I\'ve Paid';
        }
    });
});