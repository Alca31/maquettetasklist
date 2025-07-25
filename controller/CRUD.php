<?php 

//////////Gestion de la connexion en bdd//////////////////

function dbconnex() {

$servername = "127.0.0.1";
$username = "root";
$password = "root";

try {
  $conn = new PDO("mysql:host=$servername;dbname=app-database", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully <br>";
} catch(PDOException $e) {
  echo "Connection failed <br>: " . $e->getMessage();
}


return $conn;
}

function dbdeconnex($conn) {
    $conn = null;
    echo "Connection closed <br>";
    return $conn;
}

//////////////////////////////////gestion de l'utilisateur//////////////////////////////////////////

function checkAll($username, $email) {

   

    try {
    $conn = dbconnex();
    
    // verifié si l'utilisateur existe
    

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username); // lie le paramètre :username à la variable $username
    $stmt->bindParam(':email', $email);
    $stmt->execute(); // exécute la requête préparée
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($stmt->rowCount() > 0) { // alternative: if (count($data) > 0) {

            foreach ($data as $row) {
                $usernameExists = ($row["username"] == $username);
                $emailExists = ($row["email"] == $email);
                
                if ($usernameExists && $emailExists) {
                    echo "le nom d'utilisateur et l'email sont déjà pris";
                } elseif ($usernameExists) {
                    echo "le nom d'utilisateur est déjà pris";
                } elseif ($emailExists) {
                    echo "cet email est déjà pris";
                }
            }
        return false;
    }

    
    } catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
    
    } finally {
    
    dbdeconnex($conn);
    }
    return true;

}

function prepareQueries($username, $email, $password) {
    try {
        $conn = dbconnex();
        
        // Hash le mdp avec bcrypt, qui est plus sécurisé que md5
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) 
        VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "Inscription en bdd réussie. <br>";
            return true;
        } else {
            echo "Erreur lors de l'inscription.";
            return false;
            
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion: " . $e->getMessage();
        return false;
    } finally {
        dbdeconnex($conn);  
    }

}


function register(){

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlentities(trim($_POST['username']));
        $email = htmlentities(trim($_POST['email']));
        $confirm_email = htmlentities(trim($_POST['confirm_email']));
        $password = htmlentities(trim($_POST['password']));
        $confirm_password = htmlentities(trim($_POST['confirm_password']));
         
        if (empty($username) or empty($email) or empty($password) or 
            empty($confirm_password) or empty($confirm_email)) {
            if (empty($username)) {
                echo"le nom d'utilisateur est vide <br>";
            }
            if (empty($email)) {
                echo"l'email est vide <br>";
            }
            if (empty($confirm_email)) {
                echo"il faut confirmé l'émail<br>";
            }
            if (empty($password)) {
                echo"le mdp est vide <br>";
            }
            if (empty($confirm_password)) {
                echo"il faut confirmer le mdp <br>";
            }


            return;
        }

        if ($email !== $confirm_email) {
            echo "Les adresses e-mail ne correspondent pas.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Adresse e-mail invalide.";
            return;
        }

        if ($password !== $confirm_password) {
            echo "Les mots de passe ne correspondent pas.";
            return;
        }


        if (!checkAll($username, $email)) {
            return;
        }

        
        if (prepareQueries($username, $email, $password)) {
            echo "Inscription réussie pour l'utilisateur: $username avec l'email: $email";
            $conn=dbconnex();
            dbdeconnex($conn);
        } else {
            echo "l'inscription s'est mal déroulé";
            $conn=dbconnex();
            dbdeconnex($conn);
        }

        
    }
}



//////////Gestion des tâches////////////////////////////////////

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