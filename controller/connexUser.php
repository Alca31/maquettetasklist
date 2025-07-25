<?php
require_once dirname(__DIR__).'/controller/CRUD.php'; //connexion à la base de donnée

function userSession($id, $username){
session_start();
$_SESSION["id"]=$id;
$_SESSION["username"]=$username;

}

function deconnex() {

    if (session_status() === PHP_SESSION_NONE or session_id()=="") {
        session_start();
    }

    if (isset($_GET['deconnexion'])) {
 
    session_unset();//remet les variables de la session à 0
    session_destroy();//ferme la session côté serveur
    setcookie("PHPSESSID", "");
    //demande au navigateur de détruire la session côté client
    header("Location: /index.php");// redir vers l'index
    exit(); //s'assure que la redir est bien executé
    }
    

}

function connex() {
     
    

     if ($_SERVER["REQUEST_METHOD"] == "POST") {

         $username = htmlentities(trim($_POST['username']));
         $password = htmlentities(trim($_POST['password']));
            if (empty($username) || empty($password)) {
                echo "Veuillez remplir tous les champs.";
                return;
            }
            try {
               $conn=dbconnex();
               $stmt = $conn->prepare("SELECT id, username, password FROM users 
               WHERE username = :username");
               $stmt->bindParam(':username', $username);
               $stmt->execute();
               $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
               
               if ($stmt->rowCount() > 0) {
                
                foreach ($data as $row) {
                    $id=$row["id"];
                    $pass=$row["password"];
                    $user=$row["username"];
              
                 }
                  $checkpsswrd=password_verify($password, $pass);
               } else {
                echo "vous nêtes pas inscrit <br>";
            }

              

               if ($user==$username and ($checkpsswrd or $password == $pass)) {
               userSession($id, $username);
               header("Location: /task.php");
               exit();
               }
               else{

                if ($username != $user) {
                    echo "vous vous êtes trompé de nom d'utilisateur ou il n'existe pas en base de donnée. <br>";
                }

                if (!$checkpsswrd or $password != $data['password']) {
                    echo "le mot de passe ne correspond pas <br>";
                }
                 dbdeconnex($conn);
                 return;
                }

            } 
            catch (PDOException $e) {
              echo "Erreur de connexion: " . $e->getMessage();
    
            }  
            finally {
    
               dbdeconnex($conn);
            }
    }

    

}

?>