let HOST_URL = "..";

let DataComment = {};

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

DataComment.readPending = async function () {
  try {
    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=readPendingMovieComments",
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error ||
          "Erreur serveur lors du chargement des commentaires à modérer.",
      };
    }

    return data;
  } catch (e) {
    return {
      error: "Erreur reseau lors du chargement des commentaires a moderer.",
    };
  }
};

DataComment.approve = async function (idComment) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ id_comment: idComment }),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=approveMovieComment",
      config,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error || "Erreur serveur lors de l'approbation du commentaire.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors de l'approbation du commentaire." };
  }
};

DataComment.remove = async function (idComment) {
  try {
    let config = {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ id_comment: idComment }),
    };

    let answer = await fetch(
      HOST_URL + "/server/script.php?todo=deleteMovieComment",
      config,
    );
    let data = await readJsonResponse(answer);

    if (!answer.ok) {
      return {
        error:
          data.error || "Erreur serveur lors de la suppression du commentaire.",
      };
    }

    return data;
  } catch (e) {
    return { error: "Erreur reseau lors de la suppression du commentaire." };
  }
};

export { DataComment };
