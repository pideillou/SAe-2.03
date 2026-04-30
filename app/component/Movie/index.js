let templateFile = await fetch("./component/Movie/template.html");
let template = await templateFile.text();

let Movie = {};

function getMovieLabel(movie) {
  return (
    movie.name ||
    movie.title ||
    movie.titre ||
    movie.nom ||
    movie.movie_name ||
    movie.movieName ||
    "Titre inconnu"
  );
}

Movie.format = function (movie, isFavorite = false) {
  let html = template;
  html = html.replace("{{handler}}", "C.handlerDetail(" + movie.id + ")");
  html = html.replace(
    "{{image}}",
    movie.image
      ? "../server/images/" + movie.image
      : "https://via.placeholder.com/200x300?text=No+Image",
  );
  html = html.split("{{name}}").join(getMovieLabel(movie));
  html = html.replace(
    "{{description}}",
    movie.description || "Pas de description.",
  );
  html = html.replace("{{year}}", movie.year || "N/A");

  if (isFavorite) {
    html = html.replace("{{favoriteClass}}", "movie-favorite--active");
    html = html.replace("{{favoriteTitle}}", "Supprimer des favoris");
    html = html.replace(
      "{{favoriteIcon}}",
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 3.99 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18.01 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
    );
    html = html.replace(
      "{{favoriteHandler}}",
      "C.handlerRemoveFavorite(" + movie.id + ")",
    );
  } else {
    html = html.replace("{{favoriteClass}}", "");
    html = html.replace("{{favoriteTitle}}", "Ajouter aux favoris");
    html = html.replace(
      "{{favoriteIcon}}",
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.1 8.64l-.1.1-.11-.1C10.14 6.7 7.6 6.24 5.9 7.77c-1.74 1.34-1.8 3.99-.2 5.54C7.1 15.9 9.6 18.03 12 20c2.4-1.97 4.9-4.1 6.3-6.69 1.6-1.55 1.54-4.2-.2-5.54-1.71-1.53-4.25-1.07-6.0 1.0z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    );
    html = html.replace(
      "{{favoriteHandler}}",
      "C.handlerAddFavorite(" + movie.id + ")",
    );
  }

  return html;
};

Movie.formatMany = function (movies, favorites = []) {
  if (!movies || movies.length === 0) {
    return '<div class="no-movie-msg">Oups, aucun film n\'est disponible !</div>';
  }

  let html = '<div class="movie-grid">';
  for (const movie of movies) {
    let isFav = favorites.some(function (fav) {
      return String(fav.id) === String(movie.id);
    });
    html += Movie.format(movie, isFav);
  }
  html += "</div>";
  return html;
};

export { Movie };
