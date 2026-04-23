export const MovieDetail = {
  render: function(movie) {
    const el = document.createElement('div');
    el.innerHTML = `
      <div class="movie-detail">
        <img class="movie-detail__image" src="${movie.image ? '../server/images/' + movie.image : 'https://via.placeholder.com/300x450?text=No+Image'}" alt="Affiche du film">
        <div class="movie-detail__info">
          <h2 class="movie-detail__title">${movie.name}</h2>
          <div class="movie-detail__desc">${movie.description || 'Pas de description.'}</div>
          <div class="movie-detail__meta">
            Réalisateur : <span class="movie-detail__director">${movie.director || '-'}</span> |
            Année : <span class="movie-detail__year">${movie.year || '-'}</span> |
            Catégorie : <span class="movie-detail__category">${movie.category || '-'}</span> |
            Âge min : <span class="movie-detail__minage">${movie.min_age || '-'}</span>
          </div>
          <div class="movie-detail__trailer">
            ${movie.trailer ? `<iframe src="${movie.trailer}" allowfullscreen></iframe>` : '<em>Pas de trailer.</em>'}
          </div>
        </div>
      </div>
    `;
    return el.firstElementChild;
  }
};
