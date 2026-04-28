let HOST_URL = "..";

let DataProfile = {};

DataProfile.add = async function (profile) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(profile),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=addProfile",
      config,
    );
    let data = await answer.json();
    return data;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

DataProfile.getAll = async function () {
  try {
    let answer = await fetch(HOST_URL + "/server/script.php?todo=readProfiles");
    let data = await answer.json();
    return data;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

export { DataProfile };
