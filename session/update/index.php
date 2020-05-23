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

  <!--formden.js communicates with FormDen server to validate fields and submit via AJAX -->
  <script type="text/javascript" src="https://formden.com/static/cdn/formden.js"></script>

  <!-- Special version of Bootstrap that is isolated to content wrapped in .bootstrap-iso -->
  <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />

  <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

  <style>.bootstrap-iso .formden_header h2, .bootstrap-iso .formden_header p, .bootstrap-iso form{font-family: Arial, Helvetica, sans-serif; color: black}.bootstrap-iso form button, .bootstrap-iso form button:hover{color: white !important;} .asteriskField{color: red;}</style>


  <link href="../../css/sb.css" rel="stylesheet" />
  <link href="../../css/ext.css" rel="stylesheet" />
  <link href="css/jobs.css" rel="stylesheet" />
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

  <script src="js/jobs.js"></script>
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
              echo "<a class='bg-light list-group-item' style='text-align: center;'><strong>Menu questore</strong></a><a href='../users' class='list-group-item list-group-item-action bg-light'><i class='fa fa-address-card' style='padding-right: 10px;'></i>Agenti</a>";
              echo "<a href='../jobs' class='list-group-item list-group-item-action bg-light'><i class='fa fa-gear' style='padding-right: 10px;'></i>Gestione lavori</a><a class='list-group-item list-group-item-action bg-light'><i class='fa fa-arrow-up' style='padding-right: 10px;'></i>Aggiornamenti</a>";
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
                <i class="fa fa-minus-circle"></i>
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
            <li class="breadcrumb-item active" aria-current="page">Aggiornamenti</li>
          </ol>
        </nav>
        <div class="animated fadeIn">
          <div class="row">
            <div class="col-sm-1" ></div>
            <div class="col-sm-10">
              <div class="card" id="jobList">
                <div class="card-header">Aggiornamento</div>
                <div class="card-body card-block" id="statusContainer">
                </div>
              <div class="card-footer">
                The Final Road
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

    </div>
  </div>

  <!-- /#wrapper -->

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
  </script>
</body>
</html>

<?php
  $prog     = "./update.sh";
  $descr    = [0 => ['pipe','r'], 1 => ['pipe','w'], 2 =>['pipe','w']];
  $pipes    = [];
  $process  = proc_open($prog, $descr, $pipes);

  if (is_resource($process)) {
    sleep(2);
    fclose($pipes[0]);
    $return = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
  }
  echo '<script>$("#statusContainer").append("' . str_replace('"', '&quot;', str_replace("\n", "<br/>", $return . " " . $errors)) . '");</script>';
 ?>
