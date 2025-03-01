<?php
// login.php

$pageTitle = "Connexion";
require_once __DIR__ . '/header.php';

// Traitement (simplifié)
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Exemple de pseudo-vérification
    // $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    // $stmt->bind_param("s", $email);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // $user   = $result->fetch_assoc();

    // if ($user && password_verify($password, $user['password'])) {
    //     // Connexion réussie
    //     $_SESSION['user_id'] = $user['id'];
    //     header("Location: profile.php");
    //     exit;
    // } else {
    //     $error = "Identifiants incorrects.";
    // }

    // Pour la démonstration, on simule un échec
    $error = "Identifiants incorrects (exemple).";
}
?>

<div class="auth-container">
  <h2>Connexion</h2>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>

    <label>Mot de passe</label>
    <input type="password" name="password" class="form-control" required>

    <button type="submit" class="btn btn-cool">Se connecter</button>
  </form>
</div>

<?php
require_once __DIR__ . '/footer.php';
