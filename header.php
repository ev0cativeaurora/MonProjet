<?php
// header.php - Version améliorée avec correction pour le footer
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'Système de Réservation'; ?></title>
  
  <!-- CSS principal -->
  <link rel="stylesheet" href="style.css">
  
  <!-- Intégration des icônes Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  
  <!-- Meta tags pour SEO et partage social -->
  <meta name="description" content="Système de réservation en ligne avec calendrier intégré. Prenez rendez-vous facilement et gérez vos réservations.">
  <meta name="keywords" content="réservation, rendez-vous, calendrier, planning">
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="container">
      <!-- Logo et nom du site -->
      <a class="navbar-brand" href="index.php">
        <i class="bi bi-calendar-check"></i>
        <span>MonProjet</span>
      </a>
      
      <!-- Bouton menu hamburger pour mobile -->
      <button id="toggleMenu" class="navbar-toggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <!-- Menu de navigation -->
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="bi bi-house"></i> Accueil
          </a>
        </li>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
          <!-- Liens pour visiteurs -->
          <li class="nav-item">
            <a class="nav-link" href="register.php">
              <i class="bi bi-person-plus"></i> Inscription
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">
              <i class="bi bi-box-arrow-in-right"></i> Connexion
            </a>
          </li>
        <?php else: ?>
          <!-- Liens pour utilisateurs connectés -->
          <li class="nav-item">
            <a class="nav-link" href="appointment.php">
              <i class="bi bi-calendar-plus"></i> Prendre RDV
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="cancel_appointment.php">
              <i class="bi bi-calendar-x"></i> Annuler RDV
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="profile.php">
              <i class="bi bi-person-circle"></i> Mon Profil
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
          </li>
        <?php endif; ?>
        
        <!-- Formulaire de contact toujours visible -->
        <li class="nav-item">
          <a class="nav-link" href="contact.php">
            <i class="bi bi-envelope"></i> Contact
          </a>
        </li>
      </ul>
      
      <!-- Bouton Dark Mode -->
      <button id="toggleDarkMode" class="btn btn-outline-light">
        <i class="bi bi-moon-stars"></i>
      </button>
    </div>
  </nav>

  <!-- Conteneur principal -->
  <div class="main-content">
    <!-- Afficher les messages flash s'ils existent -->
    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> <?php echo e($_SESSION['flash_message']); ?>
        <?php unset($_SESSION['flash_message']); ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> <?php echo e($_SESSION['flash_error']); ?>
        <?php unset($_SESSION['flash_error']); ?>
      </div>
    <?php endif; ?>