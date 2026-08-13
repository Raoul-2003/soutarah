<?php
// includes/auth.php
session_start();

/**
 * Vérifie si un administrateur est connecté.
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Force la connexion pour accéder à une page d'administration.
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect('/admin/login.php');
    }
}

/**
 * Connecte un administrateur.
 */
function loginAdmin($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Régénération de l'ID de session pour prévenir la fixation de session
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }
    return false;
}

/**
 * Déconnecte un administrateur.
 */
function logoutAdmin() {
    session_unset();
    session_destroy();
}

