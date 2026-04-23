export const Movie = {
  format: function(films) {
    if (!films || films.length === 0) {
      return `<div class="no-movie-msg">Oups, aucun film n\'est disponible !</div>`;
    }
    // Utilise une grille CSS pour 5 films max par ligne
    return `<div class="movie-grid">
      ${films.map(film => `
        <div class="movie-card" onclick=\"C.handlerDetail(${film.id})\">
          <img src="${film.image ? '../server/images/' + film.image : 'https://via.placeholder.com/200x300?text=No+Image'}" alt="${film.name || 'Film'}">
          <div class="movie-title">${film.name || 'Titre inconnu'}</div>
          <div class="movie-desc">${film.description || 'Pas de description.'}</div>
        </div>
      `).join('')}
    </div>`;
  }
};