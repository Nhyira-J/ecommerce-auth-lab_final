document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const messageDiv = document.getElementById("loginMessage");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      const response = await fetch("../actions/login_customer_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();
      messageDiv.textContent = data.message;

      if (data.success) {
        window.location.href = "../index.php";
      }
    } catch (err) {
      console.error("Login error:", err);
      messageDiv.textContent = "Network/server error.";
    }
  });
});
