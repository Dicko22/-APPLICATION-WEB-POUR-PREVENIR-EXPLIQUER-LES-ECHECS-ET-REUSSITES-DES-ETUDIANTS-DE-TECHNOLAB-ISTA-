// ─── Utilitaires généraux ────────────────────────────────────

// Fermer les messages d'alerte après 4 secondes
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-error, .alert-success');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });
});
