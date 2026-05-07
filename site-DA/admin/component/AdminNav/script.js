let AdminNav = {
  render: function () {
    let html = `
      <nav class="navbar font-sans">
        <div class="navbar__logo">
          <a class="navbar__brand" href="/" aria-label="Retour accueil">FLOUFLIX</a>
        </div>
        <ul class="navbar__links">
          <li class="navbar__link active">Admin</li>
        </ul>
        <div class="navbar__actions">
          <button
            class="navbar__action navbar__action--logout"
            type="button"
            onclick="C.handlerLogout()"
            aria-label="Déconnexion"
          >
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path d="M16 17v-3H9v-2h7V7l5 5-5 5zM6 6h2v12H6z"/>
            </svg>
            <span>Logout</span>
          </button>
        </div>
      </nav>
    `;
    return html;
  },
};

export { AdminNav };
