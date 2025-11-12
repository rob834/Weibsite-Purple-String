// Product page script (fixed): safely handle "Add to Cart" buttons and show messages.
document.addEventListener('DOMContentLoaded', () => {
  const cartBtns = document.querySelectorAll('.cart-btn');

  if (!cartBtns || cartBtns.length === 0) {
    // No cart buttons on this page — nothing to do.
    return;
  }

  cartBtns.forEach((cartBtn) => {
    if (!cartBtn) return;
    cartBtn.addEventListener('click', function (e) {
      e.preventDefault();
      showMessage('Added to cart!', 'info', cartBtn);
    });
  });
});

function showMessage(message, type = 'info', anchorElement = null) {
  // Remove existing message if present
  const existingMessage =
    document.querySelector('.product-message') ||
    document.querySelector('.login-message');
  if (existingMessage) existingMessage.remove();

  // Create message element
  const messageDiv = document.createElement('div');
  messageDiv.className = `product-message ${type}`;
  messageDiv.textContent = message;
  messageDiv.style.cssText = `
    padding: 12px 16px;
    margin-top: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    animation: slideDown 0.3s ease;
    box-sizing: border-box;
  `;
  messageDiv.style.backgroundColor = '#f0f5ff';
  messageDiv.style.color = '#c071d0';
  messageDiv.style.borderLeft = '4px solid #c071d0';
  messageDiv.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.4)';


  // Choose where to insert the message. Prefer related product area, fall back to main or body.
    if (anchorElement && anchorElement.parentElement) {
    anchorElement.parentElement.insertBefore(messageDiv, anchorElement.nextSibling);
    } else {
    const mainContent = document.querySelector('main') || document.body;
    mainContent.insertBefore(messageDiv, mainContent.firstChild);
    }

  // Auto-remove message after 4 seconds
  setTimeout(() => {
    messageDiv.style.animation = 'slideUp 0.3s ease';
    setTimeout(() => messageDiv.remove(), 300);
  }, 4000);
}

// CSS animation for messages (inject only if not already present)
if (!document.getElementById('product-message-animations')) {
  const style = document.createElement('style');
  style.id = 'product-message-animations';
  style.textContent = `
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      from { opacity: 1; transform: translateY(0); }
      to { opacity: 0; transform: translateY(-10px); }
    }
    .product-message { z-index: 9999; }
  `;
  document.head.appendChild(style);
}
