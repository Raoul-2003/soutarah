<?php
// admin/responses.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Gérer l'export CSV (Le code reste intact)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmt = $pdo->query("
        SELECT r.*, v.code as vehicle_code, v.brand, v.model
        FROM satisfaction_responses r
        JOIN vehicles v ON r.vehicle_id = v.id
        ORDER BY r.created_at DESC
    ");
    $responses = $stmt->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=satisfaction_soutarah_' . date('Y-m-d') . '.csv');
    
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'Date', 'Véhicule', 'Immatriculation', 'Email',
        'Type (Particulier)', 'Type (Société)', 'Avec Chauffeur',
        'Note Globale', 'Propreté', 'Conformité', 'Problème Tech.',
        'Courtoisie', 'Attente',
        'Chauff. Ponctualité', 'Chauff. Conduite', 'Chauff. Attitude', 'Chauff. Trajets',
        'Commentaire'
    ], ';');
    
    foreach ($responses as $row) {
        fputcsv($output, [
            date('d/m/Y H:i', strtotime($row['created_at'])),
            $row['vehicle_code'] . ' - ' . $row['brand'] . ' ' . $row['model'],
            $row['registration_number'] ?? '',
            $row['email'],
            $row['location_type_particulier'] ?? '',
            $row['location_type_societe'] ?? '',
            $row['with_driver'],
            $row['overall_rating'],
            $row['cleanliness'],
            $row['reservation_compliance'],
            $row['technical_problem'],
            $row['customer_service'],
            $row['waiting_time'],
            $row['driver_punctuality'] ?? '',
            $row['driving_quality'] ?? '',
            $row['driver_attitude'] ?? '',
            $row['route_knowledge'] ?? '',
            $row['comment']
        ], ';');
    }
    fclose($output);
    exit;
}

// Pagination & Filtres
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$minRating = $_GET['min_rating'] ?? '';
$techProb = $_GET['tech_prob'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(v.code LIKE ? OR v.brand LIKE ? OR v.model LIKE ? OR r.email LIKE ?)";
    $searchWild = "%$search%";
    array_push($params, $searchWild, $searchWild, $searchWild, $searchWild);
}
if (!empty($minRating)) {
    $where[] = "r.overall_rating >= ?";
    $params[] = $minRating;
}
if (!empty($techProb)) {
    $where[] = "r.technical_problem = ?";
    $params[] = $techProb;
}

$orderBy = "r.created_at DESC";
if ($sort === 'date_asc') $orderBy = "r.created_at ASC";
if ($sort === 'rating_desc') $orderBy = "r.overall_rating DESC";
if ($sort === 'rating_asc') $orderBy = "r.overall_rating ASC";

$whereClause = implode(" AND ", $where);

