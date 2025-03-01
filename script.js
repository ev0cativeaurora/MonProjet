// script.js

document.addEventListener('DOMContentLoaded', function() {
    const toggleDarkModeBtn = document.getElementById('toggleDarkModeBtn');
    const icon = toggleDarkModeBtn ? toggleDarkModeBtn.querySelector('i') : null;
    
    // Récupérer la préférence "dark mode" si stockée
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
      document.body.classList.add('dark-mode');
      if (icon) {
        icon.classList.remove('bi-moon-stars');
        icon.classList.add('bi-sun');
      }
    }
  
    // Au clic, on toggle la classe "dark-mode"
    if (toggleDarkModeBtn) {
      toggleDarkModeBtn.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const darkActive = document.body.classList.contains('dark-mode');
  
        if (darkActive) {
          localStorage.setItem('darkMode', 'true');
          if (icon) {
            icon.classList.remove('bi-moon-stars');
            icon.classList.add('bi-sun');
          }
        } else {
          localStorage.setItem('darkMode', 'false');
          if (icon) {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon-stars');
          }
        }
      });
    }
  
    // Petite animation sur .feature-box
    const featureBoxes = document.querySelectorAll('.feature-box');
    featureBoxes.forEach((box, i) => {
      box.style.opacity = '0';
      box.style.transform = 'translateY(20px)';
      setTimeout(() => {
        box.style.transition = 'all 0.5s';
        box.style.opacity = '1';
        box.style.transform = 'translateY(0)';
      }, i * 200);
    });
  });
  