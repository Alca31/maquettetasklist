<?php
require_once dirname(__DIR__).'/maquettetasklist/controller/CRUD.php';
require_once dirname(__DIR__).'/maquettetasklist/controller/connexUser.php';
deconnex();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
</head>

<body>
    <a href="?deconnexion">deconnexion</a>
    <h1>vous pouvez créer une tâche ici:</h1>
    <form action="task.php" method="post">
        <label for="name">nom de votre tache</label>
        <input type="text" id="name" name="name" required>
        <label for="purpose">Objet de votre tâche</label>
        <textarea name="purpose" id="purpose" required>
           faire la vaisselle...
        </textarea>
        <input type="submit" value="Valider">
    </form>
    <?php
    createTask();
    affTask();
    ?>
</body>

</html>