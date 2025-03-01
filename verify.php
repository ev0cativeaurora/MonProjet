<?php
/**
 * verify.php - Activation du compte utilisateur
 */
require_once __DIR__ . '/config.php';

// Vérifier si le token est présent
if (!isset($_GET['token']) || empty($_GET['token'])) {
    // Rediriger vers la page de connexion avec un message d'erreur
    $_SESSION['flash_error'] = "Lien d'activation invalide ou expiré.";
    header("Location: login.php");
    exit;
}

$token = trim($_GET['token']);

// Vérifier si le token existe dans la base de données
$stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND verified = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Token invalide ou déjà utilisé
    $_SESSION['flash_error'] = "Ce lien d'activation est invalide ou a déjà été utilisé.";
    header("Location: login.php");
    exit;
}

// Récupérer l'ID de l'utilisateur
$user = $result->fetch_assoc();
$userId = $user['id'];

// Activer le compte
$updateStmt = $conn->prepare("UPDATE users SET verified = 1, verification_token = '' WHERE id = ?");
$updateStmt->bind_param("i", $userId);

if ($updateStmt->execute()) {
    // Connecter automatiquement l'utilisateur
    $_SESSION['user_id'] = $userId;
    $_SESSION['flash_message'] = "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
    
    // Rediriger vers la page de profil
    header("Location: profile.php");
    exit;
} else {
    // Erreur lors de l'activation
    $_SESSION['flash_error'] = "Une erreur s'est produite lors de l'activation de votre compte. Veuillez réessayer.";
    header("Location: login.php");
    exit;
}