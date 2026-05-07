let templateFile = await fetch("./component/CommentModeration/template.html");
let template = await templateFile.text();

let CommentModeration = {};

function escapeHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, function (character) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[character];
  });
}

function formatStatus(status) {
  if (status === "approved") return "Approuvé";
  if (status === "pending") return "En attente";
  return status || "Inconnu";
}

CommentModeration.format = function () {
  return template;
};

CommentModeration.formatComments = function (comments) {
  if (!Array.isArray(comments) || comments.length === 0) {
    return '<div class="comment-moderation__empty">Aucun commentaire à modérer pour le moment.</div>';
  }

  return comments
    .map(function (comment) {
      return (
        '<article class="comment-moderation__item">' +
        '<div class="comment-moderation__meta">' +
        "<div><strong>" +
        escapeHtml(comment.profile_name || "Profil inconnu") +
        "</strong> sur <span>" +
        escapeHtml(comment.movie_name || "Film inconnu") +
        "</span></div>" +
        "<div>" +
        escapeHtml(comment.created_at || "") +
        "</div></div>" +
        '<div class="comment-moderation__status">Statut : <strong>' +
        escapeHtml(formatStatus(comment.comment_status || "pending")) +
        "</strong></div>" +
        '<div class="comment-moderation__text">' +
        escapeHtml(comment.comment_text || "") +
        "</div>" +
        '<div class="comment-moderation__actions">' +
        '<button class="comment-moderation__approve" type="button" data-comment-id="' +
        comment.id +
        '">Approuver</button>' +
        '<button class="comment-moderation__delete" type="button" data-comment-id="' +
        comment.id +
        '">Supprimer</button>' +
        "</div></article>"
      );
    })
    .join("");
};

export { CommentModeration };
