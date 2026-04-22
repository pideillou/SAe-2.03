let templateFile = await fetch("./component/NavBar/template.html");
let template = await templateFile.text();

let NavBar = {};

NavBar.format = function (hAbout, hHome) {
  let html = template;
  html = html.replace("{{hAbout}}", hAbout);
  return html;
};

export { NavBar };
