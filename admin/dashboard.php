<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Statistiques de base
$stats = [
    'total_vehicles' => 0,
    'active_vehicles' => 0,
    'total_responses' => 0,
    'avg_rating' => 0,
    'tech_problems' => 0
];

try {
    $stats['total_vehicles'] = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
    $stats['active_vehicles'] = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status = 'available'")->fetchColumn();
    $stats['total_responses'] = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses")->fetchColumn();
    $stats['tech_problems'] = $pdo->query("SELECT COUNT(*) FROM satisfaction_responses WHERE technical_problem = 'Oui'")->fetchColumn();
    
    if ($stats['total_responses'] > 0) {
        $stats['avg_rating'] = round($pdo->query("SELECT AVG(overall_rating) FROM satisfaction_responses")->fetchColumn(), 1);
    }
    
    // Top 5 Meilleurs
    $top5 = $pdo->query("
        SELECT v.code, v.brand, v.model, AVG(r.overall_rating) as avg_rating, COUNT(r.id) as total
        FROM vehicles v
        JOIN satisfaction_responses r ON v.id = r.vehicle_id
        GROUP BY v.id
        HAVING total > 0
        ORDER BY avg_rating DESC
        LIMIT 5
    ")->fetchAll();

    // Top 5 Moins bons
    $flop5 = $pdo->query("
        SELECT v.code, v.brand, v.model, AVG(r.overall_rating) as avg_rating, COUNT(r.id) as total
        FROM vehicles v
        JOIN satisfaction_responses r ON v.id = r.vehicle_id
        GROUP BY v.id
        HAVING total > 0
        ORDER BY avg_rating ASC
        LIMIT 5
    ")->fetchAll();
    
    // Réponses par jour (pour le graphique) - 7 derniers jours
    $chartData = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as count, AVG(overall_rating) as avg
        FROM satisfaction_responses
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ")->fetchAll();

    $dates = [];
    $counts = [];
    $avgs = [];
    foreach ($chartData as $row) {
        $dates[] = date('d/m', strtotime($row['date']));
        $counts[] = (int)$row['count'];
        $avgs[] = round($row['avg'], 1);
    }

    // Dernières réponses
    $recentResponses = $pdo->query("
        SELECT r.*, v.code as vehicle_code, v.brand, v.model 
        FROM satisfaction_responses r
        JOIN vehicles v ON r.vehicle_id = v.id
        ORDER BY r.created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}

$pageTitle = "Dashboard Admin - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
    <div class="absolute w-[600px] h-[600px] bg-[#39ff14]/5 rounded-full blur-[120px] -top-32 -left-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-0 right-0"></div>
</div>

<main class="w-full max-w-[1280px] mx-auto px-4 md:px-8 py-8 relative z-10">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Tableau de bord</h1>
            <p class="text-white/60 font-body-lg">Vue d'ensemble de la flotte et de la satisfaction client</p>
        </div>
        <a href="/admin/statistics.php" class="mt-4 md:mt-0 bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2 group hover:shadow-[0_0_20px_rgba(57,255,20,0.2)]">
            <span class="material-symbols-outlined text-[#39ff14] group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 1;">analytics</span>
            Toutes les stats
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-4 rounded-xl mb-8 backdrop-blur-sm flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- KPIs (Glassmorphism) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 fade-in-up delay-100">
        
        <!-- Reponses Totales -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all hover:border-white/20">
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[120px] text-white opacity-5 group-hover:opacity-10 transition-opacity group-hover:scale-110 duration-500" style="font-variation-settings: 'FILL' 1;">chat</span>
            <div class="relative z-10">
                <div class="font-label-sm text-white/50 uppercase tracking-widest mb-2 font-semibold">Réponses totales</div>
                <div class="font-black text-5xl text-white drop-shadow-md"><?= e($stats['total_responses']) ?></div>
            </div>
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-400 opacity-50"></div>
        </div>
        
        <!-- Note Moyenne -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all hover:border-white/20">
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[120px] text-[#39ff14] opacity-5 group-hover:opacity-10 transition-opacity group-hover:scale-110 duration-500" style="font-variation-settings: 'FILL' 1;">star</span>
            <div class="relative z-10">
                <div class="font-label-sm text-white/50 uppercase tracking-widest mb-2 font-semibold">Note Moyenne</div>
                <div class="font-black text-5xl text-[#39ff14] drop-shadow-[0_0_15px_rgba(57,255,20,0.3)]"><?= e($stats['avg_rating']) ?> <span class="text-2xl text-white/40">/5</span></div>
            </div>
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#39ff14] to-emerald-400 opacity-70"></div>
        </div>

        <!-- Vehicules Actifs -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all hover:border-white/20">
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[120px] text-white opacity-5 group-hover:opacity-10 transition-opacity group-hover:scale-110 duration-500" style="font-variation-settings: 'FILL' 1;">directions_car</span>
            <div class="relative z-10">
                <div class="font-label-sm text-white/50 uppercase tracking-widest mb-2 font-semibold">Véhicules Actifs</div>
                <div class="font-black text-5xl text-white drop-shadow-md"><?= e($stats['active_vehicles']) ?> <span class="text-2xl text-white/40">/ <?= e($stats['total_vehicles']) ?></span></div>
            </div>
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500 opacity-50"></div>
        </div>

        <!-- Problemes Signales -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden group hover:bg-white/10 transition-all hover:border-white/20">
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[120px] text-red-500 opacity-5 group-hover:opacity-10 transition-opacity group-hover:scale-110 duration-500" style="font-variation-settings: 'FILL' 1;">warning</span>
            <div class="relative z-10">
                <div class="font-label-sm text-white/50 uppercase tracking-widest mb-2 font-semibold">Problèmes Tech.</div>
                <div class="font-black text-5xl text-red-400 drop-shadow-[0_0_15px_rgba(239,68,68,0.4)]"><?= e($stats['tech_problems']) ?></div>
            </div>
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-500 opacity-70"></div>
        </div>
    </div>

    <!-- Graphique & Top/Flop -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 fade-in-up delay-200">
        
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#39ff14]">monitoring</span> 
                Évolution (7 derniers jours)
            </h3>
            <div class="w-full h-[320px]">
                <canvas id="responsesChart"></canvas>
            </div>
        </div>

        <!-- Rankings -->
        <div class="flex flex-col gap-6">
            
            <!-- Top 5 -->
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden hover:bg-white/10 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#39ff14]/10 rounded-full blur-[50px]"></div>
                <h3 class="text-lg font-bold text-[#39ff14] mb-5 flex items-center gap-2 relative z-10">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">military_tech</span> 
                    Top 5 Véhicules
                </h3>
                <?php if (empty($top5)): ?>
                    <p class="text-white/50 text-sm relative z-10">Pas assez de données.</p>
                <?php else: ?>
                    <ul class="space-y-4 relative z-10">
                        <?php foreach ($top5 as $i => $v): ?>
                            <li class="flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/10 text-white/70 flex items-center justify-center font-bold text-sm group-hover:bg-[#39ff14]/20 group-hover:text-[#39ff14] transition-colors"><?= $i+1 ?></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-white truncate max-w-[120px]"><?= e($v['brand']) ?> <?= e($v['model']) ?></span>
                                        <span class="text-[10px] text-white/50 uppercase tracking-wider"><?= e($v['code']) ?></span>
                                    </div>
                                </div>
                                <div class="bg-[#39ff14]/10 text-[#39ff14] font-bold px-3 py-1 rounded-lg text-sm border border-[#39ff14]/20">
                                    <?= number_format($v['avg_rating'], 1) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Flop 5 -->
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 relative overflow-hidden hover:bg-white/10 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 rounded-full blur-[50px]"></div>
                <h3 class="text-lg font-bold text-red-400 mb-5 flex items-center gap-2 relative z-10">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span> 
                    À surveiller
                </h3>
                <?php if (empty($flop5)): ?>
                    <p class="text-white/50 text-sm relative z-10">Pas assez de données.</p>
                <?php else: ?>
                    <ul class="space-y-4 relative z-10">
                        <?php foreach ($flop5 as $i => $v): ?>
                            <li class="flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/10 text-white/70 flex items-center justify-center font-bold text-sm group-hover:bg-red-500/20 group-hover:text-red-400 transition-colors"><?= $i+1 ?></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-white truncate max-w-[120px]"><?= e($v['brand']) ?> <?= e($v['model']) ?></span>
                                        <span class="text-[10px] text-white/50 uppercase tracking-wider"><?= e($v['code']) ?></span>
                                    </div>
                                </div>
                                <div class="bg-red-500/10 text-red-400 font-bold px-3 py-1 rounded-lg text-sm border border-red-500/20">
                                    <?= number_format($v['avg_rating'], 1) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Derniers Avis -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] overflow-hidden fade-in-up delay-300 relative">
        <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-400">forum</span>
                Derniers avis reçus
            </h2>
            <a href="/admin/responses.php" class="text-white/60 hover:text-white text-sm font-medium hover:underline transition-colors flex items-center gap-1">
                Voir tout <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
        
        <?php if (empty($recentResponses)): ?>
            <div class="p-10 text-center text-white/50">Aucun avis pour le moment.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-white/40 text-xs uppercase tracking-widest bg-black/20">
                            <th class="p-5 font-semibold">Date</th>
                            <th class="p-5 font-semibold">Véhicule</th>
                            <th class="p-5 font-semibold text-center">Note</th>
                            <th class="p-5 font-semibold text-right">Détail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($recentResponses as $response): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5 text-sm text-white/60"><?= e(date('d/m/Y H:i', strtotime($response['created_at']))) ?></td>
                                <td class="p-5">
                                    <div class="font-bold text-white mb-1"><?= e($response['vehicle_code']) ?></div>
                                    <div class="text-xs text-white/50"><?= e($response['brand']) ?> <?= e($response['model']) ?></div>
                                </td>
                                <td class="p-5 text-center">
                                    <?php 
                                        $r = $response['overall_rating'];
                                        $color = $r >= 4 ? 'text-[#39ff14] bg-[#39ff14]/10 border-[#39ff14]/30' : ($r >= 3 ? 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30' : 'text-red-400 bg-red-400/10 border-red-400/30');
                                    ?>
                                    <span class="font-bold inline-block border px-3 py-1 rounded-lg <?= $color ?>"><?= e($r) ?>/5</span>
                                </td>
                                <td class="p-5 text-right">
                                    <a href="/admin/response-view.php?id=<?= e($response['id']) ?>" class="inline-flex items-center justify-center bg-white/5 text-white/70 hover:bg-white/20 hover:text-white rounded-xl p-2 transition-colors border border-white/10 group-hover:border-white/30">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
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
    const ctx = document.getElementById('responsesChart').getContext('2d');
    
    // Gradient for the line chart fill
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(57, 255, 20, 0.2)');
    gradient.addColorStop(1, 'rgba(57, 255, 20, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [
                {
                    label: 'Nombre de réponses',
                    data: <?= json_encode($counts) ?>,
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    backgroundColor: 'rgba(255, 255, 255, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Note moyenne (/5)',
                    data: <?= json_encode($avgs) ?>,
                    borderColor: '#39ff14',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#0B150F',
                    pointBorderColor: '#39ff14',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            color: 'rgba(255, 255, 255, 0.5)',
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: { color: 'rgba(255, 255, 255, 0.7)', font: { family: 'Inter', size: 12 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { color: 'rgba(255, 255, 255, 0.5)' }
                },
                y: { 
                    type: 'linear', 
                    display: true, 
                    position: 'left', 
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { color: 'rgba(255, 255, 255, 0.5)' }
                },
                y1: { 
                    type: 'linear', 
                    display: true, 
                    position: 'right', 
                    min: 0, 
                    max: 5, 
                    grid: { drawOnChartArea: false },
                    ticks: { color: 'rgba(57, 255, 20, 0.8)' }
                }
            }
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>

