<?php
// admin/authenticate.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isPost()) {
    redirect('/soutarah/admin/login.php');
}

$token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    flash('error', 'Erreur de sécurité CSRF.', 'error');
    redirect('/soutarah/admin/login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    flash('error', 'Veuillez remplir tous les champs.', 'error');
    redirect('/soutarah/admin/login.php');
}

if (loginAdmin($pdo, $email, $password)) {
    redirect('/soutarah/admin/dashboard.php');
} else {
    flash('error', 'Identifiants incorrects.', 'error');
    redirect('/soutarah/admin/login.php');
}
