document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const msg = document.getElementById('loginMessage');
  const btn = document.getElementById('loginBtn');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';

    const formData = new FormData(form);
    const email = formData.get('email').trim();
    const password = formData.get('password');

    // Validation
    if (!emailRegex.test(email)) {
      msg.textContent = 'Enter a valid email';
      return;
    }
    if (!password) {
      msg.textContent = 'Password cannot be empty';
      return;
    }

    // Show loading
    btn.disabled = true;
    btn.classList.add("loading");
    btn.textContent = "Logging in...";

    try {
      const res = await fetch('../actions/login_customer_action.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();

      if (data.status === 'success') {
    window.location.href = '../index.php'; 
    } else {
    msg.textContent = data.message || 'Login failed';
    }


    } catch (err) {
      console.error(err);
      msg.textContent = 'Network/server error. Try again.';
    } finally {
      btn.disabled = false;
      btn.classList.remove("loading");
      btn.textContent = "Login";
    }
  });
});
