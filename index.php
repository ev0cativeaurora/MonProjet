<?php
// index.php - Version améliorée
require_once __DIR__ . '/config.php';

$pageTitle = "Accueil";
require_once __DIR__ . '/header.php';
?>

<!-- Hero Section -->
<div class="hero-section">
  <div class="container">
    <h1>Réservez vos rendez-vous en toute simplicité !</h1>
    <p>Notre plateforme intuitive vous permet de prendre et de gérer vos rendez-vous où que vous soyez, quand vous le souhaitez.</p>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="mt-4">
        <a href="register.php" class="btn btn-primary btn-lg">
          <i class="bi bi-person-plus"></i> Créer un compte
        </a>
        <a href="login.php" class="btn btn-outline ms-3">
          <i class="bi bi-box-arrow-in-right"></i> Se connecter
        </a>
      </div>
    <?php else: ?>
      <div class="mt-4">
        <a href="appointment.php" class="btn btn-primary btn-lg">
          <i class="bi bi-calendar-plus"></i> Prendre rendez-vous
        </a>
        <a href="profile.php" class="btn btn-outline ms-3">
          <i class="bi bi-person-circle"></i> Mon profil
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Features Section -->
<div class="container py-5">
  <h2 class="text-center mb-5">Pourquoi choisir notre service ?</h2>
  
  <div class="feature-boxes">
    <div class="feature-box">
      <i class="bi bi-calendar-check"></i>
      <h3>Réservation Facile</h3>
      <p>Réservez rapidement votre créneau horaire en quelques clics seulement.</p>
    </div>
    
    <div class="feature-box">
      <i class="bi bi-gear"></i>
      <h3>Gestion Intuitive</h3>
      <p>Gérez facilement vos rendez-vous en ligne. Modifiez ou annulez à tout moment.</p>
    </div>
    
    <div class="feature-box">
      <i class="bi bi-shield-check"></i>
      <h3>Sécurité Optimale</h3>
      <p>Profitez d'une interface sécurisée pour protéger vos données personnelles.</p>
    </div>
  </div>
</div>

<!-- How It Works Section -->
<div class="bg-light py-5">
  <div class="container">
    <h2 class="text-center mb-5">Comment ça marche ?</h2>
    
    <div class="row">
      <div class="col-md-4 text-center mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="icon-holder mx-auto mb-3">
              <i class="bi bi-person-plus-fill"></i>
            </div>
            <h3 class="h4">1. Créez votre compte</h3>
            <p>Inscrivez-vous en quelques étapes simples pour accéder à notre plateforme.</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 text-center mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="icon-holder mx-auto mb-3">
              <i class="bi bi-calendar-plus"></i>
            </div>
            <h3 class="h4">2. Choisissez un créneau</h3>
            <p>Sélectionnez la date et l'heure qui vous conviennent dans notre calendrier interactif.</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 text-center mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="icon-holder mx-auto mb-3">
              <i class="bi bi-check-circle"></i>
            </div>
            <h3 class="h4">3. Confirmez votre réservation</h3>
            <p>Recevez une confirmation par email et gérez vos rendez-vous depuis votre espace personnel.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Testimonials Section -->
<div class="container py-5">
  <h2 class="text-center mb-5">Ce que disent nos utilisateurs</h2>
  
  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-holder">
              <i class="bi bi-person-circle"></i>
            </div>
            <div>
              <h4 class="h5 mb-0">Marie L.</h4>
              <div class="text-primary">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
              </div>
            </div>
          </div>
          <p class="card-text">"Très pratique ! Je peux prendre rendez-vous à tout moment, plus besoin d'appeler pendant les heures d'ouverture."</p>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-holder">
              <i class="bi bi-person-circle"></i>
            </div>
            <div>
              <h4 class="h5 mb-0">Thomas R.</h4>
              <div class="text-primary">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star"></i>
              </div>
            </div>
          </div>
          <p class="card-text">"Interface intuitive et rapide. J'apprécie particulièrement les rappels par email avant chaque rendez-vous."</p>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-holder">
              <i class="bi bi-person-circle"></i>
            </div>
            <div>
              <h4 class="h5 mb-0">Sophie M.</h4>
              <div class="text-primary">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
              </div>
            </div>
          </div>
          <p class="card-text">"Le système est vraiment fiable. J'utilise cette plateforme depuis plusieurs mois et je n'ai jamais eu de problème."</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Call to Action -->
<div class="bg-primary text-white py-5">
  <div class="container text-center">
    <h2 class="mb-3">Prêt à simplifier la gestion de vos rendez-vous ?</h2>
    <p class="mb-4">Rejoignez notre plateforme dès aujourd'hui et profitez d'une gestion simple et efficace.</p>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="btn btn-lg btn-outline">
        <i class="bi bi-person-plus"></i> Créer un compte gratuit
      </a>
    <?php else: ?>
      <a href="appointment.php" class="btn btn-lg btn-outline">
        <i class="bi bi-calendar-plus"></i> Prendre rendez-vous maintenant
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Contact Quick Info -->
<div class="container py-4">
  <div class="row">
    <div class="col-md-4 text-center mb-3">
      <i class="bi bi-geo-alt text-primary" style="font-size: 2rem;"></i>
      <h4>Adresse</h4>
      <p>123 Rue des Exemples, 75000 Paris</p>
    </div>
    
    <div class="col-md-4 text-center mb-3">
      <i class="bi bi-telephone text-primary" style="font-size: 2rem;"></i>
      <h4>Téléphone</h4>
      <p>+33 1 23 45 67 89</p>
    </div>
    
    <div class="col-md-4 text-center mb-3">
      <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
      <h4>Email</h4>
      <p>contact@monprojet.fr</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>