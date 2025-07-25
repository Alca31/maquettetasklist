<?php

require_once dirname(__DIR__).'/controller/dbconnex.php';
require_once dirname(__DIR__).'/controller/connexUser.php';

deconnex();

function createTask(){
    if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["name"]) && isset($_POST["purpose"])) {

        $taskname=htmlentities(trim($_POST["name"]));
        $taskpurpose=htmlentities(trim($_POST["purpose"]));
        $id_userSession=$_SESSION['id'];
        if (empty($taskname) || empty($taskpurpose)) {
             echo "Veuillez remplir tous les champs.";
                return;
        }
        try {
            $conn=dbconnex();
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, taskname, taskpurpose) 
            VALUES(:user_id, :taskname, :taskpurpose)");
            if ($stmt->execute([
                ':user_id' => $id_userSession,
                ':taskname' => $taskname,
                ':taskpurpose' => $taskpurpose
            ])) {
                echo"votre tâche s'est bien crée";
            } else {
                echo"votre tâche n'a pas pû être crée à cause d'une erreur";
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
        }
         finally {

               dbdeconnex($conn);
        }
    }
    return;
}

function validTask(){
    if (isset($_POST["valid"]) and isset($_POST["row"])) {
        $idTask = (int)$_POST["row"];
        try {
            $conn=dbconnex();
            $stmt = $conn->prepare("UPDATE tasks SET status='completed' WHERE id=:id");
            $stmt->bindParam(':id', $idTask);
            if ($stmt->execute()) {
                echo "Tâche validée avec succès.";
            } else {
                echo "Échec de la validation de la tâche.";
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
        } finally {
            dbdeconnex($conn);
        }
    }
}

function delTask(){
    if (isset($_POST["del"]) and isset($_POST["row"])) {
        $idTask = (int)$_POST["row"];
        try {
            $conn=dbconnex();
            $stmt = $conn->prepare("DELETE FROM tasks WHERE id=:id");
            $stmt->bindParam(':id', $idTask);
            if ($stmt->execute()) {
                echo "Tâche supprimée avec succès.";
            } else {
                echo "Échec de la suppression de la tâche.";
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
        } finally {
            dbdeconnex($conn);
        }
    }
}


function affTask(){
   try {     
              
            $id_userSession=$_SESSION['id'];
            $conn=dbconnex();
            $stmt = $conn->prepare("SELECT id, taskname, taskpurpose, status FROM tasks WHERE user_id=:user_id");
            $stmt->bindParam(':user_id', $id_userSession);
            $stmt->execute();
            $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $row){
                
                 echo "nom de la tâche: ".$row["taskname"]."<br>"; 
                 /*il faut normalement décoder les données avec htmlentitiesdecode(); 
                 mais se fait automatiquement sur mozilla apparement*/
                 echo "objet de la tâche: ".$row["taskpurpose"]."<br>";
                 echo "statut de la tache: ".$row["status"]."<br>";
                 echo '<form action="task.php" method=POST>
                 <input type=hidden name="row" value="'.$row["id"].'">
                 <input type=submit id="valid" name="valid" value="validez la tâche">
                 </form>';
                 validTask();
                 echo '<form action="task.php" method=POST> 
                 <input type=hidden name="row" value="'.$row["id"].'">
                 <input type=submit id="del" name="del" value="supprimer la tâche">
                 </form>';
                 delTask(); 
            }
            
           
            
   } catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
   }
   finally{
      dbdeconnex($conn);
   }

}

?>