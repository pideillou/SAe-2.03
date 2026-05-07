import { Movie } from "../Movie/index.js";

export let MovieCategory = {
  format: function (categoryName, films, favorites = []) {
    return `
      <section class="movie-category">
      <h2 class="movie-category__title">${categoryName} <span class="movie-badge">${films.length} titres</span></h2>
      <div class="movie-category__list">${Movie.formatMany(films, favorites)}</div>
      </section>
    `;
  },
};
