<?php
/**
 * contact.php
 * Page de contact avec formulaire - version corrigée pour le footer
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config_smtp.php';

$pageTitle = "Contact";
$csrfToken = generateCsrfToken();
$error = null;
$success = null;

// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    // Récupération des champs
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation simple
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        // Préparer le message à envoyer à l'administrateur
        $adminEmail = "contact@monprojet.fr"; // Remplacez par votre email
        $adminSubject = "Nouveau message de contact: $sujet";
        $adminBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <div style='background-color: #4CAF50; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0;'>
                    <h1 style='margin: 0;'>Nouveau message de contact</h1>
                </div>
                <div style='padding: 20px;'>
                    <p><strong>Nom:</strong> $nom</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Sujet:</strong> $sujet</p>
                    <p><strong>Message:</strong></p>
                    <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px;'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                </div>
                <div style='background-color: #f5f5f5; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 0.9em;'>
                    <p>Ce message a été envoyé depuis le formulaire de contact du site.</p>
                </div>
            </div>
        ";

        // Envoyer l'email à l'administrateur
        sendVerificationEmail($adminEmail, $adminSubject, $adminBody);

        // Préparer la confirmation pour l'utilisateur
        $userSubject = "Confirmation de votre message";
        $userBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <div style='background-color: #4CAF50; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0;'>
                    <h1 style='margin: 0;'>Merci pour votre message</h1>
                </div>
                <div style='padding: 20px;'>
                    <p>Bonjour $nom,</p>
                    <p>Nous avons bien reçu votre message concernant \"$sujet\".</p>
                    <p>Notre équipe va l'examiner et vous répondra dans les plus brefs délais.</p>
                    <p>Voici un rappel de votre message :</p>
                    <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px;'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                </div>
                <div style='background-color: #f5f5f5; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 0.9em;'>
                    <p>&copy; " . date('Y') . " - MonProjet. Tous droits réservés.</p>
                </div>
            </div>
        ";

        // Envoyer la confirmation à l'utilisateur
        sendVerificationEmail($email, $userSubject, $userBody);

        // Message de succès
        $success = "Votre message a été envoyé avec succès. Une confirmation vous a été envoyée par email.";
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-5">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><i class="bi bi-envelope"></i> Contactez-nous</h1>
                </div>
                <div class="card-body">
                    <p class="mb-4">Vous avez une question ou une demande ? N'hésitez pas à nous contacter via le formulaire ci-dessous.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> <?php echo e($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo e($success); ?>
                        </div>
                    <?php else: ?>
                        <form action="contact.php" method="post" data-validate="true">
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="nom">Nom et prénom</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" id="nom" name="nom" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="email">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" id="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label" for="sujet">Sujet</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                    <select id="sujet" name="sujet" class="form-control" required>
                                        <option value="">Sélectionnez un sujet</option>
                                        <option value="Demande d'information">Demande d'information</option>
                                        <option value="Problème technique">Problème technique</option>
                                        <option value="Suggestion">Suggestion</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label" for="message">Message</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-pencil"></i></span>
                                    <textarea id="message" name="message" class="form-control" rows="6" required></textarea>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Envoyer le message
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informations de contact -->
            <div class="card mb-5">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-info-circle"></i> Nos coordonnées</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="bi bi-geo-alt" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h3 class="h5 mt-2">Adresse</h3>
                                <p>123 Rue des Exemples<br>75000 Paris</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="bi bi-telephone" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h3 class="h5 mt-2">Téléphone</h3>
                                <p>+33 1 23 45 67 89</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="bi bi-envelope" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h3 class="h5 mt-2">Email</h3>
                                <p>contact@monprojet.fr</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h3 class="h5">Heures d'ouverture</h3>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Lundi - Vendredi
                                <span>9h00 - 18h00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Samedi
                                <span>10h00 - 16h00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Dimanche
                                <span>Fermé</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Carte Google Maps - avec une marge correcte pour éviter le chevauchement -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937596!2d2.292292615509614!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1647290078528!5m2!1sfr!2sfr" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>