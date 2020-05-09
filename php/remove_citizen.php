<?php session_start();
  require_once 'funs.php';

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (empty($data)) {
      die(json_encode(["status"=>"failure", "reason"=>"Nessun dato ricevuto."]));
    }
    return $data;
  }

  function loginCheck() {
    if (isset($_SESSION["user_uuid"]) && $_SESSION["user_uuid"] != "-1") {
      if (isUserChief($_SESSION["user_uuid"])) {
        return true;
      } else {
        return false;
      }
    } else {
      error_log("API remove_citizen contattata senza permesso. User UUID: " . $_SESSION["user_uuid"] . " IP: " . $_SERVER["REMOTE_ADDR"]);
      return false;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && loginCheck()) {
    $citizen_id = test_input($_POST["citizen_id"]);

    echo json_encode(removeCitizen($citizen_id));
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
?>
