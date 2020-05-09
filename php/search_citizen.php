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
      error_log("API search_citizen contattata senza permesso. User UUID: " . $_SESSION["user_uuid"] . " IP: " . $_SERVER["REMOTE_ADDR"]);
      return false;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && loginCheck()) {
    $name = test_input($_POST["name"]);
    $surname = test_input($_POST["surname"]);

    echo json_encode(searchCitizen($name, $surname));
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Richiesta non valida."]);
  }
?>
