<?php
// login.php - Version améliorée
require_once __DIR__ . '/config.php';

$pageTitle = "Connexion";
$csrfToken = generateCsrfToken();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csrf_token'])) {
        checkCsrfToken($_POST['csrf_token']);
    } else {
        die("Token CSRF manquant.");
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Vérification des identifiants
    $stmt = $conn->prepare("SELECT id, password, verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Vérifier si le compte est activé
        if ($user['verified'] == 0) {
            $error = "Votre compte n'est pas encore activé. Veuillez vérifier votre email.";
        } else {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['flash_message'] = "Connexion réussie. Bienvenue !";
            
            // Redirection
            $redirect = $_SESSION['redirect_after_login'] ?? 'profile.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        }
    } else {
        $error = "Identifiants incorrects.";
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="auth-container">
                <h2><i class="bi bi-box-arrow-in-right"></i> Connexion</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?php echo e($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                    <div class="form-group mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>

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
                    </div>

                    <div class="remember-me mb-4">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Se souvenir de moi</label>
                        </div>
                        <a href="forgot_password.php" class="forgot-password">Mot de passe oublié ?</a>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </div>
                </form>

                <div class="auth-toggle">
                    <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>