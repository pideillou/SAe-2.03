export function setupMovieForm(handler) {
  const form = document.getElementById("movieForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const data = {};
    data.name = form.title.value;
    data.director = form.director.value;
    data.year = form.year.value;
    data.length = form.duration.value;
    data.description = form.description.value;
    data.id_category = form.category.value;
    data.image = form.image.value;
    data.trailer = form.trailer.value;
    data.min_age = form.age.value;

    if (
      !data.name ||
      !data.director ||
      !data.year ||
      !data.length ||
      !data.description ||
      !data.id_category ||
      !data.image ||
      !data.min_age
    ) {
      alert("Remplis tous les champs obligatoires.");
      return;
    }

    handler(data);
  });
}
