
<?php
  function loginCheck() {
    if (isset($_SESSION["user_uuid"]) && $_SESSION["user_uuid"] != "-1") {
      return true;
    } else {
      error_log("API contattata senza permesso. User UUID: " . $_SESSION["user_uuid"] . " IP: " . $_SERVER["REMOTE_ADDR"]);
      return false;
    }
  }


  if ($_SERVER["REQUEST_METHOD"] == "POST" && loginCheck()) {
    $citizens = getAllCitizens();
    if (isset($citizens["status"]) && $citizens["status"] === "failure") {
      echo json_encode(["status"=>"failure", "reason"=>"Nessun cittadino trovato."]);
    } else {
      $allCitizInfo = [];
      $i = 0;
      foreach ($citizens as $citizen) {
        $i++;
        $allCitizInfo[$i]["id"] = $citizen["id"];
        $allCitizInfo[$i]["name"] = $citizen["name"] . " " . $citizen["surname"];
      }

      echo json_encode(["status"=>"success", "data"=>$allCitizInfo]);
    }
  } else {
    echo json_encode(["status"=>"failure", "reason"=>"Invalid request."]);
  }
?>
