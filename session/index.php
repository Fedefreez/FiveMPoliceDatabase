<?php session_start();
  if (!isset($_SESSION["user_uuid"]) && $_SESSION["user_uuid"] !== "-1") {
    die("<script>window.location.href='../';</script>");
  }

  require "../php/funs.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Menu Polizia TFR">
  <meta name="author" content="Fede">

  <title>Database Polizia - TFR</title>

  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <link href="../css/sb.css" rel="stylesheet" />
  <link href="../css/ext.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />

  <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>

  <style>.bootstrap-iso .formden_header h2, .bootstrap-iso .formden_header p, .bootstrap-iso form{font-family: Arial, Helvetica, sans-serif; color: black}.bootstrap-iso form button, .bootstrap-iso form button:hover{color: white !important;} .asteriskField{color: red;}</style>


  <!-- pref_ic -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="../vendor/jquery/jquery.min.js"></script>

  <script src="js/session.js"></script>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading"><img width="175" src="../css/images/polizia.png"/></div>
      <div class="list-group list-group-flush">

        <span><span class="list-group-item" style='text-align: center;'><strong>Menu agente</strong></span><span>
        <a class="list-group-item list-group-item-action bg-light"><i class="fa fa-laptop" style="padding-right: 10px;"></i>Database</a>
        <a href="citizen/" class="list-group-item list-group-item-action bg-light"><i class="fa fa-plus" style="padding-right: 10px;"></i>Registra cittadino</a>
        <?php
            if (isUserChief($_SESSION["user_uuid"])) {
              $isUserChief = true;
              echo "<a class='bg-light list-group-item' style='text-align: center;'><strong>Menu questore</strong></a><a href='users' class='list-group-item list-group-item-action bg-light'><i class='fa fa-address-card' style='padding-right: 10px;'></i>Agenti</a>";
              echo "<a href='jobs' class='list-group-item list-group-item-action bg-light'><i class='fa fa-gear' style='padding-right: 10px;'></i>Gestione lavori</a>";
            } else {
              $isUserChief = false;
            }
         ?>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <button class="btn btn-primary" id="menu-toggle">Chiudi menu</button>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item active">
              <button type="button" id="home-btn" class="btn btn-primary" style="border: none;" onclick="window.location.href='../';">
                <i class="fa fa-home">
                </i>
                Home
              </button>
            </li>
            <pre> </pre>
            <li class="nav-item">
              <button class="btn btn-primary logout-btn" id="logout" onclick="logout()">
                <i class="fa fa-minus-circle">
                </i>
                Logout</button>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid">
        <div id="alerts"></div>
        <div class='sufee-alert alert with-close alert-info alert-dismissible fade show'>

          <?php
            $info = getUserInfo($_SESSION["user_uuid"]);
            if (isset($info["status"]) && $info["status"] === "General failure") {
              echo "<script>setTimeout(function(){location.reload();}, 5000);</script>Errore generale. Riavvio pagina in 5 secondi...";
            } else {
              echo "Acceso eseguito come<strong> " . $info["name"] . " " . $info["surname"] . "</strong>, con ruolo di <strong>" . $info["role"] . "</strong>.";
            }
             ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Gestione database</li>
            <li class="breadcrumb-item active" aria-current="page">Cittadini</li>
          </ol>
        </nav>
        <div class="row">
          <div class="col-sm-5">
            <div class="card">
              <div class="card-header">
                <span style="font-size: 20px;"><strong>Ricerca Cittadino</strong></span>
                <a tabindex="0" style="position: relative; float: right; padding-top: 3px; padding-bottom: 3px; padding-left:13px; padding-right: 13px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover" data-trigger="focus" title="Avviso" data-content="Se ci sono spazi nel nome / cognome sostituirli con un . (ad es Paperon De Paperoni => Paperon De.Paperoni)">!</a>
              </div>
              <div class="card-body card-block">
                <input class="form-control" placeholder="Nome e/o cognome..." id="citizSearch" />
              </div>
              <div class="card-footer" syle="width: 100%">
                <button class="btn btn-outline-success" onclick="searchUser();">Cerca</button>
                <button type="button" class="pos-right btn btn-outline-warning" onclick="resetSearch();">Reset</button>
              </div>
            </div>
          </div>
          <div class="col-sm-7">
            <div class="card">
              <div class="card-header">
                <span style="font-size: 20px;"><strong>Cittadini registrati</strong></span>
              </div>
              <div class="card-body card-block">
                <ul class="list-group">
                  <?php
                    $citizens = getAllCitizens();
                    if (isset($citizens["status"]) && $citizens["status"] === "failure") {
                      echo '<li class="list-group-item">Nessun cittadino trovato.</li>';
                    } else {
                      foreach ($citizens as $citizen) {
                        echo '<li class="list-group-item citiz-info-list-container" id="citizItem' . $citizen["id"] .'">';
                        echo '<div class="citiz-info-container"><span class="citiz-info-subclass citiz-info-subclass-name">' . $citizen["name"] . " " . $citizen["surname"] . '</span><span class="citiz-info-subclass"><button onclick="toggleStatus(' . (int) $citizen["id"] . ');" id="citizBtn' . (int)$citizen["id"] . '" class="citiz-info-subclass-status btn btn-outline-' . (($citizen["status_id"] === "2") ? "danger" : "success") . '">' . (($citizen["status_id"] === "2") ? "Ricercato" : "Non ricercato") . '</button><span class="col-sm-1" role="separator"></span><button class="citiz-info-subclass-info btn btn-primary" data-toggle="modal" data-target="#citizInfoPopup" onclick="viewCitiz('. $citizen["id"] . ')">Info</button></span></div></li>';
                      }
                    }
                   ?>
                   <li id="citizSearchNotFoundItem" class="list-group-item" style="display: none;">Nessun riscontro.</li>
                </ul>
              </div>
              <div class="card-footer" syle="width: 100%">
                Database polizia v2.54 - The Final Road
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <div class="modal fade" id="citizInfoPopup" tabindex="-1" role="dialog" aria-labelledby="citizInfoPopup" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Info cittadino</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="popupAlerts"></div>
          Nome: <input id="citizInfoName" value="Caricamento..." class="form-control" disabled/>
          Cognome: <input id="citizInfoSurname" value="Caricamento..." class="form-control" disabled/>
          <br/>
          Data di nascita:
          <div class="input-group mb-3 col-sm-15">
           <div class="input-group-prepend">
             <button class="btn btn-outline-secondary" id="citizInfoDOBBtn" type="button" disabled><i class="fa fa-calendar"></i><span style="padding-left: 5px;">Data di nascita</span></button>
            </div>
           <input class="form-control" id="citizInfoDOB" name="date" value="Caricamento..." type="text" disabled/>
          </div>
          Sesso:
          <div class="input-group mb-3 col-sm-15">
            <div class="input-group-prepend">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sesso</button>
              <div class="dropdown-menu">
                <a class="dropdown-item" onclick="$('#citizInfoGender').val('Maschio');">Maschio</a>
                <a class="dropdown-item" onclick="$('#citizInfoGender').val('Femmina');">Femmina</a>
                <a class="dropdown-item" onclick="$('#citizInfoGender').val('Altro');">Altro</a>
              </div>
            </div>
            <input type="text" class="form-control" id="citizInfoGender" value="Maschio" disabled>
          </div>
          Lavoro:
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="citizInfoJobBtn" disabled><i class="fa fa-briefcase"></i><span style="padding-left: 5px;">Lavoro</span></button>
              <div class="dropdown-menu">
                <?php
                  if (($jobs = getAllJobs())["status"] === "success") {
                    foreach ($jobs["jobs"] as $job) {
                      echo '<a class="dropdown-item" onclick="setJob(&quot;' . $job["name"] . "&quot;," . $job["id"] . ');">' . $job["name"] . '</a>';
                    }
                  } else {
                    echo "<a class='dropdown-item'>Errore</a> <script>d_err('Errore nella rilevazione dei lavori. Il server ha risposto con: " . $jobs["reason"] . "');</script>";
                  }
                 ?>
              </div>
            </div>
            <input type="text" class="form-control" value="Caricamento..." id="citizInfoJob" disabled>
          </div>
          Stato: <br/><button type="button" class="btn btn-outline-success" id="citizInfoStatus" disabled>Caricamento...</button><br/>
          <br/>Porto d'armi: <br/><button type="button" onclick='$(this).html(($(this).attr("class").includes("danger") ? "Valido" : "Non Valido")).attr("class", $(this).attr("class").includes("danger") ? "btn btn-outline-success" : "btn btn-outline-danger");' class="btn btn-outline-success" id="citizInfoGunLicense" disabled>Caricamento...</button><br/>
          <br/>Reati:<button class="btn btn-danger" id="citizInfoAddCrimeBtn" style="position: relative; float: right;" onclick="prepareCrimeAddition();">Aggiungi un reato</button><br/>
          <span id="citizInfoCrimesCount">Caricamento...</span>
          <ul class="list-group" id="citizInfoCrimesList">
          </ul>
        </div>
        <div class="modal-footer">
          <?php
            if ($isUserChief) {
              echo '<button type="button" onclick="removeCitizen();" class="btn btn-danger">Rimuovi cittadino dal database</button>';
            }
           ?>
          <button class="btn btn-info" id="editCitizDataBtn" onclick="editCitizData()">Modifica dati</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>


  <script>
    localStorage.setItem("jobId", 6);

    var toggled = false;

    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
      if (toggled) {
        $(this).html("Chiudi menu");
      } else {
        $(this).html("Apri menu");
      }
      toggled = !toggled;
    });

    $(document).ready(function() {
      $('[data-toggle="popover"]').popover();
      document.getElementById("citizSearch").addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
          searchUser();
        }
      });
      interval = setInterval(function() {
        if (document.getElementsByTagName('html')[0].getAttribute('class') === "translated-ltr") {
          d_err("Se le scritte all'interno del sito sembrano sfasate, disabilita Google Traduttore.");
          clearInterval(interval);
        }
      }, 1000);
      var date_input=$('input[name="date"]'); //our date input has the name "date"
  		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
  		date_input.datepicker({
  			format: 'yyyy-mm-dd',
  			container: container,
  			todayHighlight: true,
  			autoclose: true,
  		});

      $('#citizInfoPopup').on('hidden.bs.modal', function () {
          if (localStorage.getItem("editingCitizenInfo") === "true") {
            alert("Attenzione: non hai salvato le modifiche alle informazione del cittadino, la prossima volta ricorda di cliccare salva prima di chiudere il menú per evitare problemi.\nLe modifiche sono state salvate automaticamente.");
            saveCitizData();
          }
      });
    });
    <?php
      if (isset($_GET["action"]) && $_GET["action"] === "registrationSuccessful") {
        echo "d_scc('Utente registrato.')";
      }
     ?>

  </script>

</body>

</html>
