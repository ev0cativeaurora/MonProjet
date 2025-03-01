<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$csrfToken = generateCsrfToken();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    // Supprimer les rendez-vous
    $stmt = $conn->prepare("DELETE FROM appointments WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Supprimer l'utilisateur
    $stmtDel = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmtDel->bind_param("i", $userId);
    if ($stmtDel->execute()) {
        session_destroy();
        header("Location: register.php"); // Ou login.php
        exit;
    } else {
        $error = "Erreur lors de la suppression du compte.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer mon compte</title>
</head>
<body>
<h1>Supprimer mon compte</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo e($error); ?></p>
<?php endif; ?>

<form action="delete_account.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <p>Êtes-vous sûr de vouloir supprimer votre compte ?</p>
    <button type="submit">Oui, supprimer</button>
</form>

<p><a href="profile.php">Annuler</a></p>
</body>
</html>
