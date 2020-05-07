<?php session_start();
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_SESSION["user_uuid"])) {
      if ($_SESSION['user_uuid'] !== '-1') {
        unset($_SESSION["user_uuid"]);
        echo "Success";
      }
    }
  }
?>
