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

DataMovie.requestMovies = async function (ageLimit = 0) {
  try {
    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=readMovies&age=" + ageLimit,
    );
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

DataMovie.requestMoviesByCategory = async function (ageLimit = 0) {
  const movies = await DataMovie.requestMovies(ageLimit);
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

DataMovie.addFavorite = async function (idProfile, idMovie) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        id_profile: idProfile,
        id_movie: idMovie,
      }),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=addFavorite",
      config,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error: data.error || "Erreur serveur lors de l'ajout aux favoris.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors de l'ajout aux favoris." };
  }
};

DataMovie.removeFavorite = async function (idProfile, idMovie) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        id_profile: idProfile,
        id_movie: idMovie,
      }),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=removeFavorite",
      config,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error || "Erreur serveur lors de la suppression des favoris.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors de la suppression des favoris." };
  }
};

DataMovie.getFavorites = async function (idProfile) {
  try {
    let answer = await fetch(
      HOST_URL +
        "/server/script.php?todo=readFavorites&id_profile=" +
        idProfile,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error: data.error || "Erreur serveur lors du chargement des favoris.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors du chargement des favoris." };
  }
};

DataMovie.getFeaturedMovies = async function (ageLimit = 0) {
  try {
    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=readFeaturedMovies&age=" + ageLimit,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error ||
          "Erreur serveur lors du chargement des films mis en avant.",
      };
    }

    return data;
  } catch (e) {
    return {
      error: "Erreur reseau lors du chargement des films mis en avant.",
    };
  }
};

DataMovie.getStatistics = async function () {
  try {
    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=getStatistics",
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error || "Erreur serveur lors du chargement des statistiques.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors du chargement des statistiques." };
  }
};

export { DataMovie };
