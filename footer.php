</div> <!-- Fin .main-content -->

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>MonProjet</h3>
          <p>Système de réservation en ligne avec calendrier intégré. Prenez rendez-vous facilement et gérez vos réservations.</p>
        </div>
        
        <div class="footer-section">
          <h3>Liens rapides</h3>
          <ul class="footer-links">
            <li><a href="index.php"><i class="bi bi-house"></i> Accueil</a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
              <li><a href="register.php"><i class="bi bi-person-plus"></i> Inscription</a></li>
              <li><a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Connexion</a></li>
            <?php else: ?>
              <li><a href="profile.php"><i class="bi bi-person-circle"></i> Mon Profil</a></li>
              <li><a href="appointment.php"><i class="bi bi-calendar-plus"></i> Prendre RDV</a></li>
            <?php endif; ?>
            <li><a href="contact.php"><i class="bi bi-envelope"></i> Contact</a></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Contact</h3>
          <p><i class="bi bi-geo-alt"></i> 123 Rue des Exemples, 75000 Paris</p>
          <p><i class="bi bi-telephone"></i> +33 1 23 45 67 89</p>
          <p><i class="bi bi-envelope"></i> contact@monprojet.fr</p>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> - MonProjet. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <!-- Script JS principal -->
  <script src="script.js"></script>
</body>
</html>