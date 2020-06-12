<?php session_start();
  require "../../php/funs.php";

  if (!isset($_SESSION["user_uuid"]) || $_SESSION["user_uuid"] === "-1") {
    die("<script>window.location.href='/';</script>");
  } else if (!isUserChief($_SESSION["user_uuid"])) {
    die("<h1>Non sei autorizzato a visualizzare questa pagina. L'incidente é stato registrato.</h1>");
    //todo registrazione incidenti
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Menu Polizia TFR">
  <meta name="author" content="Fede">

  <title>Database Polizia - TFR</title>

  <script src="../../vendor/jquery/jquery.min.js"></script>

  <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- <script src="../../vendor/jquery-ui/jquery-ui.js"></script> -->

  <link href="../../css/sb.css" rel="stylesheet" />
  <link href="../../css/ext.css" rel="stylesheet" />
  <link href="css/users.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.js"/>
  <!-- pref_ic -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- removing webkit -->
  <style>
    input.hide-clear[type=search]::-ms-clear,
    input.hide-clear[type=search]::-ms-reveal {
    display: none;
    width: 0;
    height: 0;
    }

    /* Chrome */
    input.hide-clear[type="search"]::-webkit-search-decoration,
    input.hide-clear[type="search"]::-webkit-search-cancel-button,
    input.hide-clear[type="search"]::-webkit-search-results-button,
    input.hide-clear[type="search"]::-webkit-search-results-decoration {
    display: none;
    }



    /* firefox*/
    input[type='number'] {
    -moz-appearance:textfield;
    }
    /* Webkit browsers like Safari and Chrome */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
  </style>

  <script src="js/users.js"></script>
</head>

<body>
  <noscript>
    É necessario avere un broswer con javascript per poter utilizzare il sito.
  </noscript>
  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading"><img width="175" src="../../css/images/polizia.png"/></div>
      <div class="list-group list-group-flush">

        <span><span class="list-group-item" style='text-align: center;'><strong>Menu agente</strong></span><span>
        <a href="../" class="list-group-item list-group-item-action bg-light"><i class="fa fa-laptop" style="padding-right: 10px;"></i>Database</a>
        <a href="../citizen" class="list-group-item list-group-item-action bg-light"><i class="fa fa-plus" style="padding-right: 10px;"></i>Registra cittadino</a>
        <?php
            if (isUserChief($_SESSION["user_uuid"])) {
              echo "<a class='bg-light list-group-item' style='text-align: center;'><strong>Menu questore</strong></a><a class='list-group-item list-group-item-action bg-light'><i class='fa fa-address-card' style='padding-right: 10px;'></i>Agenti</a>";
              echo "<a href='../jobs' class='list-group-item list-group-item-action bg-light'><i class='fa fa-gear' style='padding-right: 10px;'></i>Gestione lavori</a>";
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
              <button onclick="window.location.href='../';" type="button" id="home-btn" class="btn btn-primary" style="border: none;">
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
        <br/>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"><a href="../">Gestione database</a></li>
            <li class="breadcrumb-item active" aria-current="page">Utenti</li>
          </ol>
        </nav>
        <div class="animated fadeIn">
          <div class="card">
            <div class="card-header">
              Gestione utenti
            </div>
            <div class="card-body card-block">
              <ul class="list-group" id="userList">
                <?php
                  if (($users = getAllUsers())["status"] === "success") {
                    foreach ($users["users"] as $user) {
                      echo '<li class="list-group-item" id="user' . $user["id"] . '">';
                      echo '<div class="user-container"><span class="user-name-and-surname">' . $user["name"] . " " . $user["surname"] . '</span><span class=user-info-btn-container"><button class="btn btn-danger" onclick="removeUser(' . $user["id"] . ');">Rimuovi</button><span class="col-sm-1" role="separator"></span><button class="btn btn-primary" data-toggle="modal" data-target="#userInfoPopup" onclick="viewUser(' . $user["id"] . ');">Info</button></span></div></li>';
                    }
                  } else {
                    echo $jobs["reason"];
                  }
                 ?>
             </ul>
            </div>
            <div class="card-footer">
              Database polizia v2.55 - The Final Road
            </div>
          </div>
          <span class="col-sm-1" role="separator"></span>
          <div class="card">
            <div class="card-header">
              Creazione utente
            </div>
            <div class="card-body card-block">
              <div class="row">
                <span class="col-sm-1" role="separator"></span>
                <input class="form-control col-sm-4" placeholder="Nome" id="userName" />
                <span class="col-sm-1" role="separator"></span>
                <input class="form-control col-sm-4" placeholder="Cognome" id="userSurname" />
              </div>
              <span class="col-sm-1" role="separator"></span>
              <div class="row">
                <span class="col-sm-1" role="separator"></span>
                <input class="form-control col-sm-4" type="number" placeholder="PIN" id="userPIN" />
                <span class="col-sm-1" role="separator"></span>
                <div class="input-group mb-3 col-sm-4">
                  <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ruolo</button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" onclick="$('#userRole').val('Questore'); localStorage.setItem('RoleId', 1);">Questore</a>
                      <a class="dropdown-item" onclick="$('#userRole').val('Ispettore capo'); localStorage.setItem('RoleId', 2);">Ispettore capo</a>
                      <a class="dropdown-item" onclick="$('#userRole').val('Tenente'); localStorage.setItem('RoleId', 3);">Tenente</a>
                      <a class="dropdown-item" onclick="$('#userRole').val('Agente Scelto'); localStorage.setItem('RoleId', 14);">Agente Scelto</a>
                      <a class="dropdown-item" onclick="$('#userRole').val('Recluta'); localStorage.setItem('RoleId', 4);">Recluta</a>
                      <a class="dropdown-item" onclick="$('#userRole').val('Amministratore'); localStorage.setItem('RoleId', 5);">Amministratore</a>
                    </div>
                  </div>
                  <input type="text" class="form-control" id="userRole" value="Recluta" disabled>
                </div>
              </div>
              </div>
            <div class="card-footer">
              <button class="btn btn-outline-success" onclick="registerDeputy()">Aggiungi agente</button>
            </div>
          </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

    </div>
  </div>

  <!-- /#wrapper -->

  <div class="modal fade" id="userInfoPopup" tabindex="-1" role="dialog" aria-labelledby="userInfoPopup" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Info utente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="popupAlerts"></div>
          Nome: <input id="userInfoName" value="Caricamento..." class="form-control" disabled/>
          Cognome: <input id="userInfoSurname" value="Caricamento..." class="form-control" disabled/>
          PIN: <input id="userInfoPIN" type="number" value="Caricamento..." class="form-control" disabled/>
          <br/>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="userInfoRoleBtn" disabled><i class="fa fa-address-book"></i><span style="padding-left: 5px;">Ruolo</span></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Questore'); localStorage.setItem('RoleId', 1);">Questore</a>
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Ispettore capo'); localStorage.setItem('RoleId', 2);">Ispettore capo</a>
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Tenente'); localStorage.setItem('RoleId', 3);">Tenente</a>
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Agente Scelto'); localStorage.setItem('RoleId', 14);">Agente Scelto</a>
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Recluta'); localStorage.setItem('RoleId', 4);">Recluta</a>
                <a class="dropdown-item" onclick="$('#userInfoRole').val('Amministratore'); localStorage.setItem('RoleId', 5);">Amministratore</a>
              </div>
            </div>
            <input type="text" class="form-control" value="Caricamento..." id="userInfoRole" disabled>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-info" id="editUserInfoBtn" onclick="unlockUserInfo()">Modifica dati</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap core JavaScript -->
  <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

  <script>
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
      localStorage.clear();
      localStorage.setItem("RoleId", 4);
      interval = setInterval(function() {
        if (document.getElementsByTagName('html')[0].getAttribute('class') === "translated-ltr") {
          d_err("Se le scritte all'interno del sito sembrano sfasate, disabilita Google Traduttore.");
          clearInterval(interval);
        }
      }, 1000);

      $('#userInfoPopup').on('hidden.bs.modal', function () {
          if (localStorage.getItem("editingCitizenInfo") === "true") {
            alert("Attenzione: non hai salvato le modifiche alle informazioni del cittadino, la prossima volta ricorda di cliccare salva prima di chiudere il menú per evitare problemi.\nLe modifiche sono state salvate automaticamente.");
            lockUserInfo();
          }
      });
    });
  </script>

</body>

</html>
