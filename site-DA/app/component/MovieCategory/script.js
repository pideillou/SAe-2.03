import { Movie } from "../Movie/index.js";

export let MovieCategory = {
  format: function (categoryName, films, favorites = []) {
    return `
      <section class="movie-category">
      <h2 class="movie-category__title">${categoryName} <span class="movie-badge">${films.length} titres</span></h2>
      <div class="movie-category__list">
        <div class="movie-carousel">
          <button class="movie-carousel__arrow movie-carousel__arrow--left" type="button" aria-label="Defiler vers la gauche" onclick="C.scrollCarousel(this, -1)">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.7 5.3a1 1 0 0 1 0 1.4L10.4 11l4.3 4.3a1 1 0 1 1-1.4 1.4l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 0z"/></svg>
          </button>
          ${Movie.formatMany(films, favorites)}
          <button class="movie-carousel__arrow movie-carousel__arrow--right" type="button" aria-label="Defiler vers la droite" onclick="C.scrollCarousel(this, 1)">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.3 18.7a1 1 0 0 1 0-1.4l4.3-4.3-4.3-4.3a1 1 0 1 1 1.4-1.4l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 0 1-1.4 0z"/></svg>
          </button>
        </div>
      </div>
      </section>
    `;
  },
};
