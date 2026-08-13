<?php
// admin/statistics.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

try {
    // Globals
    $totalResponses = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses")->fetchColumn();
    $totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
    
    $avgRating = 0;
    if ($totalResponses > 0) {
        $avgRating = round($pdo->query("SELECT AVG(overall_rating) FROM satisfaction_responses")->fetchColumn(), 1);
    }
    
    $responseRate = $totalVehicles > 0 ? round(($totalResponses / $totalVehicles) * 100, 1) : 0;

    // Tech problems
    $techProblems = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses WHERE technical_problem = 'Oui'")->fetchColumn();

    // Avec/Sans Chauffeur
    $withDriver = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses WHERE with_driver = 'Oui'")->fetchColumn();
    $withoutDriver = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses WHERE with_driver = 'Non'")->fetchColumn();

    // Par Marque (Note Globale seulement)
    $byBrand = $pdo->query("
        SELECT v.brand, COUNT(r.id) as total, AVG(r.overall_rating) as avg_rating
        FROM vehicles v
        JOIN satisfaction_responses r ON v.id = r.vehicle_id
        GROUP BY v.brand
        ORDER BY avg_rating DESC
    ")->fetchAll();

    // Evolution Mensuelle (6 derniers mois)
    $monthly = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total, AVG(overall_rating) as avg_rating
        FROM satisfaction_responses
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month ASC
    ")->fetchAll();

    $mLabels = [];
    $mTotals = [];
    $mAvgs = [];
    foreach ($monthly as $m) {
        $mLabels[] = $m['month'];
        $mTotals[] = (int)$m['total'];
        $mAvgs[] = round($m['avg_rating'], 1);
    }

} catch (PDOException $e) {
    die("Erreur BD : " . $e->getMessage());
}

$pageTitle = "Statistiques - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none fixed no-print">
    <div class="absolute w-[600px] h-[600px] bg-purple-500/5 rounded-full blur-[120px] top-1/4 -right-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-10 -left-20"></div>
</div>

<main class="w-full max-w-[1280px] mx-auto px-4 md:px-8 py-8 relative z-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Statistiques Détaillées</h1>
            <p class="text-white/60 font-body-lg">Analyse approfondie de la satisfaction client</p>
        </div>
        <button onclick="window.print()" class="mt-4 md:mt-0 bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2 group hover:shadow-[0_0_20px_rgba(255,255,255,0.2)] no-print">
            <span class="material-symbols-outlined group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 0;">print</span>
            Imprimer le rapport
        </button>
    </div>

    <!-- KPIs principaux -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 fade-in-up delay-100">
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-[#39ff14]/10 text-[#39ff14] border border-[#39ff14]/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">star</span>
            </div>
            <div class="font-black text-5xl text-[#39ff14] drop-shadow-[0_0_15px_rgba(57,255,20,0.3)] mb-1"><?= e($avgRating) ?> <span class="text-xl text-white/40">/ 5</span></div>
            <div class="font-label-sm text-white/50 uppercase tracking-widest font-semibold">Note Moyenne Globale</div>
        </div>
        
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">forum</span>
            </div>
            <div class="font-black text-5xl text-white drop-shadow-md mb-1"><?= e($totalResponses) ?></div>
            <div class="font-label-sm text-white/50 uppercase tracking-widest font-semibold">Avis Collectés</div>
        </div>
        
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">build</span>
            </div>
            <div class="font-black text-5xl text-red-400 drop-shadow-[0_0_15px_rgba(239,68,68,0.3)] mb-1"><?= e($techProblems) ?></div>
            <div class="font-label-sm text-white/50 uppercase tracking-widest font-semibold">Problèmes signalés</div>
        </div>
        
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">percent</span>
            </div>
            <div class="font-black text-5xl text-white drop-shadow-md mb-1"><?= e($responseRate) ?>%</div>
            <div class="font-label-sm text-white/50 uppercase tracking-widest font-semibold">Taux de réponse</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 fade-in-up delay-200">
        <!-- Monthly Chart -->
        <div class="lg:col-span-2 bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#39ff14]">bar_chart</span> 
                Évolution Mensuelle (6 mois)
            </h3>
            <div class="w-full h-[320px]">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        
        <!-- Driver Chart -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative flex flex-col items-center justify-center">
            <h3 class="text-xl font-bold text-white mb-6 w-full text-left flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-400">pie_chart</span> 
                Répartition Location
            </h3>
            <div class="w-full max-w-[300px] h-[300px]">
                <canvas id="driverChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Stats par Marque -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] overflow-hidden fade-in-up delay-300">
        <div class="p-6 border-b border-white/10 bg-white/5">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-400">directions_car</span>
                Note Globale par Marque
            </h3>
        </div>
        
        <?php if(empty($byBrand)): ?>
            <div class="p-10 text-center text-white/50">Aucune donnée disponible.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-black/20 text-white/40 font-label-sm uppercase tracking-widest">
                            <th class="p-5 font-semibold">Marque</th>
                            <th class="p-5 font-semibold text-center">Nombre d'avis</th>
                            <th class="p-5 font-semibold text-center">Note Moyenne Globale</th>
                            <th class="p-5 font-semibold w-[40%]">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach($byBrand as $b): ?>
                            <?php 
                                $avg = $b['avg_rating'];
                                $color = $avg >= 4 ? 'bg-[#39ff14] shadow-[0_0_10px_rgba(57,255,20,0.5)]' : ($avg >= 3 ? 'bg-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.5)]' : 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]');
                                $textColor = $avg >= 4 ? 'text-[#39ff14]' : ($avg >= 3 ? 'text-yellow-400' : 'text-red-400');
                                $bgColor = $avg >= 4 ? 'bg-[#39ff14]/10 border-[#39ff14]/30' : ($avg >= 3 ? 'bg-yellow-400/10 border-yellow-400/30' : 'bg-red-400/10 border-red-400/30');
                            ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="p-5 font-bold text-white text-lg"><?= e($b['brand']) ?></td>
                                <td class="p-5 text-center text-white/70 font-mono text-lg"><?= e($b['total']) ?></td>
                                <td class="p-5 text-center">
                                    <span class="font-bold inline-block border px-3 py-1 rounded-lg <?= $textColor ?> <?= $bgColor ?>">
                                        <?= number_format($avg, 1) ?> / 5
                                    </span>
                                </td>
                                <td class="p-5">
                                    <div class="w-full bg-white/10 rounded-full h-3 border border-white/5 overflow-hidden">
                                        <div class="<?= $color ?> h-full rounded-full transition-all duration-1000 ease-out" style="width: <?= ($avg / 5) * 100 ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.color = 'rgba(255, 255, 255, 0.5)';
    Chart.defaults.font.family = 'Inter';

    // Driver Chart
    new Chart(document.getElementById('driverChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Avec Chauffeur (Oui)', 'Sans Chauffeur (Non)'],
            datasets: [{
                data: [<?= (int)$withDriver ?>, <?= (int)$withoutDriver ?>],
                backgroundColor: ['#39ff14', 'rgba(255,255,255,0.1)'],
                borderColor: ['rgba(0,0,0,0)', 'rgba(255,255,255,0.1)'],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { color: 'rgba(255, 255, 255, 0.7)' } },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12
                }
            }
        }
    });

    // Monthly Chart
    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($mLabels) ?>,
            datasets: [{
                label: 'Nombre d\'avis',
                data: <?= json_encode($mTotals) ?>,
                backgroundColor: 'rgba(57, 255, 20, 0.8)',
                hoverBackgroundColor: '#39ff14',
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false } 
                },
                x: { 
                    grid: { display: false } 
                }
            },
            plugins: {
                legend: { labels: { color: 'rgba(255, 255, 255, 0.7)' } },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12
                }
            }
        }
    });
});
</script>

<!-- CSS specifically for print -->
<style>
@media print {
    body { background-color: white !important; color: black !important; }
    header, button, .no-print { display: none !important; }
    main { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .bg-white\/5, .bg-white\/10 { background-color: white !important; border: 1px solid #e0e3e5 !important; }
    .text-white, .text-white\/50, .text-white\/60, .text-white\/70 { color: black !important; }
    .text-\[\#39ff14\] { color: #166534 !important; }
    .shadow-sm, .shadow-lg { box-shadow: none !important; }
    .backdrop-blur-xl { backdrop-filter: none !important; }
}
</style>

<?php require_once '../includes/footer.php'; ?>

