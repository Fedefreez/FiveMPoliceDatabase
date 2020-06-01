<?php session_start();
  require_once 'funs.php';

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (empty($data) && $data !== "0") {
      die(json_encode(["status"=>"failure", "reason"=>"Nessun dato ricevuto."]));
    }
    return $data;
  }

  function loginCheck() {
    if (isset($_SESSION["user_uuid"]) && $_SESSION["user_uuid"] != "-1") {
      return true;
    } else {
      error_log("API contattata senza permesso. User UUID: " . $_SESSION["user_uuid"] . " IP: " . $_SERVER["REMOTE_ADDR"]);
      return false;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && loginCheck()) {
    switch (test_input($_POST["type"])) {
      case "add":
        if (empty(($citizen_id = $_POST["citizen_id"])) || empty(($reason = $_POST["reason"]))) {
          echo json_encode(["status"=>"failure", "reason"=>"Nessun dato ricevuto."]);
        } else {
          echo json_encode(addCitizenCrime($citizen_id, $reason, null));
        }
        break;
      case "remove":
        if (empty(($crime_id = test_input($_POST["crime_id"])))) {
          echo json_encode(["status"=>"failure", "reason"=>"Nessun dato fornito."]);
        } elseif (isUserChief($_SESSION["user_uuid"]) {
          echo json_encode(removeCrime($crime_id));
        } else {
          echo json_encode(["status"=>"failure", "reason"=>"Non sei autorizzato a rimuovere crimini."]);
        }
        break;
      case "count":
        if (empty(($citizen_id = test_input($_POST["citizen_id"])))) {
          echo json_encode(["status"=>"failure", "reason"=>"Nessun dato ricevuto."]);
        } else {
          echo json_encode(countCrimes($citizen_id));
        }
        break;
      case "read":
        if (empty(($citizen_id = test_input($_POST["citizen_id"])))) {
          echo json_encode(["status"=>"failure", "reason"=>"Nessun dato ricevuto."]);
        } else {
          echo json_encode(getCitizenCrimes($citizen_id));
        }
        break;
      default:
        echo json_encode(["status"=>"failure", "reason"=>"Tipo di richiesta non valido."]);
        break;
    }
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Richiesta non valida."]);
  }
?>
