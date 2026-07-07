(function ($) {
  "use strict";

  if (typeof $ === "undefined") return;

  function bloquearBotones(form) {
    var snapshots = [];
    $(form).find('button[type="submit"], input[type="submit"]').each(function () {
      snapshots.push({ el: this, html: this.innerHTML, val: this.value });
      this.disabled = true;
      if (this.tagName === "BUTTON") {
        this.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status"></span> Procesando…';
      } else {
        this.value = "Procesando…";
      }
    });
    return snapshots;
  }

  function restaurarBotones(snapshots) {
    $.each(snapshots, function (_, s) {
      s.el.disabled = false;
      if (s.el.tagName === "BUTTON") s.el.innerHTML = s.html;
      else s.el.value = s.val;
    });
  }

  window.enviarFormAjax = function (form, opciones) {
    opciones = opciones || {};
    var noReload = opciones.noReload || form.hasAttribute("data-no-reload");
    var formData = new FormData(form);
    var url = form.action || window.location.href;
    var snapshots = bloquearBotones(form);

    $.ajax({
      type: "POST",
      url: url,
      data: formData,
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
      headers: { "X-Requested-With": "XMLHttpRequest" },

      success: function (data) {
        restaurarBotones(snapshots);
        var item = Array.isArray(data) ? data[0] : data;
        var ok = String(item.S_1) === "1";
        var msg = (item.S_2 || "").trim() || (ok ? "Operación realizada." : "Ocurrió un error.");

        if (ok) {
          if (typeof opciones.onSuccess === "function") {
            opciones.onSuccess(data);
          } else {
            Swal.fire({ title: "Éxito", text: msg, icon: "success", confirmButtonText: "Aceptar" }).then(function () {
              var ev = $.Event("ajax:success");
              $(document).trigger(ev, [data, form]);
              if (!ev.isDefaultPrevented() && !noReload) {
                window.location.reload();
              }
            });
          }
        } else {
          if (typeof opciones.onError === "function") {
            opciones.onError(data);
          } else {
            Swal.fire({ title: "Error", text: msg, icon: "error", confirmButtonText: "Aceptar" });
          }
        }
      },

      error: function (xhr, status, err) {
        restaurarBotones(snapshots);
        Swal.fire({
          title: "Error de conexión",
          text: "No se pudo completar la operación (HTTP " + xhr.status + "). Intente de nuevo.",
          icon: "error",
          confirmButtonText: "Aceptar",
        });
      },
    });
  };

  $(document).on("submit", "form", function (e) {
    var form = this;
    if (form.method.toLowerCase() !== "post" || form.hasAttribute("data-no-ajax") || form.enctype === "multipart/form-data") return;
    e.preventDefault();
    if (!form.checkValidity()) { form.reportValidity(); return; }
    window.enviarFormAjax(form);
  });

  $(document).ajaxStart(function () {
    var el = document.getElementById("globalLoader");
    if (el) el.style.display = "flex";
  }).ajaxStop(function () {
    var el = document.getElementById("globalLoader");
    if (el) el.style.display = "none";
  });
})(jQuery);
