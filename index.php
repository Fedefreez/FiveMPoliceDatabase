<?php session_start();
  if (isset($_SESSION["user_uuid"]) && $_SESSION["user_uuid"] !== "-1") {
    die("<script>window.location.href='session/';</script>");
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

  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <link href="css/sb.css" rel="stylesheet" />
  <link href="css/ext.css" rel="stylesheet" />
  <!-- pref_ic -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- animated logo -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
  <script src="http://www.jqueryscript.net/demo/Image-Loading-Animation-Plugin-with-jQuery-CSS3-LoadGo/loadgo.js"></script>


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

  <script src="js/login.js"></script>
</head>

<body>
  <noscript>
    É necessario avere un broswer con javascript per poter utilizzare il sito.
  </noscript>
  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading"><img width="175" src="css/images/polizia.png"/></div>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action bg-light"><i class="fa fa-laptop" style="padding-right: 10px;"></i>Login</a>
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
              <button type="button" id="home-btn" class="btn btn-primary" style="border: none;">
                <i class="fa fa-home">
                </i>
                Home
              </button>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid">
        <div id="alerts"></div>
        <div id="loginWrapper">
          <br/>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active" aria-current="page">Benvenuto nel database polizia v2.54</li>
            </ol>
          </nav>
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-3" ></div>
              <div class="col-sm-6">
                <div class="card">
                  <div class="card-header">Accesso</div>
                  <div class="card-body card-block">
                    <input type="number" class="form-control" placeholder="Inserire il pin..." id="user_pin"/>
                  </div>
                  <div class="card-footer">
                    <button class="btn btn-primary" onclick="login()">Accedi</button><span style="position: relative; float: right; margin-top: 4px; color: #7a7a7a;">The Final Road</span>
                  </div>
                </div>
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
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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
