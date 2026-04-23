let HOST_URL = "https://mmi.unilim.fr/~pideill2/SAe-2.03/";

let DataMovie = {};

DataMovie.add = async function(movie) {
  try {
    const response = await fetch(HOST_URL + '/server/script.php?todo=addMovie', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(movie)
    });
    const result = await response.json();
    return result;
  } catch (e) {
    return { error: 'Erreur de connexion au serveur.' };
  }
};

export { DataMovie };
