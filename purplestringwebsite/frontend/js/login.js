document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('login-form');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');

  // Handle form submission
  loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = emailInput.value.trim();
    const password = passwordInput.value;

    // Basic validation
    if (!email || !password) {
      showMessage('Please fill in all fields', 'error');
      return;
    }

    if (!isValidEmail(email)) {
      showMessage('Please enter a valid email address', 'error');
      return;
    }

    if (password.length < 8) {
      showMessage('Password must be at least 6 characters long', 'error');
      return;
    }

    // Simulate login process
    const loginBtn = loginForm.querySelector('.login-btn');
    loginBtn.disabled = true;
    loginBtn.textContent = 'Signing in...';

    // Simulate API call
    setTimeout(() => {
      showMessage('Login successful! Redirecting...', 'success');
      
      // Reset form
      loginForm.reset();
      loginBtn.disabled = false;
      loginBtn.textContent = 'Sign In';
      
      // Redirect after 1.5 seconds
      setTimeout(() => {
        // NOTE: This is sample front-end role handling only.
        // Replace with a server-authenticated role check in production.
        const normalizedEmail = email.toLowerCase();
        const normalizedPassword = password;

        const isAdmin = normalizedEmail === 'admin@purple.com' && normalizedPassword === 'Admin123!';
        const isStaff = normalizedEmail === 'staff@purple.com' && normalizedPassword === 'Staff123!';
        const isClient = normalizedEmail === 'client@purple.com' && normalizedPassword === 'Client123!';

        if (isAdmin || isStaff) {
          window.location.href = './pages/admin-homepage.html';
        } else if (isClient) {
          window.location.href = './pages/homepage.html';
        } else {
          showMessage('Invalid credentials (use admin/staff/client sample accounts)', 'error');
          return;
        }
      }, 1500);
    }, 1500);
  });

  // Email input validation
  emailInput.addEventListener('blur', function() {
    if (this.value && !isValidEmail(this.value)) {
      this.style.borderColor = '#e74c3c';
    } else {
      this.style.borderColor = '#e8d4f1';
    }
  });

  // Password visibility toggle (optional)
  const passwordToggle = document.createElement('button');
  passwordToggle.type = 'button';
  passwordToggle.innerHTML = '👁️';
  passwordToggle.style.cssText = `
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
  `;

  // Forgot password link
  const forgotLink = document.querySelector('.forgot-password');
  forgotLink.addEventListener('click', function(e) {
    e.preventDefault();
    showMessage('Password reset link sent to your email', 'success');
  });

  // Social login buttons
  const googleBtn = document.querySelector('.google-btn');
  const facebookBtn = document.querySelector('.facebook-btn');

  googleBtn.addEventListener('click', function(e) {
    e.preventDefault();
    showMessage('Google login integration coming soon!', 'info');
  });

  facebookBtn.addEventListener('click', function(e) {
    e.preventDefault();
    showMessage('Facebook login integration coming soon!', 'info');
  });

  // Signup link
  const signupLink = document.querySelector('#signup-link a');
  signupLink.addEventListener('click', function(e) {
    e.preventDefault();
    // Redirect to signup page
    window.location.href = './pages/signup.html';
  });

  // Utility functions
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function showMessage(message, type) {
    // Remove existing message if present
    const existingMessage = document.querySelector('.login-message');
    if (existingMessage) {
      existingMessage.remove();
    }

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `login-message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
      padding: 12px 16px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      animation: slideDown 0.3s ease;
    `;

    if (type === 'error') {
      messageDiv.style.backgroundColor = '#fff5f5';
      messageDiv.style.color = '#c92a2a';
      messageDiv.style.borderLeft = '4px solid #c92a2a';
    } else if (type === 'success') {
      messageDiv.style.backgroundColor = '#f1fdf7';
      messageDiv.style.color = '#2b8a3e';
      messageDiv.style.borderLeft = '4px solid #2b8a3e';
    } else if (type === 'info') {
      messageDiv.style.backgroundColor = '#f0f5ff';
      messageDiv.style.color = '#1971c2';
      messageDiv.style.borderLeft = '4px solid #1971c2';
    }

    // Insert message before form
    const loginHeader = document.getElementById('login-header');
    loginHeader.parentNode.insertBefore(messageDiv, loginHeader.nextSibling);

    // Auto-remove message after 4 seconds
    setTimeout(() => {
      messageDiv.style.animation = 'slideUp 0.3s ease';
      setTimeout(() => messageDiv.remove(), 300);
    }, 4000);
  }
});

// CSS animation for messages
const style = document.createElement('style');
style.textContent = `
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideUp {
    from {
      opacity: 1;
      transform: translateY(0);
    }
    to {
      opacity: 0;
      transform: translateY(-10px);
    }
  }
`;
document.head.appendChild(style);
