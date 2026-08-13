<?php
// admin/vehicles.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Pagination et Filtres
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$brandFilter = $_GET['brand'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Construction de la requête
$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(code LIKE ? OR model LIKE ? OR registration_number LIKE ?)";
    $searchWild = "%$search%";
    array_push($params, $searchWild, $searchWild, $searchWild);
}
if (!empty($brandFilter)) {
    $where[] = "brand = ?";
    $params[] = $brandFilter;
}
if (!empty($statusFilter)) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}

$whereClause = implode(" AND ", $where);

try {
    // Total count
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE $whereClause");
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Fetch vehicles
    $stmt = $pdo->prepare("
        SELECT v.*, 
               COUNT(r.id) as responses_count, 
               AVG(r.overall_rating) as avg_rating
        FROM vehicles v
        LEFT JOIN satisfaction_responses r ON v.id = r.vehicle_id
        WHERE $whereClause
        GROUP BY v.id
        ORDER BY v.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll();
    
    // Fetch brands for filter
    $brands = $pdo->query("SELECT DISTINCT brand FROM vehicles ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

$pageTitle = "Gestion de la flotte - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none fixed">
    <div class="absolute w-[600px] h-[600px] bg-[#39ff14]/5 rounded-full blur-[120px] top-1/4 -right-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-10 -left-20"></div>
</div>

<main class="w-full max-w-[1280px] mx-auto px-4 md:px-8 py-8 relative z-10">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Flotte de véhicules</h1>
            <p class="text-white/60 font-body-lg">Gérez, suivez et analysez votre flotte de location.</p>
        </div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <a href="/admin/print-all-qr.php" target="_blank" class="bg-white/5 text-white border border-white/20 hover:bg-white/10 px-4 py-2 rounded-xl font-label-md transition-colors flex items-center gap-2 backdrop-blur-md">
                <span class="material-symbols-outlined text-[#39ff14]" style="font-variation-settings: 'FILL' 0;">qr_code_2</span>
                Imprimer tous les QR
            </a>
            <a href="/admin/vehicle-create.php" class="bg-white text-black px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition-colors flex items-center gap-2 shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                <span class="material-symbols-outlined">add</span>
                Ajouter un véhicule
            </a>
        </div>
    </div>

    <?= getFlash('success') ?>
    <?= getFlash('error') ?>

    <!-- Filtres -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 shadow-sm mb-8 fade-in-up delay-100">
        <form method="GET" action="" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-white/40 group-focus-within:text-[#39ff14] transition-colors">search</span>
                    <input type="text" name="search" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 font-body-md text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" placeholder="Modèle, code..." value="<?= e($search) ?>">
                </div>
            </div>
            
            <div class="w-[180px]">
                <select name="brand" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] font-label-md">
                    <option value="">Toutes les marques</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?= e($b) ?>" <?= $brandFilter === $b ? 'selected' : '' ?>><?= e($b) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="w-[180px]">
                <select name="status" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] font-label-md">
                    <option value="">Tous les statuts</option>
                    <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Disponible</option>
                    <option value="maintenance" <?= $statusFilter === 'maintenance' ? 'selected' : '' ?>>En maintenance</option>
                    <option value="rented" <?= $statusFilter === 'rented' ? 'selected' : '' ?>>En location</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-white/10 text-white px-6 py-3 rounded-xl font-bold hover:bg-white/20 hover:text-[#39ff14] transition-colors border border-white/10 hover:border-[#39ff14]/50">Filtrer</button>
                <a href="/admin/vehicles.php" class="bg-transparent text-white/50 border border-white/10 px-4 py-3 rounded-xl font-label-md hover:bg-white/5 hover:text-white transition-colors flex items-center justify-center">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] overflow-hidden fade-in-up delay-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/20 text-white/40 font-label-sm uppercase tracking-widest border-b border-white/5">
                        <th class="p-5">Véhicule</th>
                        <th class="p-5">Immatriculation</th>
                        <th class="p-5">Statut</th>
                        <th class="p-5 text-center">QR Code</th>
                        <th class="p-5 text-center">Avis</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($vehicles)): ?>
                        <tr><td colspan="6" class="p-10 text-center text-white/40">Aucun véhicule trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5 flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center shrink-0 group-hover:border-[#39ff14]/30 transition-colors">
                                        <span class="material-symbols-outlined text-white/50 group-hover:text-[#39ff14] transition-colors">directions_car</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-lg leading-tight"><?= e($vehicle['brand']) ?> <?= e($vehicle['model']) ?></div>
                                        <div class="text-xs text-[#39ff14] mt-1 font-mono tracking-widest uppercase bg-[#39ff14]/10 inline-block px-2 py-0.5 rounded border border-[#39ff14]/20"><?= e($vehicle['code']) ?></div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <div class="text-sm font-bold text-white/90"><?= e($vehicle['registration_number'] ?: '-') ?></div>
                                    <div class="text-[10px] text-white/30 tracking-wider">WBA1234567890ABCD</div>
                                </td>
                                <td class="p-5">
                                    <?php
                                    $s = $vehicle['status'];
                                    if ($s === 'available') {
                                        echo '<span class="inline-flex items-center gap-1.5 bg-[#39ff14]/10 text-[#39ff14] border border-[#39ff14]/30 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-[#39ff14] animate-pulse"></span>Dispo</span>';
                                    } elseif ($s === 'maintenance') {
                                        echo '<span class="inline-flex items-center gap-1.5 bg-yellow-400/10 text-yellow-400 border border-yellow-400/30 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>Maint.</span>';
                                    } elseif ($s === 'inactive') {
                                        echo '<span class="inline-flex items-center gap-1.5 bg-red-500/10 text-red-400 border border-red-500/30 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Inactif</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center gap-1.5 bg-gray-500/10 text-gray-400 border border-gray-500/30 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>' . ucfirst($s) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if (file_exists('../qrcodes/' . $vehicle['code'] . '.png')): ?>
                                        <div class="flex items-center justify-center gap-3">
                                            <div class="p-1 border border-white/20 rounded-lg bg-white">
                                                <img src="/qrcodes/<?= e($vehicle['code']) ?>.png" class="w-8 h-8 object-contain" alt="QR">
                                            </div>
                                            <a href="/admin/print-qr.php?code=<?= e($vehicle['code']) ?>" target="_blank" class="text-white/50 hover:text-[#39ff14] transition-colors" title="Imprimer">
                                                <span class="material-symbols-outlined text-[20px]">print</span>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-white/30 text-xs border border-white/10 px-2 py-1 rounded">Manquant</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if ($vehicle['responses_count'] > 0): ?>
                                        <div class="font-bold text-[#39ff14] drop-shadow-[0_0_5px_rgba(57,255,20,0.5)]"><?= number_format($vehicle['avg_rating'], 1) ?>/5</div>
                                        <div class="text-[10px] text-white/50 uppercase tracking-widest mt-0.5"><?= (int)$vehicle['responses_count'] ?> avis</div>
                                    <?php else: ?>
                                        <span class="text-white/20">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="/admin/vehicle-edit.php?id=<?= e($vehicle['id']) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 text-white/70 hover:bg-white/20 hover:text-white transition-colors border border-white/10">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                        <form action="/admin/vehicle-delete.php" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                            <input type="hidden" name="id" value="<?= e($vehicle['id']) ?>">
                                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-colors border border-red-500/20">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
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
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&brand=<?= urlencode($brandFilter) ?>&status=<?= urlencode($statusFilter) ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all <?= $i === $page ? 'bg-white text-black shadow-[0_0_15px_rgba(255,255,255,0.4)]' : 'bg-white/5 border border-white/10 text-white/70 hover:bg-white/20 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

