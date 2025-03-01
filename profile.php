<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer le flash_message s'il existe
$flashMessage = null;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // On le supprime pour qu'il n'apparaisse qu'une seule fois
}

$userId = $_SESSION['user_id'];

// Récupérer les infos de l'utilisateur
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, adresse, telephone, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$csrfToken = generateCsrfToken();
$error = null;
$success = null;

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    $nom       = trim($_POST['nom']);
    $prenom    = trim($_POST['prenom']);
    $dateNaiss = trim($_POST['date_naissance']);
    $adresse   = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $email     = trim($_POST['email']);

    // Vérifier si l'email est déjà pris par un autre
    if ($email !== $user['email']) {
        $stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtCheck->bind_param("si", $email, $userId);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $error = "Cet email est déjà utilisé par un autre compte.";
        } else {
            $canUpdate = true;
        }
    } else {
        $canUpdate = true;
    }

    if (isset($canUpdate) && $canUpdate === true) {
        $stmtUpdate = $conn->prepare(
            "UPDATE users SET nom=?, prenom=?, date_naissance=?, adresse=?, telephone=?, email=? WHERE id=?"
        );
        $stmtUpdate->bind_param("ssssssi", $nom, $prenom, $dateNaiss, $adresse, $telephone, $email, $userId);
        if ($stmtUpdate->execute()) {
            $success = "Informations mises à jour avec succès.";
            // Mettre à jour $user localement
            $user['nom']            = $nom;
            $user['prenom']         = $prenom;
            $user['date_naissance'] = $dateNaiss;
            $user['adresse']        = $adresse;
            $user['telephone']      = $telephone;
            $user['email']          = $email;
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
<h1>Mon profil</h1>

<!-- Afficher le message s'il existe -->
<?php if ($flashMessage): ?>
    <p style="color: green;"><?php echo e($flashMessage); ?></p>
<?php endif; ?>

<p>Bonjour <?php echo e($user['prenom']); ?> !</p>

<?php if ($error): ?>
    <p style="color:red;"><?php echo e($error); ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?php echo e($success); ?></p>
<?php endif; ?>

<form action="profile.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="update_profile" value="1">

    <label>Nom :</label><br>
    <input type="text" name="nom" value="<?php echo e($user['nom']); ?>" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" value="<?php echo e($user['prenom']); ?>" required><br><br>

    <label>Date de naissance :</label><br>
    <input type="date" name="date_naissance" value="<?php echo e($user['date_naissance']); ?>" required><br><br>

    <label>Adresse postale :</label><br>
    <input type="text" name="adresse" value="<?php echo e($user['adresse']); ?>" required><br><br>

    <label>Téléphone :</label><br>
    <input type="tel" name="telephone" value="<?php echo e($user['telephone']); ?>" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" value="<?php echo e($user['email']); ?>" required><br><br>

    <button type="submit">Mettre à jour</button>
</form>

<p><a href="logout.php">Se déconnecter</a></p>
<p><a href="delete_account.php">Supprimer mon compte</a></p>
<p><a href="appointment.php">Prendre un rendez-vous</a></p>
<p><a href="cancel_appointment.php">Annuler un rendez-vous</a></p>
</body>
</html>
