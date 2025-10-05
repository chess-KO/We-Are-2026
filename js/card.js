  document.addEventListener('DOMContentLoaded', () => {
    const flipButtons = document.querySelectorAll('.flip-btn');
    flipButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const card = btn.closest('.card-flip');
        card.classList.toggle('flipped');
      });
    });
  });