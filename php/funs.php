<?php
  require_once 'core.php';
  //getUserUUID(pin) -> ritorna l'id utente
  //getAllCitizens(user_uuid da cookie) API ritorna in json nome, cognome, status (ritorna queste cose per ogni cittandino, utilizzabile da ogni user in db Ogni poliziotto non cittafino)
  //getCitizenInfo(user_uuid da cookie) API ritorna stato porto d'armi (valido o non valido) e fedina penale (ogni crimine, con id, name e data) (ritorna queste cose per ogni cittandino, utilizzabile da ogni user in db Ogni poilizzioto non cittadino)
  //leggere cookie: session_start() all'inizio poi $_SESSION["user_uuid"];

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
      die(json_encode(["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()]));
    } finally {
      $conn = null;
    }
  }

  function getUserInfo($user_UUID) : Array {
    try {
      $query = connectDb()->prepare("SELECT user.name, user.surname, user_role.name AS role FROM user, user_role WHERE (user.user_role_id = user_role.id) AND (user.id = :id)");
      $query->bindParam(":id", $user_UUID);
      $query->execute();
      if (!empty(($result = $query->fetchAll()[0]))) {
        return $result;
      } else {
        return ["status"=>"General failure"];
      }
    } catch (PDOException $e) {
      return ["status"=>"General failure"];
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
      die(json_encode(["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()]));
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
      return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
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
      return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
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

      return ["status"=>"success"];
    } catch (PDOException $e){
      return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
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

      return ["status"=>"Success"];
    } catch (PDOException $e){
      return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
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
      error_log("Query failed for isUserChief, retuning false. DEBUG" . $e->getMessage());
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

      return ["status"=>"Success"];
    } catch (PDOException $e){
      return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
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

        return ["status"=>"Success"];
      } catch (PDOException $e){
        return ["status"=>"failure", "reason"=>"Query fallita. DEBUG: " . $e->getMessage()];
      } finally {
        $conn = null;
      }
    }

?>
