<?php
session_start();

$xmlFile = 'plateforme.xml';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $password = trim($_POST['password']);

    if ($nom && $password) {
        if (file_exists($xmlFile)) {
            $xml = simplexml_load_file($xmlFile);
            $trouve = false;

            foreach ($xml->participants->participant as $participant) {
                if ((string)$participant->nom === $nom) {
                    if (isset($participant->password) && password_verify($password, (string)$participant->password)) {
                        $_SESSION['id'] = (string)$participant['id'];
                        $_SESSION['nom'] = (string)$participant->nom;
                        #$_SESSION['participant_nom'] = (string)$participant->nom;
                        $trouve = true;
                        header('Location: index.php');
                        exit;
                    }
                }
            }

            if (!$trouve) {
                $message = "Nom ou mot de passe incorrect.";
            }
        } else {
            $message = "Aucun utilisateur enregistré.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="form-container">
    <h2>Connexion</h2>

    <?php if ($message): ?>
      <p class="error"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
      <label for="nom">Nom d'utilisateur</label>
      <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>

      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

      <button type="submit">Se connecter</button>
    </form>

    <p class="link">Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
  </div>
</body>
</html>
