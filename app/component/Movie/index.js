export const Movie = {
  format: function(films) {
    if (!films || films.length === 0) {
      return `<div class="no-movie-msg">Oups, aucun film n’est disponible !</div>`;
    }
    return films.map(film => `
      <div class="movie-card">
        <img src="${film.image ? film.image : 'https://via.placeholder.com/200x300?text=No+Image'}" alt="${film.name || 'Film'}">
        <div class="movie-overlay">
          <div class="movie-title">${film.name || 'Titre inconnu'}</div>
          <div class="movie-desc">${film.description || 'Pas de description.'}</div>
        </div>
      </div>
    `).join('');
  }
};