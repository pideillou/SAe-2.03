let templateFile = await fetch("./component/Favorites/template.html");
let template = await templateFile.text();

let Favorites = {};

Favorites.formatSimple = function (favorites) {
  if (!favorites || favorites.length === 0) {
    return '<div style="color:#999999;text-align:center;padding:2rem;">Vous n\'avez aucun film dans vos favoris pour le moment.</div>';
  }

  let html = '<section class="movie-category" style="margin-bottom: 2rem;">';
  html +=
    '<h2 class="movie-category__title">Films dans mes favoris <span style="color: #999999; font-size: 0.9rem;">(' +
    favorites.length +
    " titres)</span></h2>";

  let movieGrid =
    '<div class="movie-carousel">' +
    '<button class="movie-carousel__arrow movie-carousel__arrow--left" type="button" aria-label="Defiler vers la gauche" onclick="C.scrollCarousel(this, -1)">' +
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.7 5.3a1 1 0 0 1 0 1.4L10.4 11l4.3 4.3a1 1 0 1 1-1.4 1.4l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 0z"/></svg>' +
    "</button>" +
    '<div class="movie-grid">';
  for (const movie of favorites) {
    movieGrid += `
      <article class="movie-card" onclick="C.handlerDetail(${movie.id})">
        <img src="${movie.image ? "../server/images/" + movie.image : "https://via.placeholder.com/200x300?text=No+Image"}" alt="${movie.name}" />
        <div class="movie-info">
          <div class="movie-title">${movie.name}</div>
          <div class="movie-desc">${movie.description || "Pas de description."}</div>
          <div class="movie-meta">
            <span class="movie-badge">${movie.year_movie || "N/A"}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="movie-play" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M19.266 13.516a1.917 1.917 0 0 0 0-3.032A35.8 35.8 0 0 0 9.35 5.068l-.653-.232c-1.248-.443-2.567.401-2.736 1.69a42.5 42.5 0 0 0 0 10.948c.17 1.289 1.488 2.133 2.736 1.69l.653-.232a35.8 35.8 0 0 0 9.916-5.416" />
            </svg>
          </div>
        </div>
        <button class="movie-favorite movie-favorite--active" onclick="C.handlerRemoveFavorite(${movie.id}); event.stopPropagation();" title="Supprimer des favoris">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 3.99 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18.01 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </button>
      </article>
    `;
  }
  movieGrid +=
    "</div>" +
    '<button class="movie-carousel__arrow movie-carousel__arrow--right" type="button" aria-label="Defiler vers la droite" onclick="C.scrollCarousel(this, 1)">' +
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.3 18.7a1 1 0 0 1 0-1.4l4.3-4.3-4.3-4.3a1 1 0 1 1 1.4-1.4l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 0 1-1.4 0z"/></svg>' +
    "</button>" +
    "</div>";

  html += movieGrid;
  html += "</section>";
  return html;
};

export { Favorites };
