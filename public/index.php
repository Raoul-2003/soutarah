<?php
// public/index.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = 'Accueil - SOUTARAH';
$bodyClass = 'bg-[#0f172a] text-white font-sans min-h-screen';
require_once '../includes/header.php';
?>

<!-- Custom CSS for animation -->
<style>
@keyframes spin3d {
    0% { transform: rotateY(0deg) translateY(0); }
    50% { transform: rotateY(180deg) translateY(-15px); }
    100% { transform: rotateY(360deg) translateY(0); }
}
.car-animation {
    animation: spin3d 12s ease-in-out infinite;
    transform-style: preserve-3d;
}
.float-1 { animation: float1 6s ease-in-out infinite; }
.float-2 { animation: float2 8s ease-in-out infinite reverse; }

@keyframes float1 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
@keyframes float2 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-30px); }
}
</style>

<div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-[#0B2117] to-[#04100C]">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute w-[500px] h-[500px] bg-primary/20 rounded-full blur-3xl -top-32 -left-32"></div>
        <div class="absolute w-[400px] h-[400px] bg-[#2ae500]/10 rounded-full blur-3xl bottom-0 right-0"></div>
    </div>
    
    <div class="z-10 text-center px-6 py-12 max-w-lg mx-auto w-full flex flex-col items-center">
        


        <!-- Floating spheres (decoration) like in reference -->
        <div class="absolute top-[25%] left-[10%] w-16 h-16 rounded-full bg-gradient-to-br from-[#64ffda] to-[#00bfa5] shadow-[0_0_30px_rgba(0,191,165,0.4)] float-1"></div>
        <div class="absolute bottom-[35%] right-[15%] w-8 h-8 rounded-full bg-gradient-to-br from-[#64ffda] to-[#00bfa5] shadow-[0_0_20px_rgba(0,191,165,0.4)] float-2"></div>
        <div class="absolute top-[40%] right-[10%] w-4 h-4 rounded-full bg-gradient-to-br from-[#64ffda] to-[#00bfa5] shadow-[0_0_10px_rgba(0,191,165,0.4)] float-1" style="animation-delay: -2s;"></div>

        <!-- Car Image with 3D Rotate Animation -->
        <div class="relative w-full max-w-md mx-auto mb-12 mt-8 flex items-center justify-center perspective-[1000px]">
            <!-- Decorative concentric circles behind car -->
            <div class="absolute inset-0 flex items-center justify-center opacity-30">
                <div class="w-64 h-64 border border-white/20 rounded-full absolute"></div>
                <div class="w-48 h-48 border border-white/20 rounded-full absolute"></div>
                <div class="w-32 h-32 border border-white/20 rounded-full absolute"></div>
            </div>
            
            <img src="assets/img/car.png" alt="Voiture de luxe" class="w-full h-auto object-contain relative z-10 drop-shadow-[0_30px_30px_rgba(0,0,0,0.7)] car-animation">
        </div>
        
        <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4 uppercase leading-none bg-clip-text text-transparent bg-gradient-to-b from-white to-gray-300 text-left w-full" style="font-family: 'Inter', sans-serif;">
            PORTAIL<br>ADMINISTRATEUR
        </h1>
        
        <p class="text-gray-300/80 mb-10 text-sm md:text-base text-left w-full max-w-[280px]">
            Gérez votre flotte de véhicules et consultez les avis de satisfaction de vos clients.
        </p>
        
        <a href="/admin/login.php" class="inline-flex items-center justify-between w-full bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-xl text-white px-6 py-4 rounded-[2rem] transition-all group">
            <span class="font-medium">Connexion</span>
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-white transform group-hover:translate-x-1 transition-transform text-sm">arrow_forward_ios</span>
            </div>
        </a>
    </div>
</div>

<?php 
// Disable standard footer for this splash screen
$hideFooter = true;
// We still include it if it has JS, but hide visuals
require_once '../includes/footer.php'; 
?>

