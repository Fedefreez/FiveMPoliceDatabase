function logout() {
  $.post("../../php/session_end.php", {}, function (e) {
    if (e === "Success") {
      window.location.href = "../../";
    } else {
      d_err("Errore imprevisto. Prova a ricaricare la pagina.");
    }
  }).fail(function() {
    d_err("Il server non risponde, probabilmente a causa di un errore interno.");
  });;
}

function d_err(msg) {
  $("#alerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

function d_not(msg) {
  $("#alerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-info alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

function registerDeputy() {
  var name = $("#userName").val();
  var surname = $("#userSurname").val();
  var pin = $("#userPIN").val();
  var role_id = localStorage.getItem("RoleId");

  if (name == "" || name == undefined || surname == "" || surname == undefined || !(/^\d+$/.test(pin)) || pin == "" || role_id == "" || role_id == undefined || !(/^\d+$/.test(role_id))) {
    d_err("Compilare correttamente tutti i campi.");
  } else {
    var data = {
      "name": name,
      "surname": surname,
      "pin": pin,
      "role_id": role_id
    }
    $.post("../../php/register_user.php", data, function(e) {
      e = JSON.parse(e);
      if (e["status"] === "success") {
        d_not("Utente aggiunto.");
        $("#userList").append('<li class="list-group-item" id="user' + e["user_id"] + '"><div class="user-container"><span class="user-name-and-surname">' + name + " " + surname + '</span><span class=user-info-btn-container"><button class="btn btn-primary" onclick="viewUser(' + e["user_id"] + ');">Info</button></span></div></li>');
      } else if (e["status"] === "failure") {
        d_err("Errore nella creazione dell'utente. Il server ha risposto con: " + e["reason"]);
      }
    }).fail(function() {
      d_err("Il server non risponde, probabilmente a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function viewUser(id) {

}
