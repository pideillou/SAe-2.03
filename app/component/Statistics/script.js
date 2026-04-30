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

  html = html.replace("{{stats}}", statsHtml);
  return html;
};

export { Statistics };
