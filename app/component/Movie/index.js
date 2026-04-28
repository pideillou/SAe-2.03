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

Movie.format = function (movie) {
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
  return html;
};

Movie.formatMany = function (movies) {
  if (!movies || movies.length === 0) {
    return '<div class="no-movie-msg">Oups, aucun film n\'est disponible !</div>';
  }

  let html = '<div class="movie-grid">';
  for (const movie of movies) {
    html += Movie.format(movie);
  }
  html += "</div>";
  return html;
};

export { Movie };
