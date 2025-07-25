<?php
require_once dirname(__DIR__).'/maquettetasklist/controller/CRUD.php';
require_once dirname(__DIR__).'/maquettetasklist/controller/connexUser.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
</head>
<body>
    <h1>connectez vous</h1>
    <form action="index.php" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore inscrit ? <a href="register.php">Inscrivez-vous
        <?php connex();// s'execute quand l'utilisateur clique sur le bouton ?>
</body>
</html>