<?php
// header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle ?? 'MonProjet Minimaliste'; ?></title>
  <link rel="stylesheet" href="style.css">

  <!-- (Optionnel) Bootstrap CSS -->
  <!-- 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  -->
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <a class="navbar-brand" href="index.php">MonProjet</a>
      <ul class="nav">
        <li class="nav-item"><a class="nav-link" href="register.php">Inscription</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
        <li class="nav-item"><a class="nav-link" href="appointment.php">Rendez-vous</a></li>
      </ul>
      <!-- Bouton de toggle Dark Mode (icône lune/soleil) -->
      <button id="toggleDarkModeBtn" class="btn btn-outline-light">
        <!-- Icône provenant de Bootstrap Icons (optionnel) -->
        <i class="bi bi-moon-stars"></i>
      </button>
    </div>
  </nav>

  <div class="main-content">
