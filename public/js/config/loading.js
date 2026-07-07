document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("globalLoader");

  document.querySelectorAll("form").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      const opensInNewTab = form.target === "_blank";
      if (form.method.toUpperCase() === "POST" && !opensInNewTab) {
        if (!e.defaultPrevented) {
          loader.style.display = "flex";
        }
      }
    });
  });

  window.addEventListener("load", function () {
    loader.style.display = "none";
  });
});
