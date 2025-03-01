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

// Récupérer les prochains rendez-vous
$stmtAppointments = $conn->prepare("SELECT id, date_rdv, heure_rdv FROM appointments WHERE user_id = ? AND date_rdv >= CURDATE() ORDER BY date_rdv ASC, heure_rdv ASC");
$stmtAppointments->bind_param("i", $userId);
$stmtAppointments->execute();
$resultAppointments = $stmtAppointments->get_result();
$appointments = [];
while ($row = $resultAppointments->fetch_assoc()) {
    $appointments[] = $row;
}

$pageTitle = "Mon Profil";
require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Colonne de gauche - Informations utilisateur -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><i class="bi bi-person-circle"></i> Mon profil</h1>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> <?php echo e($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo e($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="profile.php" method="post" data-validate="true">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="nom">Nom</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" id="nom" name="nom" class="form-control" value="<?php echo e($user['nom']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="prenom">Prénom</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo e($user['prenom']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label" for="date_naissance">Date de naissance</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="date_naissance" name="date_naissance" class="form-control" value="<?php echo e($user['date_naissance']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label" for="adresse">Adresse postale</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" id="adresse" name="adresse" class="form-control" value="<?php echo e($user['adresse']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label" for="telephone">Numéro de téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?php echo e($user['telephone']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo e($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="appointment.php" class="btn btn-outline">
                            <i class="bi bi-calendar-plus"></i> Prendre un rendez-vous
                        </a>
                        <a href="delete_account.php" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Supprimer mon compte
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne de droite - Menu utilisateur et rendez-vous -->
        <div class="col-lg-4">
            <!-- Carte Menu utilisateur - CORRIGÉE pour le footer -->
            <div class="user-menu">
                <div class="user-menu-header">
                    <h3><i class="bi bi-gear"></i> Menu utilisateur</h3>
                </div>
                <ul class="user-menu-list">
                    <li>
                        <a href="profile.php">
                            <i class="bi bi-person-circle"></i> Mon profil
                        </a>
                    </li>
                    <li>
                        <a href="appointment.php">
                            <i class="bi bi-calendar-plus"></i> Prendre rendez-vous
                        </a>
                    </li>
                    <li>
                        <a href="cancel_appointment.php">
                            <i class="bi bi-calendar-x"></i> Annuler rendez-vous
                        </a>
                    </li>
                    <li>
                        <a href="change_password.php">
                            <i class="bi bi-key"></i> Changer de mot de passe
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Se déconnecter
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Carte Mes rendez-vous -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-calendar-check"></i> Mes rendez-vous</h2>
                </div>
                <div class="card-body">
                    <?php if (count($appointments) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($appointments as $app): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-calendar-event text-primary"></i>
                                        <?php 
                                        $date = new DateTime($app['date_rdv']);
                                        echo e($date->format('d/m/Y')); 
                                        ?>
                                    </div>
                                    <div>
                                        <span class="badge badge-primary">
                                            <i class="bi bi-clock"></i> <?php echo e($app['heure_rdv']); ?>
                                        </span>
                                        <a href="cancel_appointment.php?id=<?php echo e($app['id']); ?>" class="btn btn-sm btn-danger ms-2">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">Vous n'avez aucun rendez-vous à venir.</p>
                            <a href="appointment.php" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-calendar-plus"></i> Prendre rendez-vous
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>