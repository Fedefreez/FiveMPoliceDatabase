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

function registerCitizen() {
  var name = $("#citizenName").val();
  var surname = $("#citizenSurname").val();
  switch ($("#citizenGender").val()) {
    case "Maschio":
      var gender = 0;
      break;
    case "Femmina":
      var gender  = 1;
      break;
    case "Altro":
      var gender = 2;
      break;
    default:
      d_err("Il genere selezionato non corrisponde ai valori predefiniti.");
      break;
  }
  var dob = $("#citizenDOB").val();
  var gunLicense = $("#gunLicense").attr("class").includes("success");
  var status = ($("#status").attr("class").includes("danger") ? 2 : 1);
  var jobId = localStorage.getItem("jobId");

  if (name == "" || name == undefined || surname == "" || surname == undefined || dob == "" || dob == undefined || jobId == "" || jobId == undefined) {
    d_err("I dati inseriti non sono validi. Controllare di aver compilato tutti i campi.");
  } else {
    data = {
      "name": name,
      "surname": surname,
      "gender": gender,
      "dob": dob,
      "job_id": jobId,
      "gun_license": gunLicense,
      "status": status
    }
    $.post("../../php/register_citizen.php", data, function(e) {
      e = JSON.parse(e);
      if (e["status"] === "success") {
        window.location.href = "../?action=registrationSuccessful";
      } else if (e["status"] === "failure"){
        d_err("Registrazione fallita. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err("Il server non risponde, probabilmente a causa di un errore interno.");
    });
  }
}

function toggleStatus() {
  if ($("#status").attr("class").includes("success")) {
    $("#status").attr("class", "btn btn-outline-danger").html("Ricercato");
  } else {
    $("#status").attr("class", "btn btn-outline-success").html("Non Ricercato");
  }
}

function setJob(jobName, jobId) {
  $("#citizenJob").val(jobName);
  localStorage.setItem("jobId", jobId);
}

function toggleGunLicense() {
  if ($("#gunLicense").attr("class").includes("success")) {
    $("#gunLicense").html("Porto d'armi non valido").attr("class", "btn btn-outline-danger");
  } else {
    $("#gunLicense").html("Porto d'armi valido").attr("class", "btn btn-outline-success");
  }
}
