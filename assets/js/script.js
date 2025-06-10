// Počkejte, dokud se dokument úplně nenahraje
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    var loadingOverlay = document.getElementById("loading-overlay");
    var footerText = document.getElementById("footer-text");

    if (loadingOverlay) loadingOverlay.style.display = "none";

    if (footerText) {
      window.addEventListener("load", function () {
        footerText.classList.add("visible");
      });
    }
  }, 1000);

  document.querySelectorAll(".popup-trigger").forEach((button) => {
    button.addEventListener("click", function () {
      const link = this.getAttribute("data-link");
      const overlay = document.getElementById("popupOverlay");
      const iframe = document.getElementById("popupIframe");

      if (overlay && iframe && link) {
        iframe.src = link;
        overlay.style.display = "flex";
      }
    });
  });

  const popupClose = document.getElementById("popupClose");
  const popupOverlay = document.getElementById("popupOverlay");
  const popupIframe = document.getElementById("popupIframe");

  if (popupClose) {
    popupClose.addEventListener("click", function () {
      if (popupOverlay && popupIframe) {
        popupIframe.src = "";
        popupOverlay.style.display = "none";
        location.reload();
      }
    });
  }

  if (popupOverlay) {
    popupOverlay.addEventListener("click", function () {
      if (popupIframe) {
        popupIframe.src = "";
      }
      popupOverlay.style.display = "none";
      location.reload();
    });
  }

  window.closeAllMenus = function () {
    const userDropdown = document.getElementById("userDropdown");
    const mobileMenu = document.getElementById("mobileMenu");
    const overlay = document.getElementById("overlay");
    if (userDropdown) userDropdown.style.display = "none";
    if (mobileMenu) mobileMenu.style.display = "none";
    if (overlay) overlay.style.display = "none";
  };

  window.toggleUserMenu = function (event) {
    const userDropdown = document.getElementById("userDropdown");
    const overlay = document.getElementById("overlay");
    if (!userDropdown || !overlay) return;
    event.stopPropagation();
    closeAllMenus();
    userDropdown.style.display =
      userDropdown.style.display === "block" ? "none" : "block";
    overlay.style.display =
      userDropdown.style.display === "block" ? "block" : "none";
  };

  window.toggleMobileMenu = function (event) {
    const mobileMenu = document.getElementById("mobileMenu");
    const overlay = document.getElementById("overlay");
    if (!mobileMenu || !overlay) return;
    event.stopPropagation();
    closeAllMenus();
    mobileMenu.style.display =
      mobileMenu.style.display === "flex" ? "none" : "flex";
    overlay.style.display =
      mobileMenu.style.display === "flex" ? "block" : "none";
  };

  document.addEventListener("click", (event) => {
    if (!event.target.closest("nav")) {
      closeAllMenus();
    }
  });

  window.addEventListener("resize", () => {
    const mobileMenu = document.getElementById("mobileMenu");
    const overlay = document.getElementById("overlay");
    if (window.innerWidth > 768) {
      if (mobileMenu) mobileMenu.style.display = "none";
      if (overlay) overlay.style.display = "none";
    }
  });
});

function removeQueryString() {
  const url = new URL(window.location.href);
  const params = new URLSearchParams(url.search);

  // Odstranit nežádoucí parametry
  params.delete('message');
  params.delete('message_type');

  // Vytvořit novou URL
  const newQuery = params.toString();
  const newUrl = `${url.origin}${url.pathname}${newQuery ? '?' + newQuery : ''}`;

  if (newUrl !== window.location.href) {
    window.history.pushState({}, document.title, newUrl);
    location.reload();
  }
}


function downloadAndRedirect(id) {
  window.location.href = "./soubor.php?id=" + id;
}

window.dataLayer = window.dataLayer || [];
function gtag() {
  dataLayer.push(arguments);
}
gtag("js", new Date());
gtag("config", "G-3BL123NWSE");
