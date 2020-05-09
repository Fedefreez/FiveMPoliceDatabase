<?php
  function connectDb(){
    $DBHOST = "localhost";
    $DBNAME = "tfr_police_db";
    $DBUSRN = "db_polizia";
    $DBPW = "tfr_polizia_8t5g8edsa;[d5AFRTT";

    try {
      $conn = new PDO("mysql:host=$DBHOST;dbname=$DBNAME", $DBUSRN, $DBPW);
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->exec("USE $DBNAME;");
      return $conn;
    } catch(PDOException $e) {
      die(json_encode(["status"=>"failure", "reason"=>"Connessione al database fallita. DEBUG: " . $e->getMessage()]));
    } finally {
      $conn = null;
    }
  }

 ?>
