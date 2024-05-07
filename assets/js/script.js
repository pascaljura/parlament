document.addEventListener("DOMContentLoaded", function () {
    // Počkejte, dokud se dokument úplně nenahraje
    setTimeout(function () {
        var loadingOverlay = document.getElementById('loading-overlay');
        var footerText = document.getElementById('footer-text');

        // Ověřte, zda elementy existují, než s nimi manipulujete
        if (loadingOverlay && footerText) {
            loadingOverlay.style.display = 'none';
            window.addEventListener('load', function() {
                footerText.classList.add('visible');
            });
        }
    }, 1000);
});

function downloadAndRedirect(id) {
    // Přesměrování na soubor.php s ID
    window.location.href = "./soubor.php?id=" + id;
}


