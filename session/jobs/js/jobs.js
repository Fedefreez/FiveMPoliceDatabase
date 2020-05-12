function removeJob(jobId) {
  var data = {
    "r_type": "remove",
    "job_id": jobId
  }

  $.post("../../php/job_manager.php", data, function(e) {
    try {
      e = JSON.parse(e);
    } catch (err) {
      d_err("Decodifica JSON fallita. Ragione: "  + err.message);
    }
    if (e["status"] === "success") {
      $("#job" + jobId).remove();
      d_not("Lavoro rimosso.");
    } else {
      d_err("Errore nella rimozione. Il server ha risposto con: " + e["reason"]);
    }
  }).fail(function() {
    d_err("Il server non risponde, probabilmente a causa di un erroe interno. Contatta un Amministratore.");
  });
}

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

function addJob() {
  var jobName = $("#jobNameInput").val();
  if (jobName == undefined || jobName == "") {
    d_err("Nome lavoro non valido.");
    return;
  }
  var data = {
    "r_type": "add",
    "job_name": jobName
  }

  $.post("../../php/job_manager.php", data, function(e) {
    try {
      e = JSON.parse(e);
    } catch (err) {
      d_err("Decodifica JSON fallita. Ragione: "  + err.message);
    }
    if (e["status"] === "success") {
      $("#jobListJobsContainer").append('<li class="list-group-item" id="job' + e["job_id"] + '"><div class="job-container"><span class="job-name">' + jobName + '</span><span class="remove-job-btn-container"><button class="btn btn-danger" onclick="removeJob(' + e["job_id"] + ');">Rimuovi</button></span></div></li>');
    } else if (e["status"] === "failure"){
      d_err("Errore nell'aggiunta del lavoro. Il server ha risposto con: " + e["reason"]);
    } else {
      d_err("Errore sconosciuto. Contatta un Amministratore.");
    }
    $("#addJob").hide();
    $("#jobList").show();
  }).fail(function() {
    d_err("Il server non risponde, probabilmente a causa di un errore interno.");
  });
}

function toggleJobAddition() {
  $("#jobList").hide();
  $("#addJob").show();
}
