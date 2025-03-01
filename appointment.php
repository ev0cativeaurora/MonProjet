<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId   = $_SESSION['user_id'];
$csrfToken = generateCsrfToken();
$error   = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    $dateRdv = trim($_POST['date_rdv'] ?? '');
    $heureRdv= trim($_POST['heure_rdv'] ?? '');

    // Vérifier la disponibilité
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE date_rdv = ? AND heure_rdv = ?");
    $stmt->bind_param("ss", $dateRdv, $heureRdv);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = "Ce créneau est déjà réservé.";
    } else {
        // Insérer
        $stmtInsert = $conn->prepare("INSERT INTO appointments (user_id, date_rdv, heure_rdv) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("iss", $userId, $dateRdv, $heureRdv);
        if ($stmtInsert->execute()) {
            $success = "Rendez-vous réservé avec succès.";
        } else {
            $error = "Erreur lors de la réservation.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre un rendez-vous</title>
</head>
<body>
<h1>Prendre un rendez-vous</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo e($error); ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?php echo e($success); ?></p>
<?php endif; ?>

<form action="appointment.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <label>Date :</label><br>
    <input type="date" name="date_rdv" required><br><br>

    <label>Heure :</label><br>
    <input type="time" name="heure_rdv" required><br><br>

    <button type="submit">Réserver</button>
</form>

<p><a href="profile.php">Retour au profil</a></p>
</body>
</html>
