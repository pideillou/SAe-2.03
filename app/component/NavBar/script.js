let templateFile = await fetch("./component/NavBar/template.html");
let template = await templateFile.text();

let NavBar = {};

function getProfileLabel(profile) {
  return profile.name || profile.title || profile.titre || "Profil";
}

function getProfileAvatar(profile) {
  if (profile.image && profile.image.trim() !== "") {
    return "../server/images/" + profile.image;
  }

  return "";
}

function getProfileInitial(profile) {
  let label = getProfileLabel(profile).trim();
  return label ? label.charAt(0).toUpperCase() : "P";
}

NavBar.format = function (hAbout, hHome, profiles, activeProfileId) {
  let html = template;
  html = html.replace("{{hAbout}}", hAbout);
  html = html.replace("{{hHome}}", hHome);

  let currentProfile = profiles.find(function (profile) {
    return String(profile.id) === String(activeProfileId);
  });

  let currentProfileLabel = currentProfile
    ? getProfileLabel(currentProfile)
    : "Aucun profil selectionne";

  let profilesHtml = "";
  if (!profiles || profiles.length === 0) {
    profilesHtml =
      '<div class="navbar__profiles-empty">Aucun profil disponible.</div>';
  } else {
    for (const profile of profiles) {
      let label = getProfileLabel(profile);
      let avatar = getProfileAvatar(profile);
      let isActive = String(profile.id) === String(activeProfileId);
      let avatarHtml = avatar
        ? '<img class="navbar__profile-avatar" src="' +
          avatar +
          '" alt="' +
          label +
          '" />'
        : '<span class="navbar__profile-avatar navbar__profile-avatar--initial">' +
          getProfileInitial(profile) +
          "</span>";

      profilesHtml +=
        '<button class="navbar__profile' +
        (isActive ? " navbar__profile--active" : "") +
        '" type="button" onclick="C.handlerSelectProfile(' +
        profile.id +
        ')">' +
        avatarHtml +
        '<span class="navbar__profile-name">' +
        label +
        "</span>" +
        "</button>";
    }
  }

  html = html.replace("{{profiles}}", profilesHtml);
  html = html.replace("{{currentProfileLabel}}", currentProfileLabel);
  return html;
};

export { NavBar };
