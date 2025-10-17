document.addEventListener('DOMContentLoaded', () => {
  const toggleButton = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.primary-navigation');

  if (!toggleButton || !navigation) {
    return;
  }

  const screenReaderText = toggleButton.querySelector('.screen-reader-text');
  const openLabel = toggleButton.dataset.openLabel || 'Ouvrir le menu';
  const closeLabel = toggleButton.dataset.closeLabel || 'Fermer le menu';
  const mobileQuery = window.matchMedia('(max-width: 768px)');

  const closeMenu = () => {
    document.body.classList.remove('menu-open');
    toggleButton.classList.remove('is-active');
    toggleButton.setAttribute('aria-expanded', 'false');

    if (screenReaderText) {
      screenReaderText.textContent = openLabel;
    }

    if (mobileQuery.matches) {
      navigation.setAttribute('aria-hidden', 'true');
    } else {
      navigation.removeAttribute('aria-hidden');
    }
  };

  const openMenu = () => {
    document.body.classList.add('menu-open');
    toggleButton.classList.add('is-active');
    toggleButton.setAttribute('aria-expanded', 'true');

    if (screenReaderText) {
      screenReaderText.textContent = closeLabel;
    }

    navigation.setAttribute('aria-hidden', 'false');
  };

  const toggleMenu = () => {
    if (!mobileQuery.matches) {
      return;
    }

    const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';

    if (isExpanded) {
      closeMenu();
    } else {
      openMenu();
    }
  };

  const handleViewportChange = () => {
    if (mobileQuery.matches) {
      closeMenu();
    } else {
      document.body.classList.remove('menu-open');
      toggleButton.classList.remove('is-active');
      toggleButton.setAttribute('aria-expanded', 'false');
      navigation.removeAttribute('aria-hidden');

      if (screenReaderText) {
        screenReaderText.textContent = openLabel;
      }
    }
  };

  toggleButton.addEventListener('click', toggleMenu);

  navigation.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keyup', (event) => {
    if (event.key === 'Escape') {
      closeMenu();
    }
  });

  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', handleViewportChange);
  } else if (typeof mobileQuery.addListener === 'function') {
    mobileQuery.addListener(handleViewportChange);
  }
  handleViewportChange();
});
