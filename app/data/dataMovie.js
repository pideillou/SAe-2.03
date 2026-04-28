let HOST_URL = "..";

let DataMovie = {};

async function readJsonResponse(answer) {
  const raw = await answer.text();

  try {
    return JSON.parse(raw);
  } catch (e) {
    return {
      error: "Reponse JSON invalide du serveur.",
      status: answer.status,
      preview: raw.slice(0, 200),
    };
  }
}

DataMovie.requestMovies = async function () {
  try {
    let answer = await fetch(HOST_URL + "/server/script.php?todo=readMovies");
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error: data.error || "Erreur serveur lors du chargement des films.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors du chargement des films." };
  }
};

DataMovie.requestMoviesByCategory = async function () {
  const movies = await DataMovie.requestMovies();
  if (!Array.isArray(movies)) {
    return {
      error:
        movies && movies.error
          ? movies.error
          : "Format de donnees films invalide.",
    };
  }

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
  try {
    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=readMovieDetail&id=" + id,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error || "Erreur serveur lors du chargement du detail film.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors du chargement du detail film." };
  }
};

export { DataMovie };
