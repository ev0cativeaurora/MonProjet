<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$csrfToken = generateCsrfToken();
$error   = null;
$success = null;

// Annuler un RDV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $stmtDel = $conn->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ?");
    $stmtDel->bind_param("ii", $appointmentId, $userId);
    if ($stmtDel->execute()) {
        $success = "Rendez-vous annulé.";
    } else {
        $error = "Erreur lors de l'annulation.";
    }
}

// Récupérer les rendez-vous de l'utilisateur
$stmt = $conn->prepare("SELECT id, date_rdv, heure_rdv FROM appointments WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuler un rendez-vous</title>
</head>
<body>
<h1>Annuler un rendez-vous</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo e($error); ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?php echo e($success); ?></p>
<?php endif; ?>

<?php if (count($appointments) > 0): ?>
    <ul>
    <?php foreach ($appointments as $app): ?>
        <li>
            Rendez-vous le <?php echo e($app['date_rdv']); ?> à <?php echo e($app['heure_rdv']); ?>
            <form action="cancel_appointment.php" method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                <input type="hidden" name="appointment_id" value="<?php echo e($app['id']); ?>">
                <button type="submit">Annuler</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Vous n'avez aucun rendez-vous.</p>
<?php endif; ?>

<p><a href="profile.php">Retour au profil</a></p>
</body>
</html>
