<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
requireAdmin();

try {
    $vehicles = $pdo->query("SELECT * FROM vehicles")->fetchAll();
    
    foreach ($vehicles as $v) {
        $code = $v['code'];
        // Générer la BONNE url avec le domaine actuel (InfinityFree)
        $vehicleUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/public/satisfaction.php?vehicle=' . urlencode($code);
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($vehicleUrl);
        
        $qrDir = '../qrcodes/';
        if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);
        
        $qrFile = $qrDir . $code . '.png';
        
        $qrContent = @file_get_contents($apiUrl);
        if ($qrContent !== false) {
            file_put_contents($qrFile, $qrContent);
        }
    }
    
    echo "<h1 style='color: green; text-align: center; margin-top: 50px;'>Succès ! Tous les QR Codes ont été recréés avec le bon lien.</h1>";
    echo "<p style='text-align: center;'><a href='/admin/vehicles.php'>Retour aux véhicules</a></p>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
