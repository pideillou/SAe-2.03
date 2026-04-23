import { DataMovie } from "../../data/dataMovie.js";

function createMovieCard(movie) {
	const card = document.createElement("div");
	card.className = "movie-card";

	const img = document.createElement("img");
	img.src = movie.image || "https://via.placeholder.com/200x300?text=No+Image";
	img.alt = movie.name || "Film";

	const overlay = document.createElement("div");
	overlay.className = "movie-overlay";

	const title = document.createElement("div");
	title.className = "movie-title";
	title.textContent = movie.name || "Titre inconnu";

	const desc = document.createElement("div");
	desc.className = "movie-desc";
	desc.textContent = movie.description || "Pas de description.";

	overlay.appendChild(title);
	overlay.appendChild(desc);

	card.appendChild(img);
	card.appendChild(overlay);
	return card;
}

// Exécution directe après chargement du composant
const grid = document.getElementById("movieGrid");
if (grid) {
	DataMovie.requestMovies().then(movies => {
		grid.innerHTML = "";
		if (!movies || movies.length === 0) {
			const msg = document.createElement("div");
			msg.className = "no-movie-msg";
			msg.textContent = "Oups, aucun film n’est disponible !";
			grid.appendChild(msg);
			return;
		}
		movies.forEach(movie => {
			grid.appendChild(createMovieCard(movie));
		});
	});
}