try {
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM satisfaction_responses r JOIN vehicles v ON r.vehicle_id = v.id WHERE $whereClause");
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    $stmt = $pdo->prepare("
        SELECT r.*, v.code as vehicle_code, v.brand, v.model
        FROM satisfaction_responses r
        JOIN vehicles v ON r.vehicle_id = v.id
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $responses = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

$pageTitle = "Réponses Client - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none fixed">
    <div class="absolute w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[120px] top-1/3 -left-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-20 -right-20"></div>
</div>

<main class="w-full max-w-[1280px] mx-auto px-4 md:px-8 py-8 relative z-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Réponses Client</h1>
            <p class="text-white/60 font-body-lg">Gérez, filtrez et exportez les avis de satisfaction</p>
        </div>
        <a href="?export=csv" class="mt-4 md:mt-0 bg-[#39ff14]/20 text-[#39ff14] border border-[#39ff14]/50 px-6 py-3 rounded-xl font-bold hover:bg-[#39ff14] hover:text-black transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(57,255,20,0.2)]">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">download</span>
            Exporter CSV
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 shadow-sm mb-8 fade-in-up delay-100">
        <form method="GET" action="" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block font-label-sm text-white/50 uppercase tracking-wider mb-2">Rechercher</label>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-white/40 group-focus-within:text-[#39ff14] transition-colors">search</span>
                    <input type="text" name="search" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 font-body-md text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" placeholder="Véhicule, email..." value="<?= e($search) ?>">
                </div>
            </div>
            <div class="w-[160px]">
                <label class="block font-label-sm text-white/50 uppercase tracking-wider mb-2">Note min.</label>
                <select name="min_rating" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] appearance-none">
                    <option value="">Toutes</option>
                    <option value="4" <?= $minRating === '4' ? 'selected' : '' ?>>>= 4 (Satisfait)</option>
                    <option value="3" <?= $minRating === '3' ? 'selected' : '' ?>>>= 3 (Moyen)</option>
                </select>
            </div>
            <div class="w-[160px]">
                <label class="block font-label-sm text-white/50 uppercase tracking-wider mb-2">Problème Tech.</label>
                <select name="tech_prob" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] appearance-none">
                    <option value="">Tous</option>
                    <option value="Oui" <?= $techProb === 'Oui' ? 'selected' : '' ?>>Signalés (Oui)</option>
                    <option value="Non" <?= $techProb === 'Non' ? 'selected' : '' ?>>Non</option>
                </select>
            </div>
            <div class="w-[180px]">
                <label class="block font-label-sm text-white/50 uppercase tracking-wider mb-2">Trier par</label>
                <select name="sort" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] appearance-none">
                    <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Plus récents</option>
                    <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Plus anciens</option>
                    <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Meilleure note</option>
                    <option value="rating_asc" <?= $sort === 'rating_asc' ? 'selected' : '' ?>>Pire note</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-white/10 text-white px-6 py-3 rounded-xl font-bold hover:bg-white/20 hover:text-[#39ff14] transition-colors border border-white/10 hover:border-[#39ff14]/50">Filtrer</button>
                <a href="/admin/responses.php" class="bg-transparent text-white/50 border border-white/10 px-4 py-3 rounded-xl font-label-md hover:bg-white/5 hover:text-white transition-colors flex items-center justify-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] overflow-hidden fade-in-up delay-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/20 text-white/40 font-label-sm uppercase tracking-widest border-b border-white/5">
                        <th class="p-5">Date</th>
                        <th class="p-5">Véhicule</th>
                        <th class="p-5">Client (Email)</th>
                        <th class="p-5">Chauffeur</th>
                        <th class="p-5 text-center">Note Globale</th>
                        <th class="p-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($responses)): ?>
                        <tr><td colspan="6" class="p-10 text-center text-white/40">Aucun avis trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($responses as $response): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5 text-sm text-white/60 font-medium"><?= e(date('d/m/Y H:i', strtotime($response['created_at']))) ?></td>
                                <td class="p-5">
                                    <div class="font-bold text-white text-lg leading-tight"><?= e($response['vehicle_code']) ?></div>
                                    <div class="text-xs text-white/50"><?= e($response['brand']) ?> <?= e($response['model']) ?></div>
                                </td>
                                <td class="p-5 text-sm text-white/80"><?= e($response['email'] ?: '-') ?></td>
                                <td class="p-5 text-sm">
                                    <?= $response['with_driver'] === 'Oui' ? '<span class="bg-[#39ff14]/10 text-[#39ff14] border border-[#39ff14]/30 px-3 py-1 rounded-lg text-xs font-bold tracking-wider">OUI</span>' : '<span class="bg-white/5 text-white/40 border border-white/10 px-3 py-1 rounded-lg text-xs tracking-wider">NON</span>' ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php 
                                        $r = $response['overall_rating'];
                                        $color = $r >= 4 ? 'text-[#39ff14] bg-[#39ff14]/10 border-[#39ff14]/30' : ($r >= 3 ? 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30' : 'text-red-400 bg-red-400/10 border-red-400/30');
                                    ?>
                                    <span class="font-bold inline-block border px-3 py-1 rounded-lg <?= $color ?>"><?= e($r) ?>/5</span>
                                </td>
                                <td class="p-5 text-right">
                                    <a href="/admin/response-view.php?id=<?= e($response['id']) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 text-white/70 hover:bg-white/20 hover:text-white transition-colors border border-white/10 group-hover:border-[#39ff14]/50 group-hover:text-[#39ff14]">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="p-6 border-t border-white/10 flex justify-center gap-2 bg-black/10">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&min_rating=<?= urlencode($minRating) ?>&tech_prob=<?= urlencode($techProb) ?>&sort=<?= urlencode($sort) ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all <?= $i === $page ? 'bg-white text-black shadow-[0_0_15px_rgba(255,255,255,0.4)]' : 'bg-white/5 border border-white/10 text-white/70 hover:bg-white/20 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

