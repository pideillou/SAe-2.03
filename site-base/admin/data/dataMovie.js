let HOST_URL = "..";

let DataMovie = {};

DataMovie.add = async function (movie) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(movie),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=addMovie",
      config,
    );
    let data = await answer.json();
    return data;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

DataMovie.searchMovies = async function (query) {
  try {
    let answer = await fetch(
      HOST_URL +
        "/server/script.php?todo=searchMovies&query=" +
        encodeURIComponent(query) +
        "&age=18",
    );
    let data = await answer.json();
    return data;
  } catch (e) {
    return { error: "Erreur de connexion au serveur lors de la recherche." };
  }
};

DataMovie.updateFeatured = async function (idMovie, isFeatured) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        id_movie: idMovie,
        is_featured: isFeatured ? 1 : 0,
      }),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=updateMovieFeatured",
      config,
    );
    let data = await answer.json();
    return data;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

export { DataMovie };
