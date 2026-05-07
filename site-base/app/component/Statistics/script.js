let templateFile = await fetch("./component/Statistics/template.html");
let template = await templateFile.text();

let Statistics = {};

Statistics.format = function (stats) {
  let html = template;
  let statsHtml = "";

  statsHtml += `
    <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
      <div style="font-size: 2rem; font-weight: bold; color: #e50914;">${stats.total_profiles}</div>
      <div style="color: #666; margin-top: 0.5rem;">Profils créés</div>
    </div>
  `;

  statsHtml += `
    <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
      <div style="font-size: 2rem; font-weight: bold; color: #e50914;">${stats.total_movies}</div>
      <div style="color: #666; margin-top: 0.5rem;">Films dans la base</div>
    </div>
  `;

  if (stats.most_favorited_movie) {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.2rem; font-weight: bold; color: #e50914;">${stats.most_favorited_movie.name}</div>
        <div style="color: #666; margin-top: 0.5rem;">${stats.most_favorited_movie.favorite_count} fois en favoris</div>
      </div>
    `;
  }

  if (stats.most_popular_category) {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.2rem; font-weight: bold; color: #e50914;">${stats.most_popular_category.name || "Sans catégorie"}</div>
        <div style="color: #666; margin-top: 0.5rem;">Catégorie la plus aimée (${stats.most_popular_category.favorite_count} favoris)</div>
      </div>
    `;
  }

  // Average favorites per profile
  if (typeof stats.avg_favorites_per_profile !== "undefined") {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.6rem; font-weight: bold; color: #e50914;">${stats.avg_favorites_per_profile}</div>
        <div style="color: #666; margin-top: 0.5rem;">Moyenne de favoris par profil</div>
      </div>
    `;
  }

  // Most active profile
  if (stats.most_active_profile) {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.2rem; font-weight: bold; color: #e50914;">${stats.most_active_profile.name}</div>
        <div style="color: #666; margin-top: 0.5rem;">Activité: ${stats.most_active_profile.activity_count} (favoris ${stats.most_active_profile.favorites}, notes ${stats.most_active_profile.ratings})</div>
      </div>
    `;
  }

  // Comments by status
  if (stats.comments_by_status) {
    const pending = stats.comments_by_status["pending"] || 0;
    const approved = stats.comments_by_status["approved"] || 0;
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.4rem; font-weight: bold; color: #e50914;">${stats.total_comments || pending + approved}</div>
        <div style="color: #666; margin-top: 0.5rem;">Commentaires (approuvés: ${approved}, en attente: ${pending})</div>
      </div>
    `;
  }

  // Top rated movie
  if (stats.top_rated_movie) {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.2rem; font-weight: bold; color: #e50914;">${stats.top_rated_movie.name}</div>
        <div style="color: #666; margin-top: 0.5rem;">Moyenne: ${stats.top_rated_movie.average} (${stats.top_rated_movie.votes} votes)</div>
      </div>
    `;
  }

  // Most recent movie
  if (stats.most_recent_movie) {
    statsHtml += `
      <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
        <div style="font-size: 1.2rem; font-weight: bold; color: #e50914;">${stats.most_recent_movie.name}</div>
        <div style="color: #666; margin-top: 0.5rem;">Ajouté le: ${stats.most_recent_movie.created_at}</div>
      </div>
    `;
  }

  html = html.replace("{{stats}}", statsHtml);
  return html;
};

export { Statistics };
