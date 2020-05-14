<?php
  require_once 'core.php';

  function testFor($pin) : bool {
    try {
      $query = connectDb()->prepare("SELECT * FROM user WHERE pin = :pin");
      $query->bindParam(":pin", $pin);
      $query->execute();
      if (!empty($query->fetchAll()[0])) {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      error_log("Errore funzione testFor: " . $e->getMessage());
      die(json_encode(["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."]));
    } finally {
      $conn = null;
    }
  }

  function getUserInfo($user_UUID) : Array {
    try {
      $query = connectDb()->prepare("SELECT user.name, user.surname, user.pin, user_role.name AS role FROM user, user_role WHERE (user.user_role_id = user_role.id) AND (user.id = :id)");
      $query->bindParam(":id", $user_UUID);
      $query->execute();
      if (!empty(($result = $query->fetchAll()[0]))) {
        return ["status"=>"success", "info"=>$result];
      } else {
        return ["status"=>"General failure"];
      }
    } catch (PDOException $e) {
      return ["status"=>"General failure"];
    } finally {
      $conn = null;
    }
  }

  function editUserInfo($user_UUID, $name, $surname, $pin, $role_id) {
    try {
      $query = connectDb()->prepare("UPDATE user SET name = :name, surname = :surname, pin = :pin, user_role_id = :role_id WHERE id = :id");
      $query->bindParam(":name", $name);
      $query->bindParam(":surname", $surname);
      $query->bindParam(":pin", $pin);
      $query->bindParam(":id", $user_UUID);
      $query->bindParam(":role_id", $role_id);
      $query->execute();

      return ["status"=>"success"];
    } catch (PDOException $e) {
      error_log("Errore funzione editUserInfo: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  function getUserUUID($pin) : int {
    try {
      $query = connectDb()->prepare("SELECT id FROM user WHERE pin = :pin");
      $query->bindParam(":pin", $pin);
      $query->execute();
      if (!empty(($id = $query->fetchAll()[0]))) {
        return (int) $id[0];
      } else {
        die(json_encode(["status"=>"failure", "reason"=>"Impossibile trovare UUID utente."]));
      }
    } catch (PDOException $e) {
      error_log("Errore funzione getUserUUID: " . $e->getMessage());
      die(json_encode(["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."]));
    } finally {
      $conn = null;
    }
  }

  function getAllCitizens() : Array {
    try {
      $query = connectDb()->query("SELECT * FROM citizen");
      if (!empty(($citizens = $query->fetchAll()))) {
        return $citizens;
      } else {
        return ["status"=>"failure", "reason"=>"Nessun cittadino trovato."];
      }
    } catch (PDOException $e) {
      error_log("Errore funzione getAllCitizens: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  function setCitizenStatus($citizen_id, int $wanted) : Array {
    try {
      $query = connectDb()->prepare("UPDATE citizen SET status_id = :status_id WHERE id = :id");
      $query->bindParam(":status_id", $wanted);
      $query->bindParam(":id", $citizen_id);
      $query->execute();

      return ["status"=>"Success"];
    } catch (PDOException $e) {
      error_log("Errore funzione setCitizenStatus: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  function registerCitizen($name, $surname, $sex_id, $birth, $role, $status) : Array {
    try{
      $conn = connectDb();
      $query = $conn->prepare("INSERT INTO citizen (name, surname, sex_id, birth, citizen_role_id, status_id) VALUES (:name, :surname, :sex, :birth, :role, :status)");
      $date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $birth)));
      $query->bindParam(":name", $name);
      $query->bindParam(":surname", $surname);
      $query->bindParam(":sex", $sex_id);
      $query->bindParam(":birth", $date);
      $query->bindParam(":role", $role);
      $query->bindParam(":status", $status);
      $query->execute();

      $query = $conn->query("SELECT last_insert_id()");
      $result = $query->fetchAll();

      if (empty($result)) {
        return ["status"=>"failure", "reason"=>"Nessun id trovato."];
      } else {
        error_log("Returning last_insert_id: " . $result[0]["last_insert_id()"]);
        return ["status"=>"success", "id"=>$result[0]["last_insert_id()"]];
      }
    } catch (PDOException $e){
      error_log("Errore funzione registerCitizen: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita per la registrazione del cittadino. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  /**
   * Rimuove un cittadino dato il suo id.
   *
   * @param $id L'id del cittadino da eliminare.
   *
   * @return true Se lo user con id $id è un Amministratore o un Questore.
   * @return false Se lo user con id $id non è un Amministratore o un Questore.
   *
   * @return Array JSon per il debug.
   */
  function removeCitizen($id) : Array {
    try{
      $conn = connectDb();
      $query = $conn->prepare("DELETE FROM citizen WHERE id = :id");
      $query->bindParam(":id", $id);
      $query->execute();

      return ["status"=>"success"];
    } catch (PDOException $e){
      error_log("Errore funzione removeCitizen: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  /**
   * Verifica che l'utente sia Questore o Amministratore (vedi in user_role i ruoli).
   *
   * @param $id L'id dello user da testare.
   *
   * @return true Se lo user con id $id è un Amministratore o un Questore.
   * @return false Se lo user con id $id non è un Amministratore o un Questore.
   */
  function isUserChief($id) : bool {
    try{
      $conn = connectDb();
      $query = $conn->prepare("SELECT COUNT(*) AS num FROM user WHERE ((user_role_id = 1) OR (user_role_id = 5)) AND (id = :id)");
      $query->bindParam(":id", $id);
      $query->execute();

      if ($query->fetchAll()[0]["num"] == "1") {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      error_log("Errore funzione isUserChief: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      return false;
    } finally {
      $conn = null;
    }
  }

  /**
   * Crea uno user.
   *
   * @param $name Il nome dello user.
   * @param $surname Il cognome dello user.
   * @param $pin Il pin dello user.
   * @param $role Il ruolo dello user (controlla in user_role i ruoli disponibili prima).
   *
   * @return Array JSon per il debug.
   */
  function createUser($name, $surname, $pin, $role) : Array {
    try{
      $conn = connectDb();
      $query = $conn->prepare("INSERT INTO user (name, surname, pin, user_role_id) VALUES (:name, :surname, :pin, :role)");
      $query->bindParam(":name", $name);
      $query->bindParam(":surname", $surname);
      $query->bindParam(":pin", $pin);
      $query->bindParam(":role", $role);
      $query->execute();

      $query = $conn->query("SELECT last_insert_id()");
      $result = $query->fetchAll();

      return ["status"=>"success", "user_id"=>$result[0]["last_insert_id()"]];
    } catch (PDOException $e){
      error_log("Errore funzione createUser: " . $e->getMessage());
      return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
    } finally {
      $conn = null;
    }
  }

  /**
   * Cancella uno user dato l'id.
   *
   * @param $id L'id dello user da eliminare.
   *
   * @return Array JSon per il debug
   */
  function deleteUser($id) : Array {
    try{
        $conn = connectDb();
        $query = $conn->prepare("DELETE FROM user WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();

        return ["status"=>"success"];
      } catch (PDOException $e){
        error_log("Errore funzione deleteUser: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function getAllUsers() {
      try {
        $conn = connectDb();
        $query = $conn->query("SELECT * FROM user");
        if (empty(($results = $query->fetchAll()))) {
          return ["status"=>"failure", "reason"=>"Nessun utente trovato."];
        } else {
          return ["status"=>"success", "users"=>$results];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione getAllUsers: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      }
    }

    function addJob($job_name) : Array {
      try{
          $conn = connectDb();
          $query = $conn->prepare("INSERT INTO citizen_role (name) VALUES (:job_name)");
          $query->bindParam(":job_name", $job_name);
          $query->execute();

          $query = $conn->prepare("SELECT id FROM citizen_role WHERE name = :name");
          $query->bindParam(":name", $job_name);
          $query->execute();
          if (empty(($id = $query->fetchAll()))) {
            return ["status"=>"failure", "reason"=>"UUID lavoro non trovato."];
          }
          return ["status"=>"success", "job_id"=>$id[0]["id"]];
        } catch (PDOException $e){
          error_log("Errore funzione addJob: " . $e->getMessage());
          return ["status"=>"failure", "reason"=>"Impossibile aggiungere lavoro. Controlla il log per maggiori informazioni."];
        } finally {
          $conn = null;
        }
    }

    function getAllJobs() : Array {
      try {
        $conn = connectDb();
        $query = $conn->query("SELECT * FROM citizen_role");
        if (empty($result = $query->fetchAll())) {
          return ["status"=>"failure", "reason"=>"Nessun lavoro trovato."];
        } else {
          return ["status"=> "success", "jobs"=>$result];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione getAllJobs: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Lettura dal database fallita. Controlla il log per maggiori informazioni."];

      } finally {
        $conn = null;
      }
    }

    function removeJob($job_id) : Array{
      if ($job_id == 10) {
        return ["status"=>"failure", "reason"=>"Per rimuovere questo lavoro contatta un amministratore."];
        //per rimuovere disoccupato modificare citizen.js valore default jobId in localStorage
      }
      try{
          $conn = connectDb();
          $query = $conn->prepare("DELETE FROM citizen_role WHERE id = :job_id");
          $query->bindParam(":job_id", $job_id);
          $query->execute();

          return ["status"=>"success"];
        } catch (PDOException $e){
          error_log("Errore funzione removeJob: " . $e->getMessage());
          return ["status"=>"failure", "reason"=>"Impossibile rimuovere lavoro. Controlla il log per maggiori informazioni."];
        } finally {
          $conn = null;
        }
    }

    function getCitizenInfo($citizen_id) : Array {
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT citizen.*, firearm_license.validity AS gun_license FROM citizen, firearm_license WHERE (citizen.id = :id) AND (firearm_license.citizen_id = :id)");
        $query->bindParam(":id", $citizen_id);
        $query->execute();

        if (empty(($results = $query->fetchAll()))) {
          return ["status"=>"failure", "reason"=>"L'UUID del cittadino non é registrato."];
        } else {
          $returning_values = [$results[0], "job"=>getJobName($results[0]["citizen_role_id"])];
          return ["status"=>"success", "info"=>$returning_values];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione getCitizenInfo: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function getJobName($job_id) {
      try {
        $query = connectDb()->prepare("SELECT name FROM citizen_role WHERE id = :id");
        $query->bindParam(":id", $job_id);
        $query->execute();

        if (empty(($result = $query->fetchAll())) && false) {
          return "Lavoro non trovato. Contattare un amministratore.";
        } else {
          return $result[0]["name"];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione getJobName: " . $e->getMessage());
        return "Query fallita. Controlla il log per maggiori informazioni.";
      } finally {
        $conn = null;
      }
    }

    function searchCitizen($name, $surname) : Array{
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT id FROM citizen WHERE (name LIKE :name) AND (surname LIKE :surname)");
        $query->bindParam(":name", $name);
        $query->bindParam(":surname", $surname);
        $query->execute();

        if(empty($result = $query->fetchAll())){
          $conn = connectDb();
          $query = $conn->prepare("SELECT id FROM citizen WHERE (name LIKE :name) AND (surname LIKE :surname)");
          $query->bindParam(":name", $surname);
          $query->bindParam(":surname", $name);
          $query->execute();

          if (empty($result = $query->fetchAll())) {
            return ["status"=>"failure", "reason"=>"Nessun cittadino ".$name." ".$surname." trovato."];
          } else {
            return ["status"=>"success", "ids"=>$result];
          }
        } else {
          return ["status"=>"success", "ids"=>$result];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione searchCitizen: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function setGunLicense($citizen_id, $validity){
      error_log("Setting gun license to $citizen_id with values $validity");
      try{
        $conn = connectDb();
        $query = $conn->prepare("UPDATE firearm_license SET validity = :val WHERE citizen_id = :id");
        if($validity === "true"){ //validity é una stringa, "false"==true => 1
          $validity = 1;
        }else{
          $validity = 0;
        }
        $query->bindParam(":id", $citizen_id);
        $query->bindParam(":val", $validity);
        $query->execute();

        return ["status"=>"success"];
      } catch (PDOException $e){
        error_log("Errore funzione setGunLicense: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita per la registrazione del porto d'armi. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function countCrimes($id) : Array {
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) FROM crime WHERE citizen_id = :id");
        $query->bindParam(":id", $id);
        $query->execute();

        return ["status"=>"success", "crimes_count"=>$query->fetchAll()];
      } catch (PDOException $e){
        error_log("Errore funzione countCrimes: " . $e->getMessage());
        return (-1);
      } finally {
        $conn = null;
      }
    }

    function getCitizenCrimes($id) : Array {
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT * FROM crime WHERE citizen_id = :id");
        $query->bindParam(":id", $id);
        $query->execute();

        return ["status"=>"success", "crimes"=>$query->fetchAll()];
      } catch (PDOException $e) {
        error_log("Errore funzione getCitizenCrimes: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function updateCitizen($citizen_id, $name, $surname, $sex_id, $birth, $role_id, $status_id) : Array {
      try{
        $conn = connectDb();
        $query = $conn->prepare("UPDATE citizen SET name = :name, surname = :surname, sex_id = :sex, birth = :birth, citizen_role_id = :role, status_id = :status WHERE id = :id");
        $query->bindParam(":name", $name);
        $query->bindParam(":surname", $surname);
        $query->bindParam(":sex", $sex_id);
        $query->bindParam(":birth", $birth);
        $query->bindParam(":role", $role_id);
        $query->bindParam(":status", $status_id);
        $query->bindParam(":id", $citizen_id);
        $query->execute();

        return ["status"=>"success"];
      } catch (PDOException $e) {
        error_log("Errore funzione updateCitizen: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function removeCrime($crime_id) : Array {
      try{
        $conn = connectDb();
        $query = $conn->prepare("DELETE FROM crime WHERE id = :id");
        $query->bindParam(":id", $crime_id);
        $query->execute();

        return ["status"=>"success"];
      } catch (PDOException $e){
        error_log("Errore funzione removeCrime: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

    function addCitizenCrime($citizen_id, $reason, $date) : Array {
      if ($date === null) {
        $date = date("Y-m-d H:i:s");
      }

      try {
        $conn = connectDb();
        $query = $conn->prepare("INSERT INTO crime (citizen_id, reason, date) VALUES (:citizen_id, :reason, :date)");
        $query->bindParam(":citizen_id", $citizen_id);
        $query->bindParam(":reason", $reason);
        $query->bindParam(":date", $date);
        $query->execute();

        $query = $conn->query("SELECT last_insert_id()");
        $result = $query->fetchAll();

        if (empty($result)) {
          return ["status"=>"failure", "reason"=>"L'id crimine ritornato é nullo."];
        } else {
          return ["status"=>"success", "crime_id"=>$result[0]["last_insert_id()"], "date"=>$date];
        }
      } catch (PDOException $e) {
        error_log("Errore funzione addCitizenCrime: " . $e->getMessage());
        return ["status"=>"failure", "reason"=>"Query fallita per l'aggiunta del crimine. Controlla il log per maggiori informazioni."];
      } finally {
        $conn = null;
      }
    }

?>
