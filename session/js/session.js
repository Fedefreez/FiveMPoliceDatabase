function logout() {
  $.post("../php/session_end.php", {}, function (e) {
    if (e === "Success") {
      window.location.href = "../";
    } else {
      d_err("Errore imprevisto. Prova a ricaricare la pagina.");
    }
  });
}

function toggleStatus(citizId) {
  $.post("../php/toggle_citizen_status.php", {"id": citizId, "wanted": $("#citizBtn"+citizId).attr("class").includes("success")}, function(e) {
    e = JSON.parse(e);
    if (e["status"] === "Success") {
      if ($("#citizBtn"+citizId).attr("class").includes("success")) {
        $("#citizBtn"+citizId).attr("class", "citiz-info-subclass-status btn btn-outline-danger").html("Ricercato");
      } else {
        $("#citizBtn"+citizId).attr("class", "citiz-info-subclass-status btn btn-outline-success").html("Non Ricercato");
      }
    } else {
      d_err("Errore imprevisto: il server ha risposto " + e["reason"]);
    }
  });
}

function d_err(msg) {
  $("#alerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}
