<?php
// admin/vehicle-create.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

if (isPost()) {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        flash('error', 'Erreur de sécurité CSRF.', 'error');
        redirect('/admin/vehicle-create.php');
    }

    $code = trim($_POST['code'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $registration = trim($_POST['registration_number'] ?? '');
    $status = $_POST['status'] ?? 'available';

    if (empty($code) || empty($brand) || empty($model)) {
        flash('error', 'Veuillez remplir les champs obligatoires (Code, Marque, Modèle).', 'error');
    } else {
        try {
            // Vérifier que le code est unique
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE code = ?");
            $stmt->execute([$code]);
            if ($stmt->fetchColumn() > 0) {
                flash('error', 'Ce code de véhicule existe déjà.', 'error');
            } else {
                $qrToken = generateToken(16);
                
                $stmt = $pdo->prepare("
                    INSERT INTO vehicles (code, brand, model, registration_number, qr_token, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([$code, $brand, $model, $registration, $qrToken, $status]);
                
                // --- GÉNÉRATION DU QR CODE ---
                $vehicleUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/public/satisfaction.php?vehicle=' . urlencode($code);
                $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($vehicleUrl);
                
                $qrDir = '../qrcodes/';
                $qrFile = $qrDir . $code . '.png';
                
                // On tente de télécharger l'image depuis l'API
                $qrContent = @file_get_contents($apiUrl);
                if ($qrContent !== false) {
                    file_put_contents($qrFile, $qrContent);
                }
                
                flash('success', 'Véhicule ajouté avec succès et QR code généré.');
                redirect('/admin/vehicles.php');
            }
        } catch (PDOException $e) {
            flash('error', 'Erreur de base de données : ' . $e->getMessage(), 'error');
        }
    }
}

$pageTitle = "Ajouter un véhicule - SOUTARAH";
$bodyClass = 'bg-[#09120e] text-white font-sans min-h-screen relative overflow-x-hidden pb-32';
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none fixed">
    <div class="absolute w-[600px] h-[600px] bg-[#39ff14]/5 rounded-full blur-[120px] top-1/4 -right-32"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#39ff14]/5 rounded-full blur-[100px] bottom-10 -left-20"></div>
</div>

<main class="w-full max-w-[800px] mx-auto px-4 md:px-8 py-8 relative z-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase leading-none bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 mb-2">Ajouter un véhicule</h1>
            <p class="text-white/60 font-body-lg">Enregistrement d'un nouveau véhicule dans la flotte</p>
        </div>
        <a href="/admin/vehicles.php" class="mt-4 md:mt-0 bg-white/5 text-white/70 border border-white/10 px-6 py-3 rounded-xl font-bold hover:bg-white/20 hover:text-white transition-colors flex items-center gap-2 backdrop-blur-md">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">arrow_back</span>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] p-6 md:p-8 shadow-sm fade-in-up delay-100">
        <?= getFlash('error') ?>
        
        <form action="/admin/vehicle-create.php" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            
            <div class="flex flex-col gap-2">
                <label class="font-label-sm text-white/50 uppercase tracking-wider font-semibold">Code Véhicule (ex: DUS-001) <span class="text-red-500">*</span></label>
                <input type="text" name="code" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl py-4 px-4 font-body-md text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" required value="<?= e($_POST['code'] ?? '') ?>" placeholder="Saisir le code...">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="font-label-sm text-white/50 uppercase tracking-wider font-semibold">Marque <span class="text-red-500">*</span></label>
                    <input type="text" name="brand" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl py-4 px-4 font-body-md text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" required value="<?= e($_POST['brand'] ?? '') ?>" placeholder="Marque...">
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-label-sm text-white/50 uppercase tracking-wider font-semibold">Modèle <span class="text-red-500">*</span></label>
                    <input type="text" name="model" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl py-4 px-4 font-body-md text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" required value="<?= e($_POST['model'] ?? '') ?>" placeholder="Modèle...">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="font-label-sm text-white/50 uppercase tracking-wider font-semibold">Immatriculation</label>
                    <input type="text" name="registration_number" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl py-4 px-4 font-body-md text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" value="<?= e($_POST['registration_number'] ?? '') ?>" placeholder="Plaque...">
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-label-sm text-white/50 uppercase tracking-wider font-semibold">Statut <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="status" class="w-full bg-[#0d1a15] border border-white/10 rounded-xl py-4 px-4 font-body-md text-white focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all appearance-none" required>
                            <option value="available" <?= ($_POST['status'] ?? '') === 'available' ? 'selected' : '' ?>>Disponible</option>
                            <option value="maintenance" <?= ($_POST['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            <option value="rented" <?= ($_POST['status'] ?? '') === 'rented' ? 'selected' : '' ?>>Loué</option>
                            <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-white/30 pointer-events-none">expand_more</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 flex justify-end">
                <button type="submit" class="bg-white text-black px-8 py-4 rounded-xl font-black uppercase tracking-wider hover:bg-[#39ff14] hover:shadow-[0_0_20px_rgba(57,255,20,0.4)] transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">save</span>
                    Enregistrer le véhicule
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

