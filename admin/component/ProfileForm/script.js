let templateFile = await fetch("./component/ProfileForm/template.html");
let template = await templateFile.text();

let ProfileForm = {};

function formatProfileOptions(profiles, activeProfileId) {
  let html = '<option value="">Nouveau profil</option>';

  for (const profile of profiles) {
    let selected =
      String(profile.id) === String(activeProfileId) ? " selected" : "";
    html +=
      '<option value="' +
      profile.id +
      '"' +
      selected +
      ">" +
      (profile.name || "Profil") +
      "</option>";
  }

  return html;
}

function formatMinAgeOptions(selectedAge) {
  let ages = [0, 3, 7, 10, 12, 16, 18];
  if (selectedAge === "" || selectedAge === null || selectedAge === undefined) {
    selectedAge = 0;
  }
  let html = "";

  for (const age of ages) {
    let label = age === 0 ? "0 - Tous publics" : String(age) + " ans+";
    if (age === 18) {
      label = "18 ans+ (Adultes)";
    }

    let selected = String(age) === String(selectedAge) ? " selected" : "";
    html +=
      '<option value="' + age + '"' + selected + ">" + label + "</option>";
  }

  return html;
}

ProfileForm.format = function (
  handler,
  handlerSelectProfile,
  profiles = [],
  activeProfileId = "",
) {
  let selectedProfile = profiles.find(function (profile) {
    return String(profile.id) === String(activeProfileId);
  });

  let html = template;
  html = html.replace("{{handler}}", handler);
  html = html.replace("{{handlerSelectProfile}}", handlerSelectProfile);
  html = html.replace(
    "{{profileOptions}}",
    formatProfileOptions(profiles, activeProfileId),
  );
  html = html.replace(
    "{{profileId}}",
    selectedProfile ? selectedProfile.id : "",
  );
  html = html.replace(
    "{{profileName}}",
    selectedProfile && selectedProfile.name ? selectedProfile.name : "",
  );
  html = html.replace(
    "{{profileImage}}",
    selectedProfile && selectedProfile.image ? selectedProfile.image : "",
  );
  html = html.replace(
    "{{minAgeOptions}}",
    formatMinAgeOptions(selectedProfile ? selectedProfile.min_age : ""),
  );
  return html;
};

export { ProfileForm };
