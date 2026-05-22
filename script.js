// Theme Management Engine
(function() {
  const getThemePreference = () => {
    try {
      const saved = localStorage.getItem('bca-theme');
      if (saved) return saved;
    } catch (e) {
      console.warn('LocalStorage is not accessible:', e);
    }
    try {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    } catch (e) {
      return 'light';
    }
  };

  const setTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.setAttribute('data-bs-theme', theme);
    try {
      localStorage.setItem('bca-theme', theme);
    } catch (e) {
      console.warn('LocalStorage is not writable:', e);
    }
    updateToggleIcons(theme);
  };

  const updateToggleIcons = (theme) => {
    const buttons = document.querySelectorAll('.theme-toggle-btn');
    buttons.forEach(btn => {
      const icon = btn.querySelector('i');
      if (icon) {
        if (theme === 'dark') {
          icon.className = 'bi bi-sun-fill';
        } else {
          icon.className = 'bi bi-moon-stars-fill';
        }
      }
    });
  };

  // Initialize Theme immediately to prevent layout flashes
  const initialTheme = getThemePreference();
  document.documentElement.setAttribute('data-theme', initialTheme);
  document.documentElement.setAttribute('data-bs-theme', initialTheme);

  // Setup DOM Event Listeners when content is ready
  document.addEventListener('DOMContentLoaded', () => {
    // Initial icon sync
    updateToggleIcons(initialTheme);

    // Setup toggle buttons
    document.body.addEventListener('click', (e) => {
      const btn = e.target.closest('.theme-toggle-btn');
      if (btn) {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
      }
    });

    // Auto-hide alerts after 4 seconds with a smooth fade-out if possible
    setTimeout(() => {
      document.querySelectorAll('.alert').forEach(alert => {
        try {
          const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
          if (bsAlert) {
            bsAlert.close();
          }
        } catch (e) {
          // Fallback if bootstrap object is not ready
          alert.style.transition = 'opacity 0.5s ease';
          alert.style.opacity = '0';
          setTimeout(() => alert.remove(), 500);
        }
      });
    }, 4000);
  });
})();
