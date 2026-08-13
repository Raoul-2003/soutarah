<?php
// includes/functions.php

/**
 * Échappe les sorties HTML pour prévenir les failles XSS.
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL donnée.
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Génère un token sécurisé (ex: pour le CSRF ou QR code).
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Vérifie si la requête courante est de type POST.
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Génère et retourne un token CSRF.
 */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité du token CSRF fourni.
 */
function verifyCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Définit un message flash.
 */
function flash($name, $message = '', $type = 'success') {
    if (!empty($message)) {
        $_SESSION['flash'][$name] = ['message' => $message, 'type' => $type];
    }
}

/**
 * Récupère un message flash s'il existe et l'efface.
 */
function getFlash($name) {
    if (isset($_SESSION['flash'][$name])) {
        $flash = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        
        $typeClass = $flash['type'] === 'error' ? 'alert-error' : 'alert-success';
        return '<div class="alert ' . $typeClass . '">' . e($flash['message']) . '</div>';
    }
    return '';
}

/**
 * Récupère un véhicule par son code public.
 */
function getVehicleByCode($pdo, $code) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE code = ? LIMIT 1");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

/**
 * Récupère un véhicule par son ID.
 */
function getVehicleById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
