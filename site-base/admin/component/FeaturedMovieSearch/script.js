let templateFile = await fetch("./component/FeaturedMovieSearch/template.html");
let template = await templateFile.text();

let FeaturedMovieSearch = {};

FeaturedMovieSearch.format = function () {
  return template;
};

FeaturedMovieSearch.formatResults = function (movies) {
  if (!Array.isArray(movies) || movies.length === 0) {
    return '<div class="featured-search__empty">Aucun film ne correspond à votre recherche.</div>';
  }

  let html = '<div class="featured-search__list">';

  for (const movie of movies) {
    const isFeatured = movie.is_featured ? true : false;
    const statusText = isFeatured ? "Mis en avant" : "Non mis en avant";
    const statusClass = isFeatured
      ? "featured-search__status--active"
      : "featured-search__status--inactive";
    const buttonText = isFeatured ? "Retirer" : "Ajouter";
    const buttonClass = isFeatured
      ? "featured-search__toggle-btn--remove"
      : "featured-search__toggle-btn--add";

    html += `
      <div class="featured-search__item">
        <div class="featured-search__item-header">
          <h3 class="featured-search__item-title">${movie.name}</h3>
          <span class="featured-search__status ${statusClass}">${statusText}</span>
        </div>
        <div class="featured-search__item-details">
          <p class="featured-search__detail"><strong>Réalisateur:</strong> ${movie.director}</p>
          <p class="featured-search__detail"><strong>Année:</strong> ${movie.year_movie}</p>
          <p class="featured-search__detail"><strong>Catégorie:</strong> ${movie.category}</p>
          <p class="featured-search__detail"><strong>Durée:</strong> ${movie.length} min</p>
          <p class="featured-search__detail"><strong>Restriction d'âge:</strong> ${movie.min_age}+</p>
        </div>
        <button
          class="featured-search__toggle-btn ${buttonClass}"
          data-movie-id="${movie.id}"
          data-is-featured="${isFeatured ? "1" : "0"}"
          type="button"
          aria-label="Modifier le statut mis en avant du film ${movie.name}"
        >
          ${buttonText} des films mis en avant
        </button>
      </div>
    `;
  }

  html += "</div>";
  return html;
};

export { FeaturedMovieSearch };
