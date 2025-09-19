// js/register.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');
  const msg = document.getElementById('registerMessage');
  const btn = document.getElementById('registerBtn');
  const emailInput = document.getElementById('email');
  const emailStatus = document.getElementById('emailStatus');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const phoneRegex = /^[0-9+\-\s()]{6,15}$/;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';

    const formData = new FormData(form);
    const email = formData.get('email').trim();
    const password = formData.get('password');
    const phone = formData.get('contact_number');

    // Validation
    if (!emailRegex.test(email)) {
      msg.textContent = 'Enter a valid email';
      return;
    }
    if (!password || password.length < 6) {
      msg.textContent = 'Password must be at least 6 characters';
      return;
    }
    if (!phoneRegex.test(phone)) {
      msg.textContent = 'Enter a valid contact number';
      return;
    }

    // Show loading state
    btn.disabled = true;
    btn.classList.add("loading");
    btn.textContent = "Registering...";

    try {
      const res = await fetch('../actions/register_customer_action.php', {
      method: 'POST',
      body: formData
      });

      const data = await res.json();

      if (data.status === 'success') {
        window.location.href = 'login.php';
      } else {
        msg.textContent = data.message || 'Registration failed';
      }
    } catch (err) {
      console.error(err);
      msg.textContent = 'Network/server error. Try again.';
    } finally {
      // Reset button
      btn.disabled = false;
      btn.classList.remove("loading");
      btn.textContent = "Register";
    }
  });

  // Email availability check
  emailInput && emailInput.addEventListener('blur', async () => {
    const email = emailInput.value.trim();
    if (!emailRegex.test(email)) return;
    try {
      const fd = new FormData();
      fd.append('email', email);
      const r = await fetch('../actions/check_email_action.php', { method: 'POST', body: fd });
      const json = await r.json();
      if (json.available === false) {
        emailStatus.textContent = 'Email already in use';
      } else {
        emailStatus.textContent = '';
      }
    } catch (e) { /* ignore */ }
  });
});
