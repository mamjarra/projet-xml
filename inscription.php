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
 <link rel="stylesheet" href="css/style.css">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="centered-page">

 

  <?php if ($message): ?>
    <p style="text-align:center; color:<?= strpos($message,'✅') !== false ? 'green' : 'red' ?>; font-weight:bold;"><?= $message ?></p>
  <?php endif; ?>

  <form  class="centered-form"  method="post" action="">
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