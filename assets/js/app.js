// assets/js/app.js

document.addEventListener('DOMContentLoaded', function() {
    // Confirmation avant suppression
    const deleteForms = document.querySelectorAll('form[action*="delete"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // Optionnel : Menu burger pour mobile dans l'admin
    const navToggle = document.querySelector('.nav-toggle');
    const adminNav = document.querySelector('.admin-nav');
    
    if (navToggle && adminNav) {
        navToggle.addEventListener('click', () => {
            const isVisible = adminNav.style.display === 'flex';
            adminNav.style.display = isVisible ? 'none' : 'flex';
        });
    }
});

// Fonction pour imprimer uniquement le QR Code
function printQR(imageUrl, code) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - ${code}</title>
                <style>
                    body { text-align: center; font-family: sans-serif; padding-top: 50px; }
                    img { max-width: 300px; height: auto; }
                    h1 { font-size: 24px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <img src="${imageUrl}" onload="window.print();window.close();">
                <h1>${code}</h1>
            </body>
        </html>
    `);
    printWindow.document.close();
}
