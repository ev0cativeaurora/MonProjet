<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$csrfToken = generateCsrfToken();
$error = null;
$success = null;

// Annuler un RDV (via POST)
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
        $success = "Rendez-vous annulé avec succès.";
    } else {
        $error = "Erreur lors de l'annulation.";
    }
}

// Annuler un RDV (via GET - pour les liens directs)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $appointmentId = (int)($_GET['id'] ?? 0);
    
    // Vérifier si le rendez-vous appartient à l'utilisateur
    $stmtCheck = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND user_id = ?");
    $stmtCheck->bind_param("ii", $appointmentId, $userId);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    
    if ($stmtCheck->num_rows > 0) {
        // Rendez-vous confirmé comme appartenant à l'utilisateur
        $_SESSION['appointment_to_cancel'] = $appointmentId;
        // Pas de suppression immédiate, on demande confirmation
    }
}

// Récupérer les rendez-vous de l'utilisateur
$stmt = $conn->prepare("SELECT id, date_rdv, heure_rdv FROM appointments WHERE user_id = ? ORDER BY date_rdv, heure_rdv");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
$upcomingAppointments = [];
$pastAppointments = [];

$today = date('Y-m-d');

while ($row = $result->fetch_assoc()) {
    if ($row['date_rdv'] >= $today) {
        $upcomingAppointments[] = $row;
    } else {
        $pastAppointments[] = $row;
    }
}

$pageTitle = "Gérer mes rendez-vous";
require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Titre de la page -->
            <h1 class="text-center mb-4"><i class="bi bi-calendar-x"></i> Gérer mes rendez-vous</h1>
            
            <!-- Confirmation de suppression (modal léger) -->
            <?php if (isset($_SESSION['appointment_to_cancel'])): ?>
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <h2 class="h5 text-center mb-3">Confirmer l'annulation</h2>
                        <p class="text-center">Êtes-vous sûr de vouloir annuler ce rendez-vous ?</p>
                        <div class="d-flex justify-content-center">
                            <form action="cancel_appointment.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                <input type="hidden" name="appointment_id" value="<?php echo e($_SESSION['appointment_to_cancel']); ?>">
                                <button type="submit" class="btn btn-danger me-2">
                                    <i class="bi bi-check-circle"></i> Oui, annuler
                                </button>
                                <a href="cancel_appointment.php" class="btn btn-outline">
                                    <i class="bi bi-x-circle"></i> Non, conserver
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['appointment_to_cancel']); ?>
            <?php endif; ?>
            
            <!-- Messages de statut -->
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
            
            <!-- Rendez-vous à venir -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-calendar-check"></i> Mes prochains rendez-vous</h2>
                </div>
                <div class="card-body">
                    <?php if (count($upcomingAppointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingAppointments as $app): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $date = new DateTime($app['date_rdv']);
                                                echo e($date->format('d/m/Y')); 
                                                ?>
                                            </td>
                                            <td><?php echo e($app['heure_rdv']); ?></td>
                                            <td>
                                                <form action="cancel_appointment.php" method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                                    <input type="hidden" name="appointment_id" value="<?php echo e($app['id']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i> Annuler
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">Vous n'avez aucun rendez-vous à venir.</p>
                            <a href="appointment.php" class="btn btn-primary mt-2">
                                <i class="bi bi-calendar-plus"></i> Prendre rendez-vous
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Historique des rendez-vous passés -->
            <?php if (count($pastAppointments) > 0): ?>
                <div class="card mb-5">
                    <div class="card-header bg-secondary text-white">
                        <h2 class="h5 mb-0"><i class="bi bi-clock-history"></i> Historique des rendez-vous</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pastAppointments as $app): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $date = new DateTime($app['date_rdv']);
                                                echo e($date->format('d/m/Y')); 
                                                ?>
                                            </td>
                                            <td><?php echo e($app['heure_rdv']); ?></td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="bi bi-check-circle"></i> Effectué
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Boutons de navigation -->
            <div class="text-center mt-4 mb-5">
                <a href="appointment.php" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Prendre un nouveau rendez-vous
                </a>
                <a href="profile.php" class="btn btn-outline ms-2">
                    <i class="bi bi-person-circle"></i> Retour au profil
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>