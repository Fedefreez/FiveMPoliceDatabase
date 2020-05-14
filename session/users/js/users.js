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

function d_scc(msg) {
  $("#alerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-success alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

function d_err_popup(msg) {
  $("#popupAlerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

function d_scc_popup(msg) {
  $("#popupAlerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-success alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
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
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err("Decodifica JSON fallita. Ragione: "  + err.message);
      }

      if (e["status"] === "success") {
        d_not("Utente aggiunto.");
        $("#userList").append('<li class="list-group-item" id="user' + e["user_id"] + '"><div class="user-container"><span class="user-name-and-surname">' + name + " " + surname + '</span><span class=user-info-btn-container"><button class="btn btn-danger" onclick="removeUser(' +  e["user_id"] + ');">Rimuovi</button><span class="col-sm-1" role="separator"></span><button class="btn btn-primary" onclick="viewUser(' + e["user_id"] + ');">Info</button></span></div></li>');
      } else if (e["status"] === "failure") {
        d_err("Errore nella creazione dell'utente. Il server ha risposto con: " + e["reason"]);
      }
    }).fail(function() {
      d_err("Il server non risponde, probabilmente a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function unlockUserInfo() {
  $("#userInfoName").attr("disabled", false);
  $("#userInfoSurname").attr("disabled", false);
  $("#userInfoPIN").attr("disabled", false);
  $("#userInfoRoleBtn").attr("disabled", false);
  $("#editUserInfoBtn").html("Salva").attr("onclick", "lockUserInfo();");
  localStorage.setItem("editingCitizenInfo", true);
}

function lockUserInfo() {
  $("#userInfoName").attr("disabled", true);
  $("#userInfoSurname").attr("disabled", true);
  $("#userInfoPIN").attr("disabled", true);
  $("#userInfoRoleBtn").attr("disabled", true);
  $("#editUserInfoBtn").html("Modifica dati").attr("onclick", "unlockUserInfo();");

  if (localStorage.getItem("editingCitizenInfo")) {
    $("#citizInfoPopup").modal("toggle"); //dismiss modal
  }
  localStorage.setItem("editingCitizenInfo", false);

  saveUserData(localStorage.getItem("viewingUserId"));
}

function viewUser(id) {
  localStorage.setItem("viewingUserId", id);

  if (id == "" || id == undefined || !(/^\d+$/.test(id))) {
    d_err_popup("Errore nella lettura dell'UUID utente. Prova a ricaricare la pagina o contatta un amministratore.");
  } else {
    $.post("../../php/user_info.php", {"user_id": id}, function(e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err_popup("Errore nella decodifica JSON. " + err.message);
        return;
      }
      if (e["status"] === "success") {
        $("#userInfoName").val(e["info"]["name"]);
        $("#userInfoSurname").val(e["info"]["surname"]);
        $("#userInfoPIN").val(e["info"]["pin"]);
        $("#userInfoRole").val(e["info"]["role"]);
        switch (e["info"]["role"]) {
          case "Questore":
            localStorage.setItem("RoleId", 1);
            break;
          case "Ispettore capo":
            localStorage.setItem("RoleId", 2);
            break;
          case "Tenente":
            localStorage.setItem("RoleId", 3);
            break;
          case "Agente scelto":
            localStorage.setItem("RoleId", 14);
            break;
          case "Recluta":
            localStorage.setItem("RoleId", 4);
            break;
          case "Amministratore":
            localStorage.setItem("RoleId", 5);
            break;
          default:
            d_err_popup("I valori con cui il server ha risposto non corrispondono a quelli predefiniti. Ricarica la pagina o contatta un amministratore.");
            break;
        }
      } else if (e["reason"] === "failure") {
        d_err_popup("Errore nella ricezione dei dati. Il server ha risposto con: "  + e["reason"]);
      } else {
        d_err_popup("Errore sconosciuto. Contatta un amministratore segnandoti questo codice: E+" + e["status"] + "+R+" + e["reason"]);
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, probabilmente a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function saveUserData(id) {
  var name = $("#userInfoName").val();
  var surname = $("#userInfoSurname").val();
  var pin = $("#userInfoPIN").val();
  var roleId = localStorage.getItem("RoleId");

  if (name == "" || name == undefined || surname == "" || surname == undefined || pin == "" || pin == undefined ||  !(/^\d+$/.test(pin)) || roleId == "" || roleId == undefined) {
    d_err_popup("Compilare correttamente tutti i campi.");
  } else {
    var data = {
      "user_id": id,
      "name": name,
      "surname": surname,
      "pin": pin,
      "role_id": roleId
    }
    $.post("../../php/edit_user.php", data, function(e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err_popup("Errore nella decodifica JSON. " + err);
        return;
      }
      if (e["status"] === "success") {
        d_scc_popup("Modifiche salvate.");
      } else if (e["status"] === "failure") {
        d_err_popup("Errore nella modifica dei dati. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err_popup("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, probabilmente a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function removeUser(id) {
  if (id == "" || id == undefined || !(/^\d+$/.test(id))) {
    d_err("Errore nella lettura dei dati dal localStorage. Ricarica la pagina oppure contatta un amministratore.");
  } else {
    $.post("../../php/remove_user.php", {"user_id": id}, function(e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err("Errore nella decodifica JSON. " + err);
      }
      if (e["status"] === "success") {
        $("#user"+id).remove();
        d_scc("Utente rimosso.");
      } else if (e["status"] === "failure") {
        d_err("Errore nella rimozione dell'utente. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err("Il server non risponde, probabilmente a causa di un errore interno. Contatta un amministratore.");
    });
  }
}
