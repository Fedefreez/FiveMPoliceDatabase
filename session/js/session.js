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
    try {
      e = JSON.parse(e);
    } catch (err) {
      d_err("Decodifica JSON fallita. Ragione: "  + err.message);
    }
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

function toggleStatusPopup(citizId) {
  $.post("../php/toggle_citizen_status.php", {"id": citizId, "wanted": $("#citizInfoStatus").attr("class").includes("success")}, function(e) {
    try {
      e = JSON.parse(e);
    } catch (err) {
      d_err("Decodifica JSON fallita. Ragione: "  + err.message);
    }
    if (e["status"] === "Success") {
      if ($("#citizInfoStatus").attr("class").includes("success")) {
        $("#citizInfoStatus").attr("class", "citiz-info-subclass-status btn btn-outline-danger").html("Ricercato");
      } else {
        $("#citizInfoStatus").attr("class", "citiz-info-subclass-status btn btn-outline-success").html("Non Ricercato");
      }
    } else {
      d_err_popup("Errore imprevisto: il server ha risposto " + e["reason"]);
    }
  });
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

function searchUser() {
  var key = $("#citizSearch").val();

  if (key == "" || key == undefined) {
    d_err("Inserire una chiave di ricerca valida.");
  } else {
    var keys = key.split(" ");
    if (keys.length != 2) {
      var name = keys[0].replace(".", " ");
      var surname = "%";
    } else {
      var name = keys[0].replace(".", " ");
      var surname = keys[1].replace(".", " ");
    }

    if (name !== "" || surname !== "") {
      $.post("../php/search_citizen.php", {"name": ((name == undefined) || (name == "") ? "%" : name), "surname": ((surname == undefined) || (surname == "") ? "%" : surname)}, function(e) {
        try {
          e = JSON.parse(e);
        } catch (err) {
          d_err("Decodifica JSON fallita. Ragione: "  + err.message);
        }


        if (e["ids"] === undefined) {
          $("#citizSearchNotFoundItem").show();
        } else {
          $("#citizSearchNotFoundItem").hide();
          $(".citiz-info-list-container").hide();

          for (var i = 0; i < e["ids"].length; i++) {
            $("#citizItem"+e["ids"][i]["id"]).show();
          }
        }
      }).fail(function() {
        d_err("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
      });
    } else {
      d_err("Inserire una chiave di ricerca valida.");
    }
  }
}

function resetSearch() {
  $("#citizSearchNotFoundItem").hide();
  var items = $(".citiz-info-list-container").show();
}

function editCitizData() {
  $("#editCitizDataBtn").attr("onclick", "saveCitizData();").html("Salva");
  $("#citizInfoName").attr("disabled", false);
  $("#citizInfoSurname").attr("disabled", false);
  $("#citizInfoDOB").attr("disabled", false);
  $("#citizInfoDOBBtn").attr("disabled", false);
  $("#citizInfoStatus").attr("disabled", false);
  $("#citizInfoGunLicense").attr("disabled", false);
  $("#citizInfoJobBtn").attr("disabled", false);
  localStorage.setItem("editingCitizenInfo", true);
}

function saveCitizData() {
  $("#editCitizDataBtn").attr("onclick", "editCitizData();").html("Modifica dati");
  $("#citizInfoName").attr("disabled", true);
  $("#citizInfoSurname").attr("disabled", true);
  $("#citizInfoDOB").attr("disabled", true);
  $("#citizInfoDOBBtn").attr("disabled", true);
  $("#citizInfoStatus").attr("disabled", true);
  $("#citizInfoGunLicense").attr("disabled", true);
  $("#citizInfoJobBtn").attr("disabled", true);
  if (localStorage.getItem("editingCitizenInfo")) {
    $("#citizInfoPopup").modal("toggle"); //dismiss modal
  }
  localStorage.setItem("editingCitizenInfo", false);

  if ($("#citizInfoStatus").attr("class").includes("success")) {
    $("#citizBtn"+localStorage.getItem("viewingCitizen")).html("Non Ricercato").attr("class", "btn btn-outline-success");
  } else {
    $("#citizBtn"+localStorage.getItem("viewingCitizen")).html("Ricercato").attr("class", "btn btn-outline-danger");
  }

  updateCitizen();
}

function viewCitiz(citizId) {
  $("#citizInfoCrimesList").empty();
  if (citizId == "" || citizId == undefined || !(/^\d+$/.test(citizId))) {
    d_err("Errore. Contatta un amministratore e salva la seguente informazione: {CitizId: }" + citizId);
  } else {
    localStorage.setItem("viewingCitizen", citizId);
    $.post("../php/citizen_info.php", {"citizen_id": citizId}, function (e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err("Decodifica JSON fallita. Ragione: "  + err.message);
      }
      if (e["status"] === "success") {
        $("#citizInfoName").val(e["info"][0]["name"]);
        $("#citizInfoSurname").val(e["info"][0]["surname"]);
        $("#citizInfoDOB").val(e["info"][0]["birth"].slice(0, -9));
        $("#citizInfoJob").val(e["info"]["job"]);

        switch (e["info"][0]["sex_id"]) {
          case "0":
            $("#citizInfoGender").val("Maschio");
            break;
          case "1":
            $("#citizInfoGender").val("Femmina");
            break;
          case "2":
            $("#citizInfoGender").val("Altro");
            break;
          default:
            $("#citizInfoGender").val("Errore. Contatta un amministratore.");
            break;
        }

        if (e["info"][0]["status_id"] === "2") {
          $("#citizInfoStatus").html("Ricercato").attr("class", "btn btn-outline-danger").attr("onclick", "toggleStatusPopup(" + citizId + ");");
        } else {
          $("#citizInfoStatus").html("Non Ricercato").attr("class", "btn btn-outline-success").attr("onclick", "toggleStatusPopup(" + citizId + ");");;
        }
        if (e["info"][0]["gun_license"] === "1") {
          $("#citizInfoGunLicense").html("Valido").attr("class", "btn btn-outline-success");
        } else {
          $("#citizInfoGunLicense").html("Non Valido").attr("class", "btn btn-outline-danger");
        }

        $.post("../php/crimes_manager.php", {"citizen_id": citizId, "type": "count"}, function(e) {
          try {
            e = JSON.parse(e);
          } catch (err) {
            d_err("Decodifica JSON fallita. Ragione: "  + err.message);
          }
          if (e["status"] === "success") {
            if (e["crimes_count"][0]["COUNT(*)"] === "0") {
              $("#citizInfoCrimesCount").attr("style", "color: green;");
              $("#citizInfoCrimesCount").html("Nessun reato commesso.");
            } else {
              $("#citizInfoCrimesCount").attr("style", "color: red;");
              $("#citizInfoCrimesCount").html(e["crimes_count"][0]["COUNT(*)"] + (e["crimes_count"][0]["COUNT(*)"] === "1" ? " reato commesso." : " reati commessi."));

              $.post("../php/crimes_manager.php", {"citizen_id": citizId, "type": "read"}, function(e) {
                try {
                  e = JSON.parse(e);
                } catch (err) {
                  d_err("Decodifica JSON fallita. Ragione: "  + err.message);
                }

                if (e["status"] === "success") {
                  for (var i = 0; i < e["crimes"].length; i++) {
                    $("#citizInfoCrimesList").prepend('<li id="crimeListGroupItem' + e["crimes"][i]["id"] + '" class="list-group-item"><span class="crime-info-container"><span class="citiz-info-crime-date">' + e["crimes"][i]["date"] + '</span><span class="citiz-info-crime-reason">' + e["crimes"][i]["reason"] +  '</span></span><button class="btn btn-outline-danger remove-crime-btn" onclick="removeCrime(' + e["crimes"][i]["id"] + ');">Rimuovi</button></li>');
                  }
                } else if (e["status"] === "failure") {
                  d_err_popup("Errore nella lettura dei crimini. Il server ha risposto con: " + e["reason"]);
                } else {
                  d_err_popup("Errore sconosciuto. Contatta un amministratore.");
                }
              }).fail(function() {
                $("#citizInfoCrimesList").attr("style", "color: red;").html("Errore");
                d_err_popup("Errore sconosciuto. Contatta un amministratore.");
              });
            }
          } else if (e["status"] === "failure") {
            $("#citizInfoCrimesCount").attr("style", "color: red;").html("Errore");
            d_err_popup("Errore nel conteggio dei crimini. Il server ha risposto con: " + e["reason"]);
          } else {
            $("#citizInfoCrimesCount").attr("style", "color: red;").html("Errore");
            d_err_popup("Errore sconosciuto. Contatta un amministratore.");
          }
        }).fail(function() {
          $("#citizInfoCrimesCount").html("Errore. Contatta un amministratore.");
          d_err_popup("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
        });
      } else if (e["status"] === "failure") {
        d_err_popup("Errore nella ricezione dei dati. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err_popup("Errore sconosciuto nella ricezione dei dati. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function d_err_popup(msg) {
  $("#popupAlerts").html("<div style='top: 10px;' class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>" + msg + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button></div>");
}

function updateCitizen() {
  var citizId = localStorage.getItem("viewingCitizen");

  var name = $("#citizInfoName").val();
  var surname = $("#citizInfoSurname").val();
  switch ($("#citizInfoGender").val()) {
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
  var dob = $("#citizInfoDOB").val();
  var gunLicense = $("#citizInfoGunLicense").attr("class").includes("success");
  var jobId = localStorage.getItem("jobId");
  var status = ($("#citizInfoStatus").attr("class").includes("danger") ? 2 : 1);

  if (citizId == "" || citizId == undefined || name == "" || name == undefined || surname == "" || surname == undefined || dob == "" || dob == undefined || jobId == "" || jobId == undefined) {
    d_err("Inserire dati validi.");
  } else {
    var data = {
      "citizen_id": citizId,
      "name": name,
      "surname": surname,
      "gender": gender,
      "dob": dob,
      "job_id": jobId,
      "gun_license": gunLicense,
      "status_id": status
    }

    $.post("../php/update_citizen.php", data, function(e) {
      e = JSON.parse(e);
      if (e["status"] === "success") {
        d_scc("Modifiche salvate.");
      } else if (e["status"] === "failure") {
        d_err("Errore nel salvataggio delle modifiche. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function setJob(name, id) {
  $("#citizInfoJob").val(name);
  localStorage.setItem("jobId", id);
}

function removeCitizen() {
  var citizId = localStorage.getItem("viewingCitizen");

  $.post("../php/remove_citizen.php", {"citizen_id": citizId}, function(e) {
    try {
      e = JSON.parse(e);
    } catch (err) {
      d_err("Decodifica JSON fallita. Ragione: "  + err.message);
    }
    if (e["status"] === "success") {
      d_scc("Utente rimosso.");
      $("#citizItem"+citizId).remove();
    } else if (e["status"] === "failure"){
      d_err("Errore nella rimozione del cittadino. Il server ha risposto con: " + e["reason"]);
    } else {
      d_err("Errore sconosciuto. Contatta un amministratore.");
    }
    $("#citizInfoPopup").modal("toggle");
  }).fail(function() {
    d_err("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
    $("#citizInfoPopup").modal("toggle");
  });
}

$(document).ready(function() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);

    if (check) {
      $(".citiz-info-subclass-status").hide();
    }
});

function prepareCrimeAddition() {
  if (!$("#addCrimeListGroupItem").length) {
    $("#citizInfoCrimesList").append('<li class="list-group-item" id="addCrimeListGroupItem"><div class="row"><input placeholder="Ragione del crimine..." class="form-control col-sm-9" id="citizInfoAddCrimeReason" /><span class="col-sm-1" role="separator"></span><button class="btn btn-danger" onclick="addCrime();">Conferma</button></div></li>')
  }
}

function addCrime() {
  var reason = $("#citizInfoAddCrimeReason").val();
  var citizId = localStorage.getItem("viewingCitizen");

  if (reason == "" || reason == undefined || citizId == "" || citizId == undefined) {
    d_err_popup("Inserire un crimine valido.");
  } else{
    $.post("../php/crimes_manager.php", {"citizen_id": citizId, "reason": reason, "type": "add"}, function(e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err("Decodifica JSON fallita. Ragione: "  + err.message);
      }
      if (e["status"] === "success") {
        viewCitiz(citizId);
      } else if (e["status"] === "failure") {
        d_err_popup("Errore nell'aggiunta del crimine. Il server ha risposto con: "+ e["reason"]);
      } else {
        d_err_popup("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
    });
  }
}

function removeCrime(crimeId) {
  if (crimeId == "" || crimeId == undefined) {
    d_err_popup("Errore nella rimozione. Ricarica la pagina e se il problema persiste contatta un amministratore.");
  } else {
    $.post("../php/crimes_manager.php", {"crime_id": crimeId, "type": "remove"}, function(e) {
      try {
        e = JSON.parse(e);
      } catch (err) {
        d_err("Decodifica JSON fallita. Ragione: "  + err.message);
      }
      if (e["status"] === "success") {
        $("#crimeListGroupItem" + crimeId).remove();
        viewCitiz(localStorage.getItem("viewingCitizen"));
      } else if (e["status"] === "failure"){
        d_err_popup("Errore nella rimozione del crimine. Il server ha risposto con: " + e["reason"]);
      } else {
        d_err_popup("Errore sconosciuto. Contatta un amministratore.");
      }
    }).fail(function() {
      d_err_popup("Il server non risponde, forse a causa di un errore interno. Contatta un amministratore.");
    });
  }
}
