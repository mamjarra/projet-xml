<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

$xmlFile = 'plateforme.xml';
if (!file_exists($xmlFile)) {
    die("Fichier de données introuvable.");
}
$xml = simplexml_load_file($xmlFile);

$groupeId = $_GET['id'] ?? null;
if (!$groupeId) {
    die("Groupe non spécifié.");
}

// Trouver le groupe
$groupe = null;
foreach ($xml->groupes->groupe as $g) {
    if ((string)$g['id'] === $groupeId) {
        $groupe = $g;
        break;
    }
}

if (!$groupe) {
    die("Groupe introuvable.");
}

// Traitement envoi message
$messageError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['message'] ?? '');
    if ($contenu === '') {
        $messageError = "Le message ne peut pas être vide.";
    } else {
        // Ajouter message dans XML
        if (!isset($groupe->messages)) {
            $groupe->addChild('messages');
        }
        $messages = $groupe->messages;
        $newMsg = $messages->addChild('message');
        $newMsg->addAttribute('id', 'm' . (count($messages->message) + 1));
        $newMsg->addChild('emetteur', $_SESSION['id']);
        $newMsg->addChild('texte', htmlspecialchars($contenu));
        $newMsg->addChild('date', date('Y-m-d H:i:s'));

        $xml->asXML($xmlFile);
        header("Location: discussion_groupe.php?id=$groupeId");
        exit();
    }
}

// Fonction pour récupérer le nom d’un participant
function getParticipantNom($xml, $id) {
    foreach ($xml->participants->participant as $p) {
        if ((string)$p['id'] === (string)$id) {
            return (string)$p->nom;
        }
    }
    return "Inconnu";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Discussion groupe - <?= htmlspecialchars($groupe->nom) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
  <div class="sidebar">
    <img src="icone-du-logo-whatsapp-bleu.png" alt="Logo" style="width:45px; margin-bottom:10px; border-radius:50%;">
    <a href="index.php">💬</a>
    <a href="contacts.php">📒</a>
    <a href="creer_groupe.php">👥</a>
    <a href="profil.php">👤</a>
    <a href="parametres.php">⚙️</a>
  </div>

  <div class="chat-area">
    <div class="chat-header">
      Discussion du groupe : <?= htmlspecialchars($groupe->nom) ?>
    </div>

    <div class="messages" style="height:400px; overflow-y:auto; padding:15px; background:#f9f9f9; border:1px solid #ccc; border-radius:8px;">
      <?php if (isset($groupe->messages->message) && count($groupe->messages->message) > 0): ?>
        <?php foreach ($groupe->messages->message as $msg): ?>
          <?php
            $emetteurNom = getParticipantNom($xml, (string)$msg->emetteur);
            $texte = htmlspecialchars((string)$msg->texte);
            $date = (string)$msg->date;
            $isMe = ((string)$msg->emetteur === $_SESSION['id']);
          ?>
          <div style="margin-bottom: 12px; max-width: 70%; padding: 10px; border-radius: 15px; <?= $isMe ? 'background:#dbeafe; margin-left:auto;' : 'background:#fff; margin-right:auto;' ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <strong><?= $emetteurNom ?></strong> <small style="font-size: 0.8em; color: gray;"><?= $date ?></small>
            <p style="margin: 5px 0 0 0; white-space: pre-wrap;"><?= $texte ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun message dans ce groupe.</p>
      <?php endif; ?>
    </div>

    <form method="post" style="margin-top: 15px;">
      <?php if ($messageError): ?>
        <p style="color: red; font-weight: bold;"><?= $messageError ?></p>
      <?php endif; ?>
      <textarea name="message" rows="3" style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;" placeholder="Écrire un message..." required></textarea>
      <button type="submit" style="margin-top:10px; padding: 8px 15px; border:none; background:#3b82f6; color:#fff; border-radius:20px; cursor:pointer;">Envoyer</button>
    </form>

  </div>
</div>

</body>
</html>
