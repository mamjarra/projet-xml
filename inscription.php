<?php
// Chargement du fichier XML
$xmlFile = 'plateforme.xml';
if (!file_exists($xmlFile)) {
    // Création d'un fichier XML vide avec structure minimale si inexistant
    $xml = new SimpleXMLElement('<plateforme><participants></participants></plateforme>');
    $xml->asXML($xmlFile);
} else {
    $xml = simplexml_load_file($xmlFile);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($nom && $telephone && $password) {
        // Vérifier que le téléphone ou email n'existe pas déjà
        $existe = false;
        foreach ($xml->participants->participant as $participant) {
            if ((string)$participant->telephone == $telephone || (string)$participant->email == $email) {
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            // Créer un nouvel ID participant simple (ex: p + numéro)
            $dernierIdNum = 0;
            foreach ($xml->participants->participant as $participant) {
                $idNum = (int)substr((string)$participant['id'], 1);
                if ($idNum > $dernierIdNum) $dernierIdNum = $idNum;
            }
            $nouveauId = 'p' . ($dernierIdNum + 1);

            $nouveauParticipant = $xml->participants->addChild('participant');
            $nouveauParticipant->addAttribute('id', $nouveauId);
            $nouveauParticipant->addChild('nom', htmlspecialchars($nom));
            $nouveauParticipant->addChild('telephone', htmlspecialchars($telephone));
            $nouveauParticipant->addChild('email', htmlspecialchars($email));
            $nouveauParticipant->addChild('statut', 'Disponible');
            $nouveauParticipant->addChild('password', password_hash($password, PASSWORD_DEFAULT)); // hash password
            $nouveauParticipant->addChild('contacts');
            $nouveauParticipant->addChild('groupes');

            $xml->asXML($xmlFile);
            $message = "✅ Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            $message = "⚠️ Téléphone ou email déjà utilisé.";
        }
    } else {
        $message = "⚠️ Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Inscription</title>

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
  form.centered-form input[type="tel"],
  form.centered-form input[type="email"],
  form.centered-form input[type="password"] {
    padding: 10px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
  }
  form.centered-form input[type="text"]:focus,
  form.centered-form input[type="tel"]:focus,
  form.centered-form input[type="email"]:focus,
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
  p.message {
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
  }
  p.message.success {
    color: green;
  }
  p.message.error {
    color: red;
  }
</style>

</head>
<body class="centered-page">

  <?php if ($message): ?>
    <p class="message <?= strpos($message,'✅') !== false ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </p>
  <?php endif; ?>

  <form class="centered-form" method="post" action="">
    <h1>Inscription</h1>
    <div>
        <label for="nom">Nom complet *</label>
        <input type="text" id="nom" name="nom" required>
    </div>
    <div>
        <label for="tel">Numéro de téléphone *</label>
        <input type="tel" id="tel" name="telephone" placeholder="+221771234567" pattern="^\+?\d{7,15}$" required>
    </div>
    <div>
        <label for="email">Email (optionnel)</label>
        <input type="email" id="email" name="email">
    </div>
    <div>
         <label for="password">Mot de passe *</label>
         <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">S'inscrire</button>
    <p class="form-link">Déjà inscrit ? <a href="connexion.php">Connectez-vous ici</a></p>
  </form>

</body>
</html>
