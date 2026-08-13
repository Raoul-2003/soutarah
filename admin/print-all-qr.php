<?php
// admin/print-all-qr.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY brand, model");
$vehicles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression en lot - QR Codes</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 2rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        .print-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            border: 2px dashed #E5E7EB;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 1rem;
        }
        .vehicle-info {
            background: #F3F4F6;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .vehicle-info h2 {
            margin: 0;
            color: #0B1F3A;
            font-size: 1.2rem;
        }
        .vehicle-info p {
            margin: 0.25rem 0 0;
            color: #6B7280;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .qr-code {
            width: 100%;
            max-width: 180px;
            height: auto;
            margin-bottom: 1rem;
        }
        .footer-text {
            color: #2563EB;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.3;
        }
        .no-print {
            text-align: center;
            margin-bottom: 2rem;
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
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .grid {
                gap: 1cm;
            }
            .print-card {
                border: 1px dashed #ccc;
            }
            /* Assurer environ 4-6 par page A4 selon la taille */
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Imprimer tous les QR Codes</button>
    </div>

    <div class="grid">
        <?php foreach ($vehicles as $vehicle): ?>
            <?php if (file_exists('../qrcodes/' . $vehicle['code'] . '.png')): ?>
                <div class="print-card">
                    <img src="/soutarah/assets/images/logo.png" class="logo" alt="SOUTARAH">
                    
                    <div class="vehicle-info">
                        <h2><?= e($vehicle['brand']) ?> <?= e($vehicle['model']) ?></h2>
                        <p>Code : <?= e($vehicle['code']) ?></p>
                    </div>

                    <img src="/soutarah/qrcodes/<?= e($vehicle['code']) ?>.png" class="qr-code" alt="QR Code">
                    
                    <div class="footer-text">
                        Scannez pour évaluer<br>
                        votre expérience de location
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

</body>
</html>
