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
    $name = test_input($_POST["name"]);
    $surname = test_input($_POST["surname"]);
    $sex_id = test_input($_POST["gender"]);
    $dob = test_input($_POST["dob"]);
    $status = test_input($_POST["status"]);
    $gun_license = test_input($_POST["gun_license"]);
    $job_id = test_input($_POST["job_id"]);


    if ((($result = registerCitizen($name, $surname, $sex_id, $dob, $job_id, $status))["status"] === "success")) {
      if (($result2 = setGunLicense($result["id"], $gun_license))["status"] === "success") {
        echo json_encode(["status"=>"success"]);
      } else {
        echo json_encode($result2);
      }
    } else {
      echo json_encode($result);
    }
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
?>
