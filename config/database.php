<?php
// config/database.php

$host = 'localhost';
$dbname = 'soutarah_satisfaction';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // In production, we should log the error and not display it to the user.
    die('Erreur de connexion à la base de données. Veuillez contacter l\'administrateur.');
}

