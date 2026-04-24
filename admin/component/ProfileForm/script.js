export function setupProfileForm(handler) {
  const form = document.getElementById("profileForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const data = {};
    data.name = form.name.value;
    data.image = form.image.value || null;
    data.min_age = form.min_age.value;

    if (!data.name || !data.min_age) {
      alert("Remplis tous les champs obligatoires.");
      return;
    }

    if (data.name.trim().length === 0) {
      alert("Le nom du profil ne peut pas être vide.");
      return;
    }

    handler(data);
  });
}
