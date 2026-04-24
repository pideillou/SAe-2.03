let HOST_URL = "https://mmi.unilim.fr/~pideill2/SAe-2.03/";

let DataProfile = {};

DataProfile.add = async function (profile) {
  try {
    const response = await fetch(
      HOST_URL + "/server/script.php?todo=addProfile",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(profile),
      },
    );
    const result = await response.json();
    return result;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

DataProfile.getAll = async function () {
  try {
    const response = await fetch(
      HOST_URL + "/server/script.php?todo=readProfiles",
    );
    if (!response.ok) throw new Error("Erreur serveur");
    const result = await response.json();
    return result;
  } catch (e) {
    return { error: "Erreur de connexion au serveur." };
  }
};

export { DataProfile };
