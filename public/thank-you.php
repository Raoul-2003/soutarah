<?php
// public/thank-you.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Merci ! - SOUTARAH";
$bodyClass = 'bg-[#0b1711] text-white font-body-md antialiased min-h-screen relative overflow-hidden flex items-center justify-center';
$hideAdminNav = true;
require_once '../includes/header.php';
?>

<!-- Ambient Background -->
<style>
    .bg-thank-you {
        background-color: #0b1711;
        background-image: url('/public/assets/img/car.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
    
    /* Animation for the checkmark */
    .checkmark-circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #39ff14;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    
    .checkmark {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #39ff14;
        stroke-miterlimit: 10;
        margin: 10% auto;
        box-shadow: inset 0px 0px 0px #39ff14;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    }
    
    .checkmark-check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    
    @keyframes stroke {
        100% { stroke-dashoffset: 0; }
    }
    
    @keyframes scale {
        0%, 100% { transform: none; }
        50% { transform: scale3d(1.1, 1.1, 1); }
    }
    
    @keyframes fill {
        100% { box-shadow: inset 0px 0px 0px 30px rgba(57, 255, 20, 0.1); }
    }
</style>

<div class="fixed inset-0 z-0 bg-thank-you opacity-40 pointer-events-none blur-[4px]"></div>
<div class="fixed inset-0 z-0 bg-gradient-to-b from-[#09120e]/80 via-[#09120e]/95 to-[#09120e] pointer-events-none"></div>

<main class="relative z-10 w-full max-w-lg px-4 mx-auto text-center">
    <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-[32px] p-10 shadow-[0_15px_50px_rgba(0,0,0,0.5)] fade-in-up">
        
        <!-- Animated Checkmark -->
        <svg class="checkmark drop-shadow-[0_0_15px_rgba(57,255,20,0.8)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>

        <h1 class="font-display-lg-mobile text-[36px] font-black text-white mb-4 tracking-tight uppercase bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400 fade-in-up delay-100">Merci !</h1>
        
        <p class="font-body-lg text-white/70 mb-8 leading-relaxed fade-in-up delay-200">
            Votre avis a bien été enregistré. <br>Toute l'équipe <strong class="text-[#39ff14] font-bold">SOUTARAH</strong> vous remercie pour votre confiance et espère vous revoir très bientôt.
        </p>

        <div class="fade-in-up delay-300">
            <a href="https://soutarahgroup.ci/" class="inline-flex items-center justify-center gap-2 bg-[#39ff14]/10 text-[#39ff14] border border-[#39ff14]/50 px-8 py-4 rounded-xl font-bold uppercase tracking-wider hover:bg-[#39ff14] hover:text-black transition-all shadow-[0_0_20px_rgba(57,255,20,0.2)] hover:shadow-[0_0_30px_rgba(57,255,20,0.5)]">
                Visiter notre site web
                <span class="material-symbols-outlined text-[20px]">public</span>
            </a>
        </div>
    </div>
</main>

<script>
    // Ajout de confettis pour un effet waouh (avec canvas-confetti via CDN)
    let script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js';
    script.onload = function() {
        setTimeout(() => {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 100, colors: ['#39ff14', '#ffffff', '#0d1a15'] };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount,
                    origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
                }));
                confetti(Object.assign({}, defaults, { particleCount,
                    origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
                }));
            }, 250);
        }, 800); // Trigger exactly when the checkmark animation scales up
    };
    document.head.appendChild(script);
</script>

<?php require_once '../includes/footer.php'; ?>

