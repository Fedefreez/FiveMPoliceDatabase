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

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (($r_type = test_input($_POST["r_type"])) === "add") {
      echo json_encode(addJob(test_input($_POST["job_name"])));
    } else if ($r_type === "remove") {
      echo json_encode(removeJob(test_input($_POST["job_id"])));
    } else {
      echo json_encode(["status"=>"failure", "reason"=>"Richiesta non formata correttamente."]);
    }
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Richiesta non valida."]);
  }
?>
