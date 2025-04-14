document.addEventListener("DOMContentLoaded", function () {
  // Počkejte, dokud se dokument úplně nenahraje
  setTimeout(function () {
    var loadingOverlay = document.getElementById("loading-overlay");
    var footerText = document.getElementById("footer-text");

    // Ověřte, zda elementy existují, než s nimi manipulujete
    if (loadingOverlay && footerText) {
      loadingOverlay.style.display = "none";
      window.addEventListener("load", function () {
        footerText.classList.add("visible");
      });
    }
  }, 1000);
});
document.querySelectorAll(".popup-trigger").forEach((button) => {
  button.addEventListener("click", function () {
    const link = this.getAttribute("data-link");
    const overlay = document.getElementById("popupOverlay");
    const iframe = document.getElementById("popupIframe");
    iframe.src = link;
    overlay.style.display = "flex";
  });
});

document.getElementById("popupClose").addEventListener("click", function () {
  const overlay = document.getElementById("popupOverlay");
  const iframe = document.getElementById("popupIframe");
  iframe.src = "";
  overlay.style.display = "none";
});
document.getElementById("popupOverlay").addEventListener("click", function () {
  const overlay = document.getElementById("popupOverlay");
  const iframe = document.getElementById("popupIframe");
  iframe.src = "";
  overlay.style.display = "none";
});
function downloadAndRedirect(id) {
  // Přesměrování na soubor.php s ID
  window.location.href = "./soubor.php?id=" + id;
}
const userDropdown = document.getElementById("userDropdown");
const mobileMenu = document.getElementById("mobileMenu");
const overlay = document.getElementById("overlay");

function toggleUserMenu(event) {
  event.stopPropagation();
  closeAllMenus();
  userDropdown.style.display =
    userDropdown.style.display === "block" ? "none" : "block";
  overlay.style.display =
    userDropdown.style.display === "block" ? "block" : "none"; // Zobrazení overlay
}

function toggleMobileMenu(event) {
  event.stopPropagation();
  closeAllMenus();
  mobileMenu.style.display =
    mobileMenu.style.display === "flex" ? "none" : "flex";
  overlay.style.display =
    mobileMenu.style.display === "flex" ? "block" : "none"; // Zobrazení overlay
}

function closeAllMenus() {
  userDropdown.style.display = "none";
  mobileMenu.style.display = "none";
  overlay.style.display = "none"; // Skrytí overlay
}

// Zavření při kliknutí mimo menu
document.addEventListener("click", (event) => {
  if (!event.target.closest("nav")) {
    closeAllMenus();
  }
});
function removeQueryString() {
  // Zkontrolujeme, jestli URL obsahuje otazník
  if (window.location.href.indexOf("?") > -1) {
    // Získáme část URL před otazníkem
    const newUrl = window.location.href.split("?")[0];

    // Nastavíme novou URL bez query stringu
    window.history.pushState({}, document.title, newUrl);

    // Obnovíme stránku, aby se URL aktualizovala
    location.reload();
  }
}
// Zavření při resize (pro jistotu)
window.addEventListener("resize", () => {
  if (window.innerWidth > 768) {
    mobileMenu.style.display = "none";
    overlay.style.display = "none"; // Skrytí overlay při změně velikosti
  }
});
window.dataLayer = window.dataLayer || [];
function gtag() {
  dataLayer.push(arguments);
}
gtag("js", new Date());

gtag("config", "G-3BL123NWSE");
