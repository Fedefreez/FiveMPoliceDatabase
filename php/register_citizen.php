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

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = test_input($_POST["name"]);
    $surname = test_input($_POST["surname"]);
    $sex_id = test_input($_POST["gender"]);
    $dob = test_input($_POST["dob"]);
    $status = test_input($_POST["status"]);
    $gun_license = test_input($_POST["gun_license"]);

    if ($gun_license === true) {
      //TODO: funzione per aggiungere porto d'armi
    }

    echo json_encode(registerCitizen($name, $surname, $sex_id, $dob, 6, $status));
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
?>
