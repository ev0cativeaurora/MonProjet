<?php
/**
 * register.php
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/config_smtp.php'; // Si vous souhaitez envoyer un email
require_once __DIR__ . '/header.php';

// Génération du token CSRF
$csrfToken = generateCsrfToken();
$error   = null;
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
    $nom         = trim($_POST['nom'] ?? '');
    $prenom      = trim($_POST['prenom'] ?? '');
    $dateNaiss   = trim($_POST['date_naissance'] ?? '');
    $adresse     = trim($_POST['adresse'] ?? '');
    $telephone   = trim($_POST['telephone'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $password    = trim($_POST['password'] ?? '');

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Générer un token de vérification (si besoin)
        $verificationToken = bin2hex(random_bytes(32));

        // Insérer l'utilisateur en base
        $stmtInsert = $conn->prepare(
            "INSERT INTO users 
             (nom, prenom, date_naissance, adresse, telephone, email, password, verified, verification_token)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)"
        );
        $stmtInsert->bind_param("ssssssss", 
            $nom, $prenom, $dateNaiss, $adresse, 
            $telephone, $email, $hashedPassword, 
            $verificationToken
        );

        if ($stmtInsert->execute()) {
            // Envoi d'un mail de vérification (facultatif)
            $verifyLink = "http://localhost/MonProjet/verify.php?token=$verificationToken";

            $sujet   = "Vérification de votre compte";
            $message = "
                <h1>Bienvenue, $prenom !</h1>
                <p>Merci pour votre inscription. Pour activer votre compte, cliquez ci-dessous :</p>
                <p><a href='$verifyLink'>Activer mon compte</a></p>
                <p>Si le lien ne fonctionne pas, copiez-collez dans votre navigateur : $verifyLink</p>
            ";

            sendVerificationEmail($email, $sujet, $message);

            $success = "Compte créé ! Consultez votre boîte mail pour vérifier votre compte.";
        } else {
            $error = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
<h1>Créer un compte</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo e($error); ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?php echo e($success); ?></p>
<?php endif; ?>

<form action="register.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" required><br><br>

    <label>Date de naissance :</label><br>
    <input type="date" name="date_naissance" required><br><br>

    <label>Adresse postale :</label><br>
    <input type="text" name="adresse" required><br><br>

    <label>Numéro de téléphone :</label><br>
    <input type="tel" name="telephone" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">S'inscrire</button>
</form>
</body>
</html>
