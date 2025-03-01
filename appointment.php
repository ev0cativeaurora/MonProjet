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

// Récupérer les rendez-vous existants pour le calendrier
$existingAppointments = [];
$stmtExisting = $conn->prepare("SELECT date_rdv, heure_rdv FROM appointments");
$stmtExisting->execute();
$resultExisting = $stmtExisting->get_result();
while ($row = $resultExisting->fetch_assoc()) {
    $existingAppointments[] = $row;
}

// Convertir les données pour JavaScript
$appointmentsJson = json_encode($existingAppointments);

$pageTitle = "Prendre un rendez-vous";
require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><i class="bi bi-calendar-plus"></i> Prendre un rendez-vous</h1>
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
                    
                    <form action="appointment.php" method="post" data-validate="true">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                        
                        <!-- Calendrier interactif -->
                        <div class="calendar-container mb-4">
                            <div class="calendar-header">
                                <h2 class="calendar-month">Mois Année</h2>
                                <div class="calendar-nav">
                                    <button type="button" class="prev-month"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" class="next-month"><i class="bi bi-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="calendar-grid">
                                <div class="calendar-weekdays">
                                    <div>Lun</div>
                                    <div>Mar</div>
                                    <div>Mer</div>
                                    <div>Jeu</div>
                                    <div>Ven</div>
                                    <div>Sam</div>
                                    <div>Dim</div>
                                </div>
                                <div class="calendar-days">
                                    <!-- Les jours seront générés en JS -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label" for="date_rdv">Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="date_rdv" name="date_rdv" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label" for="heure_rdv">Heure</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <select id="heure_rdv" name="heure_rdv" class="form-control" required>
                                    <option value="">Choisir une heure</option>
                                    <?php
                                    // Générer les créneaux de 9h à 17h par intervalles de 30 minutes
                                    $startHour = 9;
                                    $endHour = 17;
                                    for ($hour = $startHour; $hour <= $endHour; $hour++) {
                                        for ($min = 0; $min < 60; $min += 30) {
                                            if ($hour == $endHour && $min > 0) continue;
                                            $timeStr = sprintf("%02d:%02d", $hour, $min);
                                            echo "<option value=\"$timeStr\">$timeStr</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Réserver
                            </button>
                            <a href="profile.php" class="btn btn-outline ms-2">
                                <i class="bi bi-arrow-left"></i> Retour au profil
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Liste des prochains rendez-vous -->
            <?php
            $stmtUpcoming = $conn->prepare("SELECT date_rdv, heure_rdv FROM appointments WHERE user_id = ? AND date_rdv >= CURDATE() ORDER BY date_rdv ASC, heure_rdv ASC LIMIT 3");
            $stmtUpcoming->bind_param("i", $userId);
            $stmtUpcoming->execute();
            $resultUpcoming = $stmtUpcoming->get_result();
            
            if ($resultUpcoming->num_rows > 0):
            ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-calendar-check"></i> Vos prochains rendez-vous</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php while ($rdv = $resultUpcoming->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    <?php 
                                    $date = new DateTime($rdv['date_rdv']);
                                    echo e($date->format('d/m/Y')); 
                                    ?>
                                </div>
                                <span class="badge badge-primary">
                                    <i class="bi bi-clock"></i> <?php echo e($rdv['heure_rdv']); ?>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    
                    <div class="text-center mt-3">
                        <a href="cancel_appointment.php" class="btn btn-sm btn-outline">
                            <i class="bi bi-calendar-x"></i> Gérer mes rendez-vous
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Stockage des rendez-vous existants pour vérification côté client
const existingAppointments = <?php echo $appointmentsJson; ?>;

// Fonction pour vérifier la disponibilité d'un créneau
function isTimeSlotAvailable(date, time) {
    for (let i = 0; i < existingAppointments.length; i++) {
        if (existingAppointments[i].date_rdv === date && existingAppointments[i].heure_rdv === time) {
            return false;
        }
    }
    return true;
}

// Mettre à jour les heures disponibles quand la date change
document.getElementById('date_rdv').addEventListener('change', function() {
    updateAvailableTimeSlots(this.value);
});

function updateAvailableTimeSlots(selectedDate) {
    const timeSelect = document.getElementById('heure_rdv');
    const options = timeSelect.options;
    
    // Réinitialiser toutes les options
    for (let i = 0; i < options.length; i++) {
        options[i].disabled = false;
        options[i].classList.remove('text-danger');
    }
    
    // Désactiver les créneaux déjà réservés
    if (selectedDate) {
        for (let i = 1; i < options.length; i++) {
            if (!isTimeSlotAvailable(selectedDate, options[i].value)) {
                options[i].disabled = true;
                options[i].classList.add('text-danger');
            }
        }
    }
}