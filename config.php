<?php
/**
 * config.php
 * Configuration de la base de données et fonctions utilitaires
 */

session_start();

// Paramètres de connexion MySQL
$host     = 'localhost';
$dbName   = 'mon_projet_db';   // Assurez-vous que c'est le nom exact de votre base
$user     = 'root';            // Par défaut, sous XAMPP
$password = '';                // Par défaut, sous XAMPP (vide)

// Connexion à la base
$conn = new mysqli($host, $user, $password, $dbName);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Fonction utilitaire pour échapper les sorties (prévenir XSS)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Générer un token CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier le token CSRF
function checkCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("Token CSRF invalide ou absent.");
    }
}
