<?php
// includes/header.php
$bodyClass = $bodyClass ?? 'bg-surface text-on-surface font-body-md antialiased min-h-screen pb-32';
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $pageTitle ?? 'SOUTARAH GROUP' ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "tertiary-container": "#cedef9",
                        "surface-bright": "#f7f9fb",
                        "on-tertiary-fixed-variant": "#38485d",
                        "surface-container-high": "#e6e8ea",
                        "on-background": "#191c1e",
                        "surface-container-highest": "#e0e3e5",
                        "surface-container": "#eceef0",
                        "secondary": "#565e74",
                        "secondary-fixed-dim": "#bec6e0",
                        "primary": "#106e00",
                        "error": "#ba1a1a",
                        "primary-fixed-dim": "#2ae500",
                        "inverse-surface": "#2d3133",
                        "inverse-primary": "#2ae500",
                        "on-primary-fixed": "#022100",
                        "on-secondary-fixed-variant": "#3f465c",
                        "on-primary-container": "#107100",
                        "tertiary": "#505f76",
                        "outline": "#6b7c63",
                        "tertiary-fixed": "#d3e4fe",
                        "surface-tint": "#106e00",
                        "secondary-container": "#dae2fd",
                        "on-secondary-fixed": "#131b2e",
                        "on-error": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "surface-variant": "#e0e3e5",
                        "primary-fixed": "#79ff5b",
                        "primary-container": "#39ff14",
                        "on-primary-fixed-variant": "#095300",
                        "error-container": "#ffdad6",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "tertiary-fixed-dim": "#b7c8e1",
                        "on-tertiary-container": "#526278",
                        "on-surface-variant": "#3c4b35",
                        "on-secondary": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "on-surface": "#191c1e",
                        "secondary-fixed": "#dae2fd",
                        "background": "#f7f9fb",
                        "on-tertiary-fixed": "#0b1c30",
                        "on-error-container": "#93000a",
                        "outline-variant": "#baccb0",
                        "on-secondary-container": "#5c647a",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "surface": "#f7f9fb"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px",
                        "20px": "20px"
                    },
                    spacing: {
                        "margin-mobile": "16px",
                        "unit": "4px",
                        "stack-xs": "8px",
                        "gutter": "24px",
                        "stack-xl": "64px",
                        "container-max": "1280px",
                        "stack-md": "24px"
                    },
                    fontFamily: {
                        "label-sm": ["Inter"],
                        "body-lg": ["Inter"],
                        "display-lg-mobile": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "display-lg": ["Inter"]
                    },
                    fontSize: {
                        "label-sm": ["12px", { "lineHeight": "1.2", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
                        "display-lg-mobile": ["36px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-md": ["24px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "1.25", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "label-md": ["14px", { "lineHeight": "1.2", "letterSpacing": "0.01em", "fontWeight": "500" }],
                        "display-lg": ["48px", { "lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>

    <style>
        /* Base styles */
        body { min-height: max(884px, 100dvh); }
        
        /* Animations */
        .fade-in-up { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .section-transition { opacity: 0; transform: translateY(20px); animation: fadeUp 0.5s forwards ease-out; }
        
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        
        /* Specific elements */
        .glass-panel { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .glow-hover:hover { box-shadow: 0 0 15px rgba(57, 255, 20, 0.4); }
        .navy-text { color: #0F172A; }
        .bg-navy { background-color: #0F172A; }
        .border-navy { border-color: #0F172A; }
        
        /* Custom radio buttons */
        .radio-custom:checked + div { border-color: #0F172A; background-color: #f8fafc; }
        .radio-custom:checked + div .indicator { background-color: #39ff14; border-color: #0F172A; }
        
        /* Stars */
        .star-rating span { transition: color 0.2s ease, transform 0.2s ease; cursor: pointer; }
        .star-rating span:hover, .star-rating span.active { color: #39ff14; transform: scale(1.1); font-variation-settings: 'FILL' 1; }
        
        /* Checkbox grid specific */
        .checkbox-custom:checked + div { border-color: #0F172A; background-color: #f8fafc; }
        .checkbox-custom:checked + div .indicator { background-color: #39ff14; border-color: #0F172A; }
        
        /* Sweet Alerts for Flash messages */
        .flash-alert {
            animation: slideInDown 0.5s ease-out forwards;
        }
        @keyframes slideInDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="<?= $bodyClass ?>">
<?php if (isAdminLoggedIn() && !isset($hideAdminNav)): ?>
    <!-- Admin Navigation Shell -->
    <header class="fixed top-0 w-full z-50 bg-[#09120e]/80 backdrop-blur-2xl border-b border-white/10 shadow-lg">
        <div class="flex items-center justify-between px-margin-mobile h-16 w-full max-w-container-max mx-auto">
            <div class="flex items-center gap-4">
                <a href="/admin/dashboard.php" class="font-headline-md text-headline-md text-white font-bold tracking-tight flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-[#39ff14] group-hover:drop-shadow-[0_0_10px_rgba(57,255,20,0.8)] transition-all" style="font-variation-settings: 'FILL' 1;">directions_car</span>
                    SOUTARAH
                </a>
            </div>
            <nav class="hidden md:flex gap-4">
                <a href="/admin/dashboard.php" class="text-white/60 hover:text-white transition-colors font-label-md py-2 px-3 rounded-lg hover:bg-white/10 flex items-center gap-2">Dashboard</a>
                <a href="/admin/vehicles.php" class="text-white/60 hover:text-white transition-colors font-label-md py-2 px-3 rounded-lg hover:bg-white/10 flex items-center gap-2">Véhicules</a>
                <a href="/admin/responses.php" class="text-white/60 hover:text-white transition-colors font-label-md py-2 px-3 rounded-lg hover:bg-white/10 flex items-center gap-2">Réponses</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="/admin/logout.php" class="text-white/50 hover:text-red-400 transition-colors flex items-center gap-1 font-label-md py-2 px-3 rounded-lg hover:bg-red-500/10 border border-transparent hover:border-red-500/20">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span class="hidden sm:inline">Déconnexion</span>
                </a>
            </div>
        </div>
    </header>
    <!-- Padding to avoid overlap -->
    <div class="h-20"></div>
<?php endif; ?>

<!-- Affichage des messages flash -->
<?php if(isset($_SESSION['flash'])): ?>
    <div class="fixed top-20 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md px-4 flash-alert">
        <div class="p-4 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] border backdrop-blur-xl <?= $_SESSION['flash']['type'] === 'error' ? 'bg-red-900/40 border-red-500/50 text-red-200' : 'bg-[#39ff14]/10 border-[#39ff14]/30 text-white' ?> flex items-center gap-3 relative overflow-hidden">
            <div class="absolute inset-0 w-1 bg-<?= $_SESSION['flash']['type'] === 'error' ? 'red-500' : '[#39ff14]' ?>"></div>
            <span class="material-symbols-outlined ml-2 text-<?= $_SESSION['flash']['type'] === 'error' ? 'red-400' : '[#39ff14]' ?>" style="font-variation-settings: 'FILL' 1;">
                <?= $_SESSION['flash']['type'] === 'error' ? 'error' : 'check_circle' ?>
            </span>
            <p class="font-body-md font-medium"><?= e($_SESSION['flash']['message']) ?></p>
            <button onclick="this.parentElement.style.display='none'" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

