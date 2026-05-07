let templateFile = await fetch("./component/MovieDetail/template.html");
let template = await templateFile.text();

let MovieDetail = {};

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

MovieDetail.format = function (movie) {
  let html = template;
  html = html.replace(
    /{{newHidden}}/g,
    movie.is_new ? "" : 'aria-hidden="true" hidden',
  );
  html = html.replace(
    "{{image}}",
    movie.image
      ? "../server/images/" + movie.image
      : "https://via.placeholder.com/300x450?text=No+Image",
  );
  html = html.replace("{{name}}", getMovieLabel(movie));
  html = html.replace(
    "{{description}}",
    movie.description || "Pas de description.",
  );
  html = html.replace("{{director}}", movie.director || "-");
  html = html.replace("{{year}}", movie.year_movie || "-");
  html = html.replace("{{category}}", movie.category || "-");
  let minAgeValue = movie.min_age ?? movie.minAge ?? movie.age_min ?? null;
  let minAgeLabel = "-";
  if (minAgeValue !== null && minAgeValue !== undefined && minAgeValue !== "") {
    minAgeLabel =
      parseInt(minAgeValue) === 0 || minAgeValue === "0"
        ? "Tous publics"
        : `${minAgeValue} ans`;
  }
  html = html.replace("{{minAge}}", minAgeLabel);

  let trailerHtml = movie.trailer
    ? '<iframe src="' +
      movie.trailer +
      '" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>' +
      '<div class="movie-detail__trailer-fallback"><a class="movie-detail__trailer-link" href="' +
      movie.trailer +
      '" target="_blank" rel="noopener noreferrer">Ouvrir la bande annonce dans un nouvel onglet</a></div>'
    : "<em>Pas de trailer.</em>";
  html = html.replace("{{trailer}}", trailerHtml);

  return html;
};

export { MovieDetail };
