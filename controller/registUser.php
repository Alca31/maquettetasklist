<?php

require_once dirname(__DIR__).'/controller/dbconnex.php';

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


?>