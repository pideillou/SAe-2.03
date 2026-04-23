
// Version très simple pour débutant
export function setupMovieForm(handler) {
  // On récupère le formulaire
  const form = document.getElementById('movieForm');
  if (!form) return;

  // Quand on soumet le formulaire
  form.addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    // On récupère les valeurs des champs

    // On adapte les noms pour coller à la table Movie
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

    // Vérification très simple

    if (!data.name || !data.director || !data.year || !data.length || !data.description || !data.id_category || !data.image || !data.min_age) {
      alert('Remplis tous les champs obligatoires.');
      return;
    }

    // Appel la fonction pour ajouter le film
    handler(data);
  });
}
