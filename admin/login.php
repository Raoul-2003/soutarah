<?php
// admin/login.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (isAdminLoggedIn()) {
    redirect('/admin/dashboard.php');
}

$bodyClass = 'bg-luxury-car text-on-background min-h-screen flex items-center justify-center p-margin-mobile relative overflow-hidden';
$hideAdminNav = true;
require_once '../includes/header.php';
?>

<style>
    .bg-luxury-car {
        background-color: #0B2117;
        background-image: url('../public/assets/img/car.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<!-- Dark Overlay to ensure form readability -->
<div class="absolute inset-0 z-0 bg-black/50 backdrop-blur-[2px] pointer-events-none"></div>

<!-- Login Container -->
<main class="w-full max-w-md relative z-10">
    <!-- Glassmorphism Card (Flou amélioré) -->
    <div class="bg-white/10 backdrop-blur-2xl border border-white/20 shadow-[0px_8px_32px_rgba(0,0,0,0.5)] rounded-[20px] p-stack-xl flex flex-col gap-stack-md transition-all duration-300">
        
        <!-- Header Section -->
        <header class="text-center flex flex-col gap-stack-xs items-center">
            <!-- Logo Image -->
            <div class="w-32 h-32 mb-2 flex items-center justify-center">
                <img src="../assets/images/logo.png" alt="SOUTARAH Logo" class="w-full h-full object-contain drop-shadow-md">
            </div>
            <h1 class="font-headline-lg text-headline-lg md:font-display-lg-mobile md:text-display-lg-mobile text-white tracking-tight">
                SOUTARAH
            </h1>
            <p class="font-body-md text-body-md text-white/80">
                Portail d'accès administrateur
            </p>
        </header>
        
        <?= getFlash('error') ?>
        
        <!-- Form Section -->
        <form action="/admin/authenticate.php" method="POST" class="flex flex-col gap-stack-md mt-stack-xs">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            
            <!-- Input Group: Email -->
            <div class="flex flex-col gap-unit">
                <label class="font-label-sm text-label-sm text-white/90 uppercase tracking-wider pl-1" for="email">
                    Identifiant Administrateur (Email)
                </label>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-white/50 group-focus-within:text-[#39ff14] transition-colors">badge</span>
                    <input name="email" id="email" type="email" required autofocus class="w-full bg-white/10 border border-white/20 rounded-[12px] py-3 pl-12 pr-4 font-body-md text-body-md text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-4 focus:ring-[#39ff14]/30 transition-all duration-200" placeholder="admin@soutarah.com">
                </div>
            </div>
            
            <!-- Input Group: Password -->
            <div class="flex flex-col gap-unit">
                <div class="flex justify-between items-center pl-1 pr-1">
                    <label class="font-label-sm text-label-sm text-white/90 uppercase tracking-wider" for="password">
                        Clé de sécurité (Mot de passe)
                    </label>
                </div>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-white/50 group-focus-within:text-[#39ff14] transition-colors">lock</span>
                    <input name="password" id="password" type="password" required class="w-full bg-white/10 border border-white/20 rounded-[12px] py-3 pl-12 pr-12 font-body-md text-body-md text-white placeholder:text-white/30 focus:outline-none focus:border-[#39ff14] focus:ring-4 focus:ring-[#39ff14]/30 transition-all duration-200" placeholder="••••••••••••">
                </div>
            </div>
            
            <!-- Action Area -->
            <div class="mt-stack-xs flex flex-col gap-stack-xs">
                <button type="submit" class="w-full bg-white text-black py-4 rounded-[12px] font-label-md text-label-md flex items-center justify-center gap-2 transition-all duration-300 hover:bg-gray-200 hover:shadow-[0_0_20px_rgba(255,255,255,0.4)] active:scale-[0.98] group relative overflow-hidden">
                    <span class="relative z-10 flex items-center gap-2 font-bold">
                        S'authentifier
                        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </span>
                    <div class="absolute inset-0 bg-[#39ff14]/10 opacity-0 group-hover:opacity-100 transition-opacity z-0"></div>
                </button>
                <p class="text-center font-label-sm text-label-sm text-white/60 flex items-center justify-center gap-1 mt-unit">
                    <span class="material-symbols-outlined text-[14px]">encrypted</span>
                    Session chiffrée de bout en bout
                </p>
            </div>
        </form>
    </div>
    
    <!-- Footer Info -->
    <div class="mt-stack-md text-center">
        <p class="font-label-sm text-label-sm text-white/60">
            © <?= date('Y') ?> SOUTARAH GROUP. Personnel autorisé uniquement.
        </p>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

