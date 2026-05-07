document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.reveal-btn');
  const cards = document.querySelectorAll('.left-cards .profile-card');

  function showCard(id) {
    cards.forEach((c) => {
      if (c.id === id) {
        c.classList.remove('hidden');
        c.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        c.classList.add('hidden');
      }
    });
  }

  buttons.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const target = btn.getAttribute('data-target');
      if (target) showCard(target);
    });
  });

  // initialize — ensure first card visible
  showCard('card-1');
});
