// script.js - Version améliorée avec correction pour le footer

document.addEventListener('DOMContentLoaded', function() {
    // ========== Fonctionnalités de navigation ==========
    
    // Gestion du menu mobile avec hamburger
    const toggleBtn = document.getElementById('toggleMenu');
    const navMenu = document.querySelector('.nav');
    
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        toggleBtn.classList.toggle('active');
        navMenu.classList.toggle('show');
      });
    }
    
    // Fermer le menu mobile si on clique ailleurs
    document.addEventListener('click', function(e) {
      if (navMenu && navMenu.classList.contains('show') && 
          !navMenu.contains(e.target) && 
          e.target !== toggleBtn) {
        navMenu.classList.remove('show');
        if (toggleBtn) toggleBtn.classList.remove('active');
      }
    });
    
    // Gérer la classe active dans la navigation
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
      // Extraire le chemin de l'URL du lien
      const linkPath = new URL(link.href, window.location.origin).pathname;
      
      // Si le chemin actuel correspond au chemin du lien, ajouter la classe active
      if (currentLocation === linkPath || 
          (linkPath !== '/' && currentLocation.includes(linkPath))) {
        link.classList.add('active');
      }
    });
    
    // ========== Dark Mode ==========
    
    const toggleDarkModeBtn = document.getElementById('toggleDarkMode');
    const icon = toggleDarkModeBtn ? toggleDarkModeBtn.querySelector('i') : null;
    
    // Récupérer la préférence utilisateur si stockée
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    // Vérifier si l'utilisateur a une préférence système pour le mode sombre
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Appliquer le mode sombre si préférence stockée ou préférence système
    if (isDarkMode || (prefersDarkScheme.matches && localStorage.getItem('darkMode') === null)) {
      document.body.classList.add('dark-mode');
      if (icon) {
        icon.classList.remove('bi-moon-stars');
        icon.classList.add('bi-sun');
      }
    }
  
    // Gérer le clic sur le bouton de basculement mode sombre/clair
    if (toggleDarkModeBtn) {
      toggleDarkModeBtn.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const darkActive = document.body.classList.contains('dark-mode');
        
        localStorage.setItem('darkMode', darkActive.toString());
        
        if (icon) {
          if (darkActive) {
            icon.classList.remove('bi-moon-stars');
            icon.classList.add('bi-sun');
          } else {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon-stars');
          }
        }
      });
    }
    
    // ========== Animation des boîtes de fonctionnalités ==========
    
    const featureBoxes = document.querySelectorAll('.feature-box');
    
    // Fonction pour vérifier si un élément est dans la viewport
    function isInViewport(element) {
      const rect = element.getBoundingClientRect();
      return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.bottom >= 0
      );
    }
    
    // Fonction pour animer les éléments visibles
    function animateOnScroll() {
      featureBoxes.forEach((box, index) => {
        if (isInViewport(box) && !box.classList.contains('fade-up')) {
          // Ajouter un délai progressif pour chaque boîte
          setTimeout(() => {
            box.classList.add('fade-up');
          }, index * 150);
        }
      });
    }
    
    // Lancer l'animation au chargement et au scroll
    if (featureBoxes.length > 0) {
      animateOnScroll();
      window.addEventListener('scroll', animateOnScroll);
    }
    
    // ========== Gestion des formulaires ==========
    
    // Afficher/masquer le mot de passe
    const passwordToggles = document.querySelectorAll('.password-toggle-btn');
    
    passwordToggles.forEach(toggle => {
      toggle.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Changer l'icône
        const icon = this.querySelector('i');
        if (icon) {
          if (type === 'text') {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
          } else {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
          }
        }
      });
    });
    
    // Validation des formulaires côté client
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
      form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Valider les champs obligatoires
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
            
            // Créer un message d'erreur s'il n'existe pas déjà
            let errorMessage = field.nextElementSibling;
            if (!errorMessage || !errorMessage.classList.contains('error-message')) {
              errorMessage = document.createElement('div');
              errorMessage.className = 'error-message text-danger';
              errorMessage.textContent = 'Ce champ est obligatoire';
              field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }
          } else {
            field.classList.remove('is-invalid');
            const errorMessage = field.nextElementSibling;
            if (errorMessage && errorMessage.classList.contains('error-message')) {
              errorMessage.remove();
            }
          }
        });
        
        // Valider l'email
        const emailFields = form.querySelectorAll('input[type="email"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        emailFields.forEach(field => {
          if (field.value.trim() && !emailRegex.test(field.value.trim())) {
            isValid = false;
            field.classList.add('is-invalid');
            
            let errorMessage = field.nextElementSibling;
            if (!errorMessage || !errorMessage.classList.contains('error-message')) {
              errorMessage = document.createElement('div');
              errorMessage.className = 'error-message text-danger';
              errorMessage.textContent = 'Veuillez entrer une adresse email valide';
              field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }
          }
        });
        
        // Valider le téléphone (format français)
        const phoneFields = form.querySelectorAll('input[type="tel"]');
        const phoneRegex = /^(\+33|0)[1-9](\d{2}){4}$/;
        
        phoneFields.forEach(field => {
          if (field.value.trim() && !phoneRegex.test(field.value.replace(/\s/g, ''))) {
            isValid = false;
            field.classList.add('is-invalid');
            
            let errorMessage = field.nextElementSibling;
            if (!errorMessage || !errorMessage.classList.contains('error-message')) {
              errorMessage = document.createElement('div');
              errorMessage.className = 'error-message text-danger';
              errorMessage.textContent = 'Veuillez entrer un numéro de téléphone valide';
              field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }
          }
        });
        
        // Validation des mots de passe
        const passwordField = form.querySelector('input[name="password"]');
        const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
        
        if (passwordField && confirmPasswordField) {
          if (passwordField.value !== confirmPasswordField.value) {
            isValid = false;
            confirmPasswordField.classList.add('is-invalid');
            
            let errorMessage = confirmPasswordField.nextElementSibling;
            if (!errorMessage || !errorMessage.classList.contains('error-message')) {
              errorMessage = document.createElement('div');
              errorMessage.className = 'error-message text-danger';
              errorMessage.textContent = 'Les mots de passe ne correspondent pas';
              confirmPasswordField.parentNode.insertBefore(errorMessage, confirmPasswordField.nextSibling);
            }
          }
        }
        
        if (!isValid) {
          e.preventDefault();
        }
      });
    });
    
    // Supprimer les messages d'erreur lors de la saisie
    document.addEventListener('input', function(e) {
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
        e.target.classList.remove('is-invalid');
        const errorMessage = e.target.nextElementSibling;
        if (errorMessage && errorMessage.classList.contains('error-message')) {
          errorMessage.remove();
        }
      }
    });
    
    // ========== Gestion du calendrier ==========
    
    // Fonctionnalité de calendrier pour la prise de rendez-vous
    const calendarContainer = document.querySelector('.calendar-container');
    if (calendarContainer) {
      let currentDate = new Date();
      let selectedDate = null;
      
      // Fonction pour générer le calendrier
      function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay(); // 0 = Dimanche, 1 = Lundi, etc.
        
        // Adapter pour commencer par Lundi
        const startingDayAdjusted = startingDay === 0 ? 6 : startingDay - 1;
        
        // Mettre à jour l'en-tête du calendrier
        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        document.querySelector('.calendar-month').textContent = `${monthNames[month]} ${year}`;
        
        // Générer les jours
        const daysContainer = document.querySelector('.calendar-days');
        daysContainer.innerHTML = '';
        
        // Ajouter les espaces vides pour les jours avant le premier du mois
        for (let i = 0; i < startingDayAdjusted; i++) {
          const emptyDay = document.createElement('div');
          emptyDay.className = 'calendar-day disabled';
          daysContainer.appendChild(emptyDay);
        }
        
        // Ajouter les jours du mois
        const today = new Date();
        for (let i = 1; i <= daysInMonth; i++) {
          const dayElement = document.createElement('div');
          dayElement.className = 'calendar-day';
          dayElement.textContent = i;
          
          const dateToCheck = new Date(year, month, i);
          
          // Vérifier si c'est aujourd'hui
          if (dateToCheck.getDate() === today.getDate() && 
              dateToCheck.getMonth() === today.getMonth() && 
              dateToCheck.getFullYear() === today.getFullYear()) {
            dayElement.classList.add('today');
          }
          
          // Vérifier si c'est le jour sélectionné
          if (selectedDate && 
              dateToCheck.getDate() === selectedDate.getDate() && 
              dateToCheck.getMonth() === selectedDate.getMonth() && 
              dateToCheck.getFullYear() === selectedDate.getFullYear()) {
            dayElement.classList.add('selected');
          }
          
          // Désactiver les jours passés
          if (dateToCheck < new Date(today.setHours(0, 0, 0, 0))) {
            dayElement.classList.add('disabled');
          } else {
            // Ajouter l'événement click pour les jours disponibles
            dayElement.addEventListener('click', function() {
              // Retirer la classe selected de tous les jours
              document.querySelectorAll('.calendar-day').forEach(day => {
                day.classList.remove('selected');
              });
              
              // Ajouter la classe selected à ce jour
              this.classList.add('selected');
              
              // Mettre à jour la date sélectionnée
              selectedDate = new Date(year, month, i);
              
              // Mettre à jour le champ de date du formulaire s'il existe
              const dateInput = document.querySelector('input[name="date_rdv"]');
              if (dateInput) {
                const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                dateInput.value = formattedDate;
              }
            });
          }
          
          daysContainer.appendChild(dayElement);
        }
      }
      
      // Initialiser le calendrier
      generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
      
      // Navigation mois précédent/suivant
      document.querySelector('.prev-month').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
      });
      
      document.querySelector('.next-month').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
      });
    }
    
    // ========== Feedback utilisateur ==========
    
    // Faire disparaître automatiquement les messages d'alerte après un délai
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
          alert.remove();
        }, 300);
      }, 5000);
    });
    
    // Ajouter des transitions pour les éléments qui apparaissent/disparaissent
    document.querySelectorAll('.alert, .form-container, .card').forEach(element => {
      element.style.transition = 'opacity 0.3s, transform 0.3s';
    });
    
    // Correction du problème de chevauchement du footer et du menu utilisateur
    // Cette fonction s'assure que le contenu n'est pas caché par le footer
    function adjustContentPadding() {
      const footer = document.querySelector('.footer');
      const mainContent = document.querySelector('.main-content');
      
      if (footer && mainContent) {
        const footerHeight = footer.offsetHeight;
        mainContent.style.paddingBottom = (footerHeight + 40) + 'px';
      }
    }
    
    // Exécuter cette fonction au chargement et au redimensionnement
    window.addEventListener('load', adjustContentPadding);
    window.addEventListener('resize', adjustContentPadding);
  });