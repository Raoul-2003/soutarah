<?php
// admin/response-view.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('/admin/responses.php');
}

try {
    $stmt = $pdo->prepare("
        SELECT r.*, v.code as vehicle_code, v.brand, v.model, v.registration_number
        FROM satisfaction_responses r
        JOIN vehicles v ON r.vehicle_id = v.id
        WHERE r.id = ?
    ");
    $stmt->execute([$id]);
    $response = $stmt->fetch();

    if (!$response) {
        flash('error', 'Réponse introuvable.', 'error');
        redirect('/admin/responses.php');
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Helper pour afficher les étoiles fixes avec effet glow
function renderStars($rating) {
    if (!$rating) return '<span class="text-white/40 text-sm">N/A</span>';
    $rating = (int)$rating;
    $html = '<div class="flex gap-2 justify-center">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $color = $rating >= 4 ? 'text-[#39ff14] drop-shadow-[0_0_8px_rgba(57,255,20,0.8)]' : ($rating >= 3 ? 'text-yellow-400 drop-shadow-[0_0_8px_rgba(250,204,21,0.8)]' : 'text-red-500 drop-shadow-[0_0_8px_rgba(239,68,68,0.8)]');
            $html .= '<span class="material-symbols-outlined text-[32px] ' . $color . '" style="font-variation-settings: \'FILL\' 1;">star</span>';
        } else {
            $html .= '<span class="material-symbols-outlined text-[32px] text-white/20" style="font-variation-settings: \'FILL\' 0;">star</span>';
        }
    }
    $html .= '</div>';
    return $html;
}

$pageTitle = "Détail de l'avis - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none fixed">
    <div class="absolute w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[120px] top-1/4 -right-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-10 -left-20"></div>
</div>

<main class="w-full max-w-[1280px] mx-auto px-4 md:px-8 py-8 relative z-10">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Détail de l'avis #<?= e($response['id']) ?></h1>
            <p class="text-white/60 font-body-lg">Reçu le <?= e(date('d/m/Y à H:i', strtotime($response['created_at']))) ?></p>
        </div>
        <a href="/admin/responses.php" class="mt-4 md:mt-0 bg-white/5 text-white/70 border border-white/10 px-6 py-3 rounded-xl font-bold hover:bg-white/20 hover:text-white transition-colors flex items-center gap-2 backdrop-blur-md">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">arrow_back</span>
            Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 fade-in-up delay-100">
        <!-- Infos Générales -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-8 shadow-sm">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-400">info</span>
                Informations Générales
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                    <span class="text-white/50 font-label-sm uppercase tracking-widest font-semibold">Véhicule</span>
                    <span class="font-bold text-white text-right">
                        <span class="text-lg"><?= e($response['vehicle_code']) ?></span><br>
                        <span class="text-xs font-normal text-white/50 uppercase tracking-widest"><?= e($response['brand']) ?> <?= e($response['model']) ?></span>
                    </span>
                </div>
                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                    <span class="text-white/50 font-label-sm uppercase tracking-widest font-semibold">Email Client</span>
                    <span class="font-bold text-white"><?= e($response['email'] ?: 'Non renseigné') ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                    <span class="text-white/50 font-label-sm uppercase tracking-widest font-semibold">Type Location</span>
                    <span class="text-right flex flex-col gap-2 items-end">
                        <?php if ($response['location_type_particulier']): ?>
                            <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs px-3 py-1 rounded-lg font-bold tracking-wider">PART: <?= e($response['location_type_particulier']) ?></span>
                        <?php endif; ?>
                        <?php if ($response['location_type_societe']): ?>
                            <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs px-3 py-1 rounded-lg font-bold tracking-wider">PRO: <?= e($response['location_type_societe']) ?></span>
                        <?php endif; ?>
                        <?php if (!$response['location_type_particulier'] && !$response['location_type_societe']): ?>
                            <span class="text-white/30">-</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-white/50 font-label-sm uppercase tracking-widest font-semibold">Avec Chauffeur</span>
                    <?= $response['with_driver'] === 'Oui' ? '<span class="bg-[#39ff14]/10 border border-[#39ff14]/30 text-[#39ff14] font-bold px-4 py-1.5 rounded-lg text-sm tracking-widest">OUI</span>' : '<span class="bg-white/5 border border-white/10 text-white/50 px-4 py-1.5 rounded-lg text-sm tracking-widest font-bold">NON</span>' ?>
                </div>
            </div>
        </div>

        <!-- Satisfaction Globale -->
        <?php 
            $or = $response['overall_rating'];
            $orColor = $or >= 4 ? '#39ff14' : ($or >= 3 ? '#facc15' : '#ef4444');
            $orBg = $or >= 4 ? 'bg-[#39ff14]/5' : ($or >= 3 ? 'bg-yellow-400/5' : 'bg-red-500/5');
            $orBorder = $or >= 4 ? 'border-[#39ff14]/30' : ($or >= 3 ? 'border-yellow-400/30' : 'border-red-500/30');
            $orShadow = $or >= 4 ? 'shadow-[0_0_30px_rgba(57,255,20,0.1)]' : ($or >= 3 ? 'shadow-[0_0_30px_rgba(250,204,21,0.1)]' : 'shadow-[0_0_30px_rgba(239,68,68,0.1)]');
        ?>
        <div class="<?= $orBg ?> border <?= $orBorder ?> backdrop-blur-xl rounded-[24px] p-8 <?= $orShadow ?> flex flex-col items-center justify-center text-center relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[200px] opacity-[0.03]" style="font-variation-settings: 'FILL' 1; color: <?= $orColor ?>;">star</span>
            
            <h3 class="text-xl font-bold text-white mb-6 relative z-10 uppercase tracking-widest">Satisfaction Globale</h3>
            <div class="font-black text-8xl leading-none mb-6 relative z-10 drop-shadow-[0_0_20px_<?= str_replace('#', 'rgba(', $orColor) ?>0.5)]" style="color: <?= $orColor ?>;">
                <?= e($or) ?><span class="text-3xl text-white/30 drop-shadow-none">/5</span>
            </div>
            <div class="relative z-10">
                <?= renderStars($or) ?>
            </div>
        </div>
    </div>

    <!-- Commentaire -->
    <?php if (!empty($response['comment'])): ?>
        <div class="bg-white/5 backdrop-blur-xl rounded-[24px] p-8 border border-white/10 mb-10 fade-in-up delay-200 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-blue-500"></div>
            <h3 class="text-xl font-bold text-white flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-blue-400">format_quote</span>
                Commentaire du client
            </h3>
            <p class="text-lg text-white/90 italic whitespace-pre-wrap pl-6 border-l border-white/10 leading-relaxed font-serif">"<?= e($response['comment']) ?>"</p>
        </div>
    <?php endif; ?>

    <!-- Détail des évaluations -->
    <div class="flex items-center gap-4 mb-8 fade-in-up delay-300">
        <h2 class="text-2xl font-black text-white uppercase tracking-widest">Détails des évaluations</h2>
        <div class="flex-1 h-px bg-gradient-to-r from-white/20 to-transparent"></div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 fade-in-up delay-300">
        
        <!-- Véhicule -->
        <div class="bg-white/5 backdrop-blur-xl rounded-[24px] p-6 border border-white/10 hover:bg-white/10 transition-colors">
            <h4 class="text-lg font-bold text-white mb-6 pb-4 border-b border-white/10 flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-400" style="font-variation-settings: 'FILL' 1;">directions_car</span>
                Véhicule
            </h4>
            <div class="space-y-5">
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Propreté à la livraison</span>
                    <strong class="text-white text-lg"><?= e($response['cleanliness'] ?: '-') ?></strong>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Conformité réservation</span>
                    <strong class="text-white text-lg"><?= e($response['reservation_compliance'] ?: '-') ?></strong>
                </div>
                <div class="mt-4 p-4 rounded-xl border <?= $response['technical_problem'] === 'Oui' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-[#39ff14]/10 border-[#39ff14]/30 text-[#39ff14]' ?>">
                    <span class="text-[10px] uppercase tracking-widest block mb-2 opacity-70 font-semibold">Problème technique ?</span>
                    <div class="font-black text-xl flex items-center gap-2 tracking-wider">
                        <span class="material-symbols-outlined"><?= $response['technical_problem'] === 'Oui' ? 'warning' : 'check_circle' ?></span>
                        <?= e($response['technical_problem'] ?: '-') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accueil et Service -->
        <div class="bg-white/5 backdrop-blur-xl rounded-[24px] p-6 border border-white/10 hover:bg-white/10 transition-colors">
            <h4 class="text-lg font-bold text-white mb-6 pb-4 border-b border-white/10 flex items-center gap-2">
                <span class="material-symbols-outlined text-cyan-400" style="font-variation-settings: 'FILL' 1;">support_agent</span>
                Accueil et service
            </h4>
            <div class="space-y-5">
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Courtoisie / Pro</span>
                    <strong class="text-white text-lg"><?= e($response['customer_service'] ?: '-') ?></strong>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Temps d'attente</span>
                    <strong class="text-white text-lg"><?= e($response['waiting_time'] ?: '-') ?></strong>
                </div>
            </div>
        </div>

        <!-- Chauffeur -->
        <?php if ($response['with_driver'] === 'Oui'): ?>
            <div class="bg-white/5 backdrop-blur-xl rounded-[24px] p-6 border border-white/10 hover:bg-white/10 transition-colors">
                <h4 class="text-lg font-bold text-white mb-6 pb-4 border-b border-white/10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-400" style="font-variation-settings: 'FILL' 1;">person</span>
                    Prestation chauffeur
                </h4>
                <div class="space-y-5">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Ponctualité</span>
                        <strong class="text-white text-lg"><?= e($response['driver_punctuality'] ?: '-') ?></strong>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Qualité conduite</span>
                        <strong class="text-white text-lg"><?= e($response['driving_quality'] ?: '-') ?></strong>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Attitude / Présentation</span>
                        <strong class="text-white text-lg"><?= e($response['driver_attitude'] ?: '-') ?></strong>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-white/50 uppercase tracking-widest font-semibold">Connaissance trajets</span>
                        <strong class="text-white text-lg"><?= e($response['route_knowledge'] ?: '-') ?></strong>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white/5 backdrop-blur-xl rounded-[24px] p-6 border border-white/10 flex flex-col items-center justify-center text-center opacity-50">
                <span class="material-symbols-outlined text-[48px] text-white/30 mb-2">person_off</span>
                <span class="text-white/50 font-semibold uppercase tracking-widest text-sm">Pas de chauffeur<br>pour cette location</span>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

