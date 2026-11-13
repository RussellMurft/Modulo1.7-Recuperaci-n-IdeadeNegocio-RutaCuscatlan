(() => {
  'use strict';
  const button = document.querySelector('.rc-menu-toggle');
  const nav = document.querySelector('#rc-primary-nav');
  if (!button || !nav) return;

  button.addEventListener('click', () => {
    const expanded = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', String(!expanded));
    nav.classList.toggle('is-open', !expanded);
    document.body.classList.toggle('rc-menu-open', !expanded);
  });

  nav.addEventListener('click', (event) => {
    if (event.target.closest('a') && window.matchMedia('(max-width: 900px)').matches) {
      button.setAttribute('aria-expanded', 'false');
      nav.classList.remove('is-open');
      document.body.classList.remove('rc-menu-open');
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && nav.classList.contains('is-open')) {
      button.setAttribute('aria-expanded', 'false');
      nav.classList.remove('is-open');
      document.body.classList.remove('rc-menu-open');
      button.focus();
    }
  });
})();
