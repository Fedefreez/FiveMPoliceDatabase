function login() {
  var pin = $("#user_pin").val();
  if (/^\d+$/.test(pin) && pin !== "") {
    $.post("php/login.php", {"pin": pin}, function (e) {
      e = JSON.parse(e);
      if (e["status"] === "success" && e["accepted"] === "true") {
        window.location.href = "session/";
      } else if (e["accepted"] === "false") {
        d_err("Credenziali incorrette.");
      } else if (e["status"] === "failure"){
        d_err("Errore, il server ha risposto con: " + e["reason"]);
      } else {
        d_err("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err("Il server non risponde, probabilmente a causa di un errore interno.");
    });
  } else {
    d_err("É necessario inserire un pin numerico.");
  }
}

function d_err(msg) {
  $("#alerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

$(document).ready(function() {
  document.getElementById("user_pin").addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
      login();
    }
  });
});
