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

  <!-- Style CSS en interne pour test -->
  <style>
    body {
      background-color: #f4f6f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    form.centered-form {
      background: white;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 320px;
      box-sizing: border-box;
    }
    form.centered-form h1 {
      margin-bottom: 25px;
      font-weight: 700;
      text-align: center;
      color: #1e40af;
    }
    form.centered-form div {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
    }
    form.centered-form label {
      margin-bottom: 6px;
      font-weight: 600;
      color: #334155;
    }
    form.centered-form input[type="text"],
    form.centered-form input[type="password"] {
      padding: 10px 14px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s;
    }
    form.centered-form input[type="text"]:focus,
    form.centered-form input[type="password"]:focus {
      border-color: #3b82f6;
      outline: none;
    }
    form.centered-form button {
      width: 100%;
      padding: 12px;
      background-color: #3b82f6;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    form.centered-form button:hover {
      background-color: #1e40af;
    }
    p.form-link {
      margin-top: 15px;
      font-size: 14px;
      text-align: center;
      color: #64748b;
    }
    p.form-link a {
      color: #3b82f6;
      text-decoration: none;
      font-weight: 600;
    }
    p.form-link a:hover {
      text-decoration: underline;
    }
    p.error {
      background-color: #fee2e2;
      color: #b91c1c;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      text-align: center;
      font-weight: 600;
    }
  </style>

</head>
<body class="centered-page">

  <?php if ($message): ?>
    <p class="error"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form class="centered-form" method="post">
    <h1>Connexion</h1>
    <div>
      <label for="nom">Nom d'utilisateur</label>
      <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
    </div>
    <div>
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
    </div>
    <button type="submit">Se connecter</button>
    <p class="form-link">Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
  </form>
</body>
</html>
