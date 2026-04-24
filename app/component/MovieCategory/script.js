import { Movie } from "../Movie/index.js";

export let MovieCategory = {
  render: function (categoryName, films) {
    let el = document.createElement("div");
    el.className = "movie-category";
    el.innerHTML = `
      <h2 class="movie-category__title">${categoryName}</h2>
      <div class="movie-category__list">${Movie.format(films)}</div>
    `;
    return el;
  },
};
