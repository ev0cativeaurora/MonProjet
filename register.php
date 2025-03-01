<?php
/**
 * register.php - Version améliorée
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config_smtp.php';

$pageTitle = "Inscription";
$csrfToken = generateCsrfToken();
$error = null;
$success = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    // Récupération des champs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $dateNaiss = trim($_POST['date_naissance'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Validation côté serveur
    $errors = [];

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier la complexité du mot de passe
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Cet email est déjà utilisé.";
    }

    // Si aucune erreur, procéder à l'enregistrement
    if (empty($errors)) {
        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Générer un token de vérification
        $verificationToken = bin2hex(random_bytes(32));

        // Insérer l'utilisateur en base
        $stmtInsert = $conn->prepare(
            "INSERT INTO users 
             (nom, prenom, date_naissance, adresse, telephone, email, password, verified, verification_token)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)"
        );
        $stmtInsert->bind_param(
            "ssssssss",
            $nom, $prenom, $dateNaiss, $adresse,
            $telephone, $email, $hashedPassword,
            $verificationToken
        );

        if ($stmtInsert->execute()) {
            // Créer l'URL de vérification avec le chemin absolu
            $siteUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $siteUrl = rtrim($siteUrl, '/'); // Supprimer le slash final s'il existe
            $verifyLink = "$siteUrl/verify.php?token=$verificationToken";
        
            // Préparer l'email
            $subject = "Vérification de votre compte";
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <div style='background-color: #4CAF50; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0;'>
                        <h1 style='margin: 0;'>Bienvenue, $prenom !</h1>
                    </div>
                    <div style='padding: 20px;'>
                        <p>Merci pour votre inscription. Pour activer votre compte, cliquez sur le bouton ci-dessous :</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$verifyLink' style='background-color: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Activer mon compte</a>
                        </div>
                        <p>Si le bouton ne fonctionne pas, copiez-collez le lien suivant dans votre navigateur :</p>
                        <p style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; word-break: break-all;'>$verifyLink</p>
                        <p>Ce lien est valable pendant 24 heures.</p>
                    </div>
                    <div style='background-color: #f5f5f5; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 0.9em;'>
                        <p>&copy; " . date('Y') . " - MonProjet. Tous droits réservés.</p>
                    </div>
                </div>
            ";
        
            // Envoyer l'email
            sendVerificationEmail($email, $subject, $message);
        
            $success = "Compte créé avec succès ! Un email de vérification a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception.";
        } else {
            $error = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    } else {
        // Afficher les erreurs
        $error = implode("<br>", $errors);
    }

    $stmt->close();
}

require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><i class="bi bi-person-plus"></i> Créer un compte</h1>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                            <div class="mt-3">
                                <a href="login.php" class="btn btn-sm btn-primary">
                                    <i class="bi bi-box-arrow-in-right"></i> Aller à la page de connexion
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="register.php" method="post" data-validate="true">
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="nom">Nom</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" id="nom" name="nom" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="prenom">Prénom</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" id="prenom" name="prenom" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="date_naissance">Date de naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="date" id="date_naissance" name="date_naissance" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="adresse">Adresse postale</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" id="adresse" name="adresse" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="telephone">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" id="telephone" name="telephone" class="form-control" required>
                                </div>
                                <small class="form-text">Format: 06XXXXXXXX ou 07XXXXXXXX</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="password">Mot de passe</label>
                                        <div class="password-toggle">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                <input type="password" id="password" name="password" class="form-control" required>
                                            </div>
                                            <button type="button" class="password-toggle-btn">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <small class="form-text">Au moins 8 caractères</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="confirm_password">Confirmer le mot de passe</label>
                                        <div class="password-toggle">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                            </div>
                                            <button type="button" class="password-toggle-btn">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                                <label for="terms" class="form-check-label">J'accepte les <a href="terms.php">conditions générales d'utilisation</a></label>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> S'inscrire
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>