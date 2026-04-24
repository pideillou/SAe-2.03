let HOST_URL = "https://mmi.unilim.fr/~pideill2/SAe-2.03";

let DataMovie = {};

DataMovie.requestMovies = async function () {
  let answer = await fetch(HOST_URL + "/server/script.php?todo=readmovies");
  let data = await answer.json();
  return data;
};

DataMovie.requestMoviesByCategory = async function () {
  const movies = await DataMovie.requestMovies();
  const grouped = {};

  for (const movie of movies) {
    const category = movie.category || "Sans catégorie";

    if (!grouped[category]) {
      grouped[category] = [];
    }

    grouped[category].push(movie);
  }

  return grouped;
};

DataMovie.requestMovieDetails = async function (id) {
  const res = await fetch(
    `${HOST_URL}/server/script.php?todo=readMovieDetail&id=${id}`,
  );
  if (!res.ok) throw new Error("Erreur serveur");
  return await res.json();
};

export { DataMovie };
