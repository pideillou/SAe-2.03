let HOST_URL = "..";

let DataProfile = {};

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

DataProfile.read = async function () {
  try {
    let answer = await fetch(HOST_URL + "/server/script.php?todo=readProfiles");
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error: data.error || "Erreur serveur lors du chargement des profils.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors du chargement des profils." };
  }
};

export { DataProfile };
