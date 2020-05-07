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

  <!-- pref_ic -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
              echo "<a class='bg-light list-group-item' style='text-align: center;'><strong>Menu questore</strong></a><a href='#' class='list-group-item list-group-item-action bg-light'><i class='fa fa-address-card' style='padding-right: 10px;'></i>Agenti</a>";
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
              </div>
              <div class="card-body card-block">
                <input class="form-control" placeholder="Nome e/o cognome..." id="citiz_search" />
              </div>
              <div class="card-footer" syle="width: 100%">
                <button class="btn btn-outline-success" onclick="searchUser();">Cerca</button>
                <button type="button" class="pos-right btn btn-outline-warning" onclick="window.location.href = 'citizen/'">Registra cittadino</button>
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
                        echo '<li class="list-group-item">';
                        echo '<div class="citiz-info-container"><span class="citiz-info-subclass citiz-info-subclass-name">' . $citizen["name"] . " " . $citizen["surname"] . '</span><span class="citiz-info-subclass"><button onclick="toggleStatus(' . (int) $citizen["id"] . ');" id="citizBtn' . (int)$citizen["id"] . '" class="citiz-info-subclass-status btn btn-outline-' . (($citizen["status_id"] === "2") ? "danger" : "success") . '">' . (($citizen["status_id"] === "2") ? "Ricercato" : "Non ricercato") . '</button></span><span class="citiz-info-subclass"><button class="citiz-info-subclass-info btn btn-primary" onclick="viewCitiz('. $citizen["id"] . ')">Info</button></span></div></li>';
                      }
                    }
                   ?>
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

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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
      document.getElementById("citiz_search").addEventListener("keyup", function(event) {
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
    });

  </script>

</body>

</html>
