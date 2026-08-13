<?php
// public/satisfaction.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$code = $_GET['vehicle'] ?? '';

if (empty($code)) {
    die('<div class="min-h-screen flex items-center justify-center bg-[#09120e] text-white"><div class="bg-red-500/20 border border-red-500/50 p-6 rounded-xl shadow-sm text-center max-w-sm">Code véhicule manquant. Veuillez scanner le QR code à nouveau.</div></div>');
}

$vehicle = getVehicleByCode($pdo, $code);

if (!$vehicle || $vehicle['status'] !== 'available') {
    die('<div class="min-h-screen flex items-center justify-center bg-[#09120e] text-white"><div class="bg-red-500/20 border border-red-500/50 p-6 rounded-xl shadow-sm text-center max-w-sm">Véhicule non trouvé ou inactif. Veuillez vérifier le QR code.</div></div>');
}

$bodyClass = 'bg-[#0b1711] text-white font-body-md antialiased min-h-screen pb-32 relative overflow-x-hidden';
$hideAdminNav = true; // Formulaire public, on cache la navbar admin s'il est connecté
require_once '../includes/header.php';
?>

<!-- Ambient Car Background -->
<style>
    .bg-satisfaction-car {
        background-color: #0b1711;
        background-image: url('/soutarah/public/assets/img/car.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>
<div class="fixed inset-0 z-0 bg-satisfaction-car opacity-40 pointer-events-none blur-[2px]"></div>
<div class="fixed inset-0 z-0 bg-gradient-to-b from-[#09120e]/80 via-[#09120e]/95 to-[#09120e] pointer-events-none"></div>

<!-- Top Navigation (Transactional) -->
<header class="fixed top-0 w-full z-50 bg-[#09120e]/80 backdrop-blur-2xl border-b border-white/10 shadow-[0px_4px_20px_rgba(0,0,0,0.5)] transition-all duration-300 ease-in-out">
    <div class="flex items-center justify-between px-margin-mobile h-16 w-full max-w-container-max mx-auto">
        <div class="w-10"></div> <!-- Spacer for centering -->
        <span class="font-headline-md text-headline-md text-white font-bold tracking-tight flex items-center gap-2">
            <span class="material-symbols-outlined text-[#39ff14]" style="font-variation-settings: 'FILL' 1;">directions_car</span>
            SOUTARAH
        </span>
        <div class="w-10"></div> <!-- Spacer for centering -->
    </div>
    <!-- Progress Bar -->
    <div class="w-full bg-white/5 h-1">
        <div id="formProgress" class="bg-[#39ff14] h-full w-[10%] transition-all duration-500 shadow-[0_0_10px_rgba(57,255,20,0.8)]"></div>
    </div>
</header>

<main class="pt-24 px-margin-mobile max-w-lg mx-auto relative z-10">
    <div class="mb-10 text-center section-transition">
        <h1 class="font-display-lg-mobile text-[32px] font-black text-white mb-2 tracking-tight uppercase bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Votre avis compte</h1>
        <p class="font-body-md text-white/60">Aidez-nous à améliorer nos services (* Obligatoire)</p>
    </div>

    <!-- Vehicle Card -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[20px] p-5 shadow-[0_8px_32px_rgba(0,0,0,0.3)] mb-10 section-transition delay-100 flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-black/40 flex items-center justify-center shrink-0 border border-white/10">
            <span class="material-symbols-outlined text-[32px] text-[#39ff14]" style="font-variation-settings: 'FILL' 1;">directions_car</span>
        </div>
        <div>
            <div class="font-label-sm text-white/50 uppercase tracking-widest mb-1 font-semibold">Véhicule loué</div>
            <h2 class="text-xl font-bold text-white"><?= e($vehicle['brand']) ?> <?= e($vehicle['model']) ?></h2>
            <div class="text-sm text-[#39ff14] mt-1 font-mono tracking-widest uppercase bg-[#39ff14]/10 inline-block px-2 py-0.5 rounded border border-[#39ff14]/20"><?= e($vehicle['code']) ?></div>
        </div>
    </div>

    <form action="/soutarah/public/submit.php" method="POST" id="satisfactionForm" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="vehicle_code" value="<?= e($vehicle['code']) ?>">

        <!-- Email -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition delay-200">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Adresse e-mail <span class="text-red-500">*</span></h3>
            <input type="email" name="email" required onchange="updateProgress()" class="w-full bg-black/40 border border-white/10 rounded-xl p-4 font-body-lg text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all" placeholder="votre@email.com">
        </div>

        <!-- Location avec chauffeur -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition delay-300">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Location avec chauffeur ? <span class="text-red-500">*</span></h3>
            <div class="space-y-3">
                <label class="block relative cursor-pointer group">
                    <input class="sr-only radio-custom" type="radio" name="with_driver" value="Oui" required onchange="toggleDriver(true); updateProgress()">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                        <span class="font-bold text-white">Oui</span>
                        <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                    </div>
                </label>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only radio-custom" type="radio" name="with_driver" value="Non" required onchange="toggleDriver(false); updateProgress()">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                        <span class="font-bold text-white">Non</span>
                        <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Type Location -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition delay-400">
            <h3 class="font-headline-md text-lg font-bold text-white mb-5">Quel est votre type de location ?</h3>
            
            <div class="mb-5">
                <h4 class="font-label-sm text-[#39ff14] uppercase tracking-widest mb-3 font-semibold">Particulier</h4>
                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_particulier" value="Loisirs" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Loisirs</span></label>
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_particulier" value="Déplacement d'affaires" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Déplacement d'affaires</span></label>
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_particulier" value="Société" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Société</span></label>
                </div>
            </div>
            
            <div class="pt-5 border-t border-white/10">
                <h4 class="font-label-sm text-[#39ff14] uppercase tracking-widest mb-3 font-semibold">Société</h4>
                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_societe" value="Loisirs" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Loisirs</span></label>
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_societe" value="Déplacement d'affaires" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Déplacement d'affaires</span></label>
                    <label class="flex items-center gap-3 cursor-pointer group"><input type="radio" name="location_type_societe" value="Société" class="w-5 h-5 accent-[#39ff14] cursor-pointer"> <span class="text-white/80 group-hover:text-white transition-colors">Société</span></label>
                </div>
            </div>
        </div>

        <div class="pt-6 pb-2 text-center relative">
            <h2 class="text-2xl font-black text-white uppercase tracking-wider relative z-10">L'État et la Qualité</h2>
            <div class="absolute top-1/2 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        </div>

        <!-- Propreté -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Propreté du véhicule à la livraison ?</h3>
            <div class="space-y-3">
                <?php foreach(['Sale', 'Propre', 'Très propre'] as $val): ?>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only checkbox-custom" type="checkbox" name="cleanliness[]" value="<?= $val ?>">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center gap-4 transition-colors group-hover:border-[#39ff14]/50">
                        <div class="w-6 h-6 rounded-md border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                        <span class="font-bold text-white"><?= $val ?></span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Conformité -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Véhicule conforme à votre réservation ?</h3>
            <div class="space-y-3">
                <?php foreach(['Oui', 'Non'] as $val): ?>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only radio-custom" type="radio" name="reservation_compliance" value="<?= $val ?>">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                        <span class="font-bold text-white"><?= $val ?></span>
                        <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Problème Tech -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Problème technique durant la location ?</h3>
            <div class="space-y-3">
                <?php foreach(['Oui', 'Non'] as $val): ?>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only radio-custom" type="radio" name="technical_problem" value="<?= $val ?>">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                        <span class="font-bold text-white"><?= $val ?></span>
                        <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pt-6 pb-2 text-center relative">
            <h2 class="text-2xl font-black text-white uppercase tracking-wider relative z-10">L'Accueil</h2>
            <div class="absolute top-1/2 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        </div>

        <!-- Courtoisie -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Courtoisie et professionnalisme de l'équipe ?</h3>
            <div class="space-y-3">
                <?php foreach(['Pas du tout satisfaisant', 'Satisfaisant', 'Très satisfaisant'] as $val): ?>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only checkbox-custom" type="checkbox" name="customer_service[]" value="<?= $val ?>">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center gap-4 transition-colors group-hover:border-[#39ff14]/50">
                        <div class="w-6 h-6 rounded-md border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                        <span class="font-bold text-white"><?= $val ?></span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Attente -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Temps d'attente pour la remise des clés ?</h3>
            <div class="space-y-3">
                <?php foreach(['Pas du tout satisfaisant', 'Satisfaisant', 'Très satisfaisant'] as $val): ?>
                <label class="block relative cursor-pointer group">
                    <input class="sr-only radio-custom" type="radio" name="waiting_time" value="<?= $val ?>">
                    <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                        <span class="font-bold text-white"><?= $val ?></span>
                        <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sections Chauffeur (Hidden by default) -->
        <div id="driver-sections" style="display: none;" class="space-y-6">
            <div class="pt-6 pb-2 text-center relative">
                <h2 class="text-2xl font-black text-white uppercase tracking-wider relative z-10">Le Chauffeur</h2>
                <div class="absolute top-1/2 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg">
                <h3 class="font-headline-md text-lg font-bold text-white mb-4">Ponctualité pour la prise en charge ?</h3>
                <div class="space-y-3">
                    <?php foreach(['Parfaitement ponctuel', 'Léger retard', 'Retard important'] as $val): ?>
                    <label class="block relative cursor-pointer group">
                        <input class="sr-only checkbox-custom" type="checkbox" name="driver_punctuality[]" value="<?= $val ?>">
                        <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center gap-4 transition-colors group-hover:border-[#39ff14]/50">
                            <div class="w-6 h-6 rounded-md border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                            <span class="font-bold text-white"><?= $val ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg">
                <h3 class="font-headline-md text-lg font-bold text-white mb-4">Conduite du chauffeur sur la route ?</h3>
                <div class="space-y-3">
                    <?php foreach(['Excellente', 'Correcte', 'Inquiétante'] as $val): ?>
                    <label class="block relative cursor-pointer group">
                        <input class="sr-only radio-custom" type="radio" name="driving_quality" value="<?= $val ?>">
                        <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                            <span class="font-bold text-white"><?= $val ?></span>
                            <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg">
                <h3 class="font-headline-md text-lg font-bold text-white mb-4">Attitude et présentation du chauffeur ?</h3>
                <div class="space-y-3">
                    <?php foreach(['Très professionnel', 'Satisfaisant', 'Peu professionnel'] as $val): ?>
                    <label class="block relative cursor-pointer group">
                        <input class="sr-only radio-custom" type="radio" name="driver_attitude" value="<?= $val ?>">
                        <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center justify-between transition-colors group-hover:border-[#39ff14]/50">
                            <span class="font-bold text-white"><?= $val ?></span>
                            <div class="w-6 h-6 rounded-full border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg">
                <h3 class="font-headline-md text-lg font-bold text-white mb-4">Connaissance des trajets et du trafic ?</h3>
                <div class="space-y-3">
                    <?php foreach(['Parfaite', 'Moyenne', 'Insuffisante'] as $val): ?>
                    <label class="block relative cursor-pointer group">
                        <input class="sr-only checkbox-custom" type="checkbox" name="route_knowledge[]" value="<?= $val ?>">
                        <div class="border border-white/10 bg-black/20 rounded-xl p-4 flex items-center gap-4 transition-colors group-hover:border-[#39ff14]/50">
                            <div class="w-6 h-6 rounded-md border-2 border-white/30 flex items-center justify-center indicator transition-colors"></div>
                            <span class="font-bold text-white"><?= $val ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Note globale -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-8 shadow-[0_0_30px_rgba(57,255,20,0.1)] section-transition">
            <h3 class="text-2xl font-black text-white mb-6 text-center tracking-tight">Comment évaluez-vous nos services ? <span class="text-red-500">*</span></h3>
            
            <input type="radio" name="overall_rating" value="1" id="rate-1" class="sr-only" required onchange="updateProgress()">
            <input type="radio" name="overall_rating" value="2" id="rate-2" class="sr-only" required onchange="updateProgress()">
            <input type="radio" name="overall_rating" value="3" id="rate-3" class="sr-only" required onchange="updateProgress()">
            <input type="radio" name="overall_rating" value="4" id="rate-4" class="sr-only" required onchange="updateProgress()">
            <input type="radio" name="overall_rating" value="5" id="rate-5" class="sr-only" required onchange="updateProgress()">
            
            <div class="flex justify-center gap-3 sm:gap-6 star-rating text-white/20 text-5xl mb-2">
                <label for="rate-1" class="flex flex-col items-center cursor-pointer group hover:scale-110 transition-transform"><span class="text-white/40 font-bold text-sm mb-2">1</span><span class="material-symbols-outlined text-[48px] star-icon drop-shadow-md" data-val="1">star</span></label>
                <label for="rate-2" class="flex flex-col items-center cursor-pointer group hover:scale-110 transition-transform"><span class="text-white/40 font-bold text-sm mb-2">2</span><span class="material-symbols-outlined text-[48px] star-icon drop-shadow-md" data-val="2">star</span></label>
                <label for="rate-3" class="flex flex-col items-center cursor-pointer group hover:scale-110 transition-transform"><span class="text-white/40 font-bold text-sm mb-2">3</span><span class="material-symbols-outlined text-[48px] star-icon drop-shadow-md" data-val="3">star</span></label>
                <label for="rate-4" class="flex flex-col items-center cursor-pointer group hover:scale-110 transition-transform"><span class="text-white/40 font-bold text-sm mb-2">4</span><span class="material-symbols-outlined text-[48px] star-icon drop-shadow-md" data-val="4">star</span></label>
                <label for="rate-5" class="flex flex-col items-center cursor-pointer group hover:scale-110 transition-transform"><span class="text-white/40 font-bold text-sm mb-2">5</span><span class="material-symbols-outlined text-[48px] star-icon drop-shadow-md" data-val="5">star</span></label>
            </div>
        </div>

        <!-- Commentaire -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[24px] p-6 shadow-lg section-transition">
            <h3 class="font-headline-md text-lg font-bold text-white mb-4">Commentaire (facultatif)</h3>
            <textarea name="comment" class="w-full bg-black/40 border border-white/10 rounded-xl p-4 font-body-lg text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-1 focus:ring-[#39ff14] transition-all min-h-[120px]" placeholder="Laissez-nous un commentaire..."></textarea>
        </div>

        <!-- Sticky Footer for Submit -->
        <div class="fixed bottom-0 left-0 w-full bg-[#09120e]/80 backdrop-blur-2xl border-t border-white/10 p-4 pb-safe z-40">
            <div class="max-w-lg mx-auto">
                <button type="submit" class="w-full bg-white text-black font-black uppercase tracking-wider text-[18px] py-4 rounded-xl shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:bg-[#39ff14] hover:shadow-[0_0_30px_rgba(57,255,20,0.5)] transition-all flex items-center justify-center gap-2 group">
                    Soumettre mon avis
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform" data-icon="send">send</span>
                </button>
            </div>
        </div>
    </form>
</main>

<style>
    /* Custom CSS to override the previous style.css defaults for this specific page */
    .radio-custom:checked + div { border-color: #39ff14; background-color: rgba(57, 255, 20, 0.1); }
    .radio-custom:checked + div .indicator { background-color: #39ff14; border-color: #39ff14; box-shadow: 0 0 10px rgba(57, 255, 20, 0.5); }
    
    .checkbox-custom:checked + div { border-color: #39ff14; background-color: rgba(57, 255, 20, 0.1); }
    .checkbox-custom:checked + div .indicator { background-color: #39ff14; border-color: #39ff14; box-shadow: 0 0 10px rgba(57, 255, 20, 0.5); }
</style>

<script>
    function toggleDriver(show) {
        const sections = document.getElementById('driver-sections');
        if (show) {
            sections.style.display = 'block';
            sections.classList.add('fade-in-up');
        } else {
            sections.style.display = 'none';
        }
    }

    // Star rating JS Logic
    const starIcons = document.querySelectorAll('.star-icon');
    const radioInputs = document.querySelectorAll('input[name="overall_rating"]');
    
    radioInputs.forEach(radio => {
        radio.addEventListener('change', () => {
            const val = parseInt(radio.value);
            starIcons.forEach(icon => {
                const iconVal = parseInt(icon.getAttribute('data-val'));
                if (iconVal <= val) {
                    icon.classList.add('active');
                    icon.style.color = '#39ff14';
                    icon.style.fontVariationSettings = "'FILL' 1";
                    icon.style.textShadow = "0 0 15px rgba(57, 255, 20, 0.5)";
                } else {
                    icon.classList.remove('active');
                    icon.style.color = '';
                    icon.style.fontVariationSettings = "'FILL' 0";
                    icon.style.textShadow = "none";
                }
            });
        });
    });

    // Progress bar Logic
    function updateProgress() {
        const form = document.getElementById('satisfactionForm');
        const requiredInputs = form.querySelectorAll('input[required]');
        
        const uniqueNames = new Set();
        requiredInputs.forEach(input => uniqueNames.add(input.name));
        const totalRequired = uniqueNames.size;
        
        const filledNames = new Set();
        requiredInputs.forEach(input => {
            if ((input.type === 'radio' && input.checked) || (input.type !== 'radio' && input.value.trim() !== '')) {
                filledNames.add(input.name);
            }
        });
        
        const percent = Math.max(10, (filledNames.size / totalRequired) * 100);
        document.getElementById('formProgress').style.width = percent + '%';
        
        if (percent === 100) {
            document.getElementById('formProgress').style.backgroundColor = '#39ff14'; // success
            document.getElementById('formProgress').style.boxShadow = '0 0 15px rgba(57,255,20,0.8)';
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>
