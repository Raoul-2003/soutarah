<?php
require 'config/database.php';
$stmt = $pdo->query("SHOW COLUMNS FROM vehicles");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $pdo->query("SELECT code, status FROM vehicles LIMIT 5");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
