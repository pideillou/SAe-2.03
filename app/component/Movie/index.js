export const Movie = {
  format: function (films) {
    if (!films || films.length === 0) {
      return `<div class="no-movie-msg">Oups, aucun film n\'est disponible !</div>`;
    }
    return `<div class="movie-grid">
      ${films
        .map(
          (film) => `
        <div class="movie-card" onclick=\"C.handlerDetail(${film.id})\">
          <img src="${film.image ? "../server/images/" + film.image : "https://via.placeholder.com/200x300?text=No+Image"}" alt="${film.name || "Film"}">
          <div class="movie-info">
            <div class="movie-title">${film.name || "Titre inconnu"}</div>
            <div class="movie-desc">${film.description || "Pas de description."}</div>
            <div class="movie-meta">
              <span class="movie-badge">${film.year || "N/A"}</span>
              <span class="movie-play" aria-hidden="true">▶</span>
            </div>
          </div>
        </div>
      `,
        )
        .join("")}
    </div>`;
  },
};
