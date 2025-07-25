<?php require dirname(__DIR__).'/maquettetasklist/controller/registUser.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <form action="register.php" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">mail:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="confirm_email">confimez l'email:</label>
        <input type="email" id="confirm_email" name="confirm_email" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">Confirmer le mot de passe:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="index.php">Connectez-vous</a></p>
    <?php register();//s'execute quand l'utilisateur clique sur le bouton ?>
</body>
</html>
