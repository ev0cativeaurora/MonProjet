<?php
require_once __DIR__ . '/config.php'; // Chargement de la config
// session_start() si pas déjà dans config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];

// Vérifier si le token correspond à un compte non vérifié
$stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND verified = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Token invalide ou compte déjà vérifié.");
}

// Activer le compte (verified = 1)
$stmtUpdate = $conn->prepare("UPDATE users SET verified = 1, verification_token = '' WHERE id = ?");
$stmtUpdate->bind_param("i", $user['id']);
if ($stmtUpdate->execute()) {
    // Connecter automatiquement l’utilisateur
    $_SESSION['user_id'] = $user['id'];

    // Stocker un "flash message" : il sera affiché sur la page de profil
    $_SESSION['flash_message'] = "Votre compte a été activé avec succès !";

    // Redirection immédiate vers le profil
    header("Location: profile.php");
    exit;
} else {
    echo "Erreur lors de la vérification du compte.";
}
