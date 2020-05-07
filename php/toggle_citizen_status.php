<?php session_start();
  require_once 'funs.php';

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (empty($data)) {
      die(json_encode(["status"=>"failure", "reason"=>"Nessun dato inviato."]));
    }

    return $data;
  }



  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $citizen_id = test_input($_POST["id"]);
    $wanted = test_input($_POST["wanted"]);

    echo json_encode(setCitizenStatus($citizen_id, ($wanted === "true" ? 2 : 1)));
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
 ?>
