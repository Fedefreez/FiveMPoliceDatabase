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
      error_log("API contattata senza permesso. User UUID: " . $_SESSION["user_uuid"] . " IP: " . $_SERVER["REMOTE_ADDR"]);
      return false;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && loginCheck()) {
    $user_id = test_input($_POST["user_id"]);
    $name = test_input($_POST["name"]);
    $surname = test_input($_POST["surname"]);
    $pin = test_input($_POST["pin"]);
    $role_id = test_input($_POST["role_id"]);

    echo json_encode(editUserInfo($user_id, $name, $surname, $pin, $role_id));
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
?>
