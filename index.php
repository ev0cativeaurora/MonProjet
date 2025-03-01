<?php
// index.php
$pageTitle = "Accueil";
require_once __DIR__ . '/header.php';
?>

<div class="hero-section">
  <h1>Bienvenue sur MonProjet</h1>
  <p>Réservez vos rendez-vous en toute simplicité !</p>
  <a href="register.php" class="btn btn-cool">Créer un compte</a>
</div>

<div class="feature-boxes">
  <div class="feature-box">
    <h3>Fonctionnalité 1</h3>
    <p>Réservez rapidement votre créneau horaire.</p>
  </div>
  <div class="feature-box">
    <h3>Fonctionnalité 2</h3>
    <p>Gérez facilement vos rendez-vous en ligne.</p>
  </div>
  <div class="feature-box">
    <h3>Fonctionnalité 3</h3>
    <p>Profitez d’une interface intuitive et sécurisée.</p>
  </div>
</div>

<?php
require_once __DIR__ . '/footer.php';
