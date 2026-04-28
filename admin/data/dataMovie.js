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

export { DataMovie };
