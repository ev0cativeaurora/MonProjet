<?php
/**
 * config_smtp.php
 * Configuration SMTP pour envoyer des emails via PHPMailer et Gmail
 */

 require_once __DIR__ . '/vendor/autoload.php';  // <-- Autoload de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Si vous avez installé PHPMailer via Composer :
// require __DIR__ . '/vendor/autoload.php';

// Sinon, si vous l'avez extrait manuellement, adaptez les chemins :
// require_once __DIR__ . '/PHPMailer/src/Exception.php';
// require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
// require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendVerificationEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  
        $mail->SMTPAuth   = true;             
        // Identifiants Gmail : votre email et mot de passe d'application
        $mail->Username   = 'anouargamer1@gmail.com';     
        $mail->Password   = '**************************************************************';         
        
        // Choisissez SSL (port 465) OU TLS (port 587)
        // (décommentez la configuration souhaitée)

        // 1) SSL sur port 465
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // 2) TLS sur port 587
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port       = 587;

        // Expéditeur
        $mail->setFrom('anouargamer1@gmail.com', 'MonProjet');

        // Destinataire
        $mail->addAddress($to);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Envoi
        $mail->send();
    } catch (Exception $e) {
        // Pour debug, vous pouvez décommenter :
        // echo "Erreur lors de l'envoi du mail : " . $mail->ErrorInfo;
    }
}
