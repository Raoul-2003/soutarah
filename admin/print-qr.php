<?php
// admin/print-qr.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$code = $_GET['code'] ?? '';
if (empty($code)) {
    die("Code véhicule manquant.");
}

$vehicle = getVehicleByCode($pdo, $code);
if (!$vehicle) {
    die("Véhicule introuvable.");
}

$qrPath = '../qrcodes/' . $code . '.png';
if (!file_exists($qrPath)) {
    die("QR Code introuvable pour ce véhicule.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression QR - <?= e($code) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .print-card {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid #E5E7EB;
        }
        .logo {
            max-width: 180px;
            margin-bottom: 2rem;
        }
        .vehicle-info {
            background: #F3F4F6;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .vehicle-info h2 {
            margin: 0;
            color: #0B1F3A;
            font-size: 1.5rem;
        }
        .vehicle-info p {
            margin: 0.5rem 0 0;
            color: #6B7280;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .qr-code {
            width: 100%;
            max-width: 250px;
            height: auto;
            margin-bottom: 2rem;
        }
        .footer-text {
            color: #2563EB;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
        }
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn {
            background: #2563EB;
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 12px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }
        @media print {
            body {
                background: white;
                align-items: flex-start;
                padding-top: 2cm;
            }
            .print-card {
                box-shadow: none;
                border: 2px dashed #ccc;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Imprimer</button>
    </div>

    <div class="print-card">
        <img src="/assets/images/logo.png" class="logo" alt="SOUTARAH">
        
        <div class="vehicle-info">
            <h2><?= e($vehicle['brand']) ?> <?= e($vehicle['model']) ?></h2>
            <p>Code : <?= e($vehicle['code']) ?></p>
        </div>

        <img src="/qrcodes/<?= e($code) ?>.png" class="qr-code" alt="QR Code">
        
        <div class="footer-text">
            Scannez pour évaluer<br>
            votre expérience de location
        </div>
    </div>

    <script>
        // Optionnel : Lancer l'impression automatiquement
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

