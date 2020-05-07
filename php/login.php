<?php session_start();
  require_once 'funs.php';

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (!ctype_digit($data)) {
      $_SESSION["user_uuid"] = "-1";
      die(json_encode(["status"=>"success", "accepted"=>"false"]));
    }
    return $data;
  }



  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pin = test_input($_POST["pin"]);

    if (testFor($pin)) {
      echo json_encode(["status"=>"success", "accepted"=>"true"]);
      $_SESSION["user_uuid"] = getUserUUID($pin);
    } else {
      echo json_encode(["status"=>"success", "accepted"=>"false"]);
      $_SESSION["user_uuid"] = "-1";
    }
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
    $_SESSION["user_uuid"] = "-1";
  }
 ?>
