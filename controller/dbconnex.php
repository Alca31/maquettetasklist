<?php  

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

?>