<?php
session_start();

// Vérifie si utilisateur connecté
if (!isset($_SESSION['id'])) {
    die("Utilisateur non connecté. Veuillez vous connecter.");
}

$participantActif = $_SESSION['id'];

$xml = simplexml_load_file('plateforme.xml');

$parametres = null;
foreach ($xml->participants->participant as $p) {
    if ($p['id'] == $participantActif) {
        if (!isset($p->parametres)) {
            $p->addChild('parametres');
        }
        $parametres = $p->parametres;
        break;
    }
}

if (!$parametres) {
    die("Participant introuvable dans le fichier XML.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parametres->theme = $_POST['theme'];
    $parametres->notifications = $_POST['notifications'];
    $xml->asXML('plateforme.xml');
    echo "✅ Paramètres sauvegardés ! <a href='parametres.php'>Retour</a>";
    exit;
}
?>
<!-- HTML et form restent inchangés -->
<link rel="stylesheet" href="style.css">
<div class="container">
  <div class="sidebar">
    <img src="icone-du-logo-whatsapp-bleu.png" alt="Logo">
    <a href="index.php">💬</a>
    <a href="contacts.php">📒</a>
    <a href="creer_groupe.php">👥</a>
    <a href="profil.php">👤</a>
    <a href="parametres.php">⚙️</a>
  </div>

  <div class="chat-area">
    <div class="chat-header">Mes paramètres</div>
    <div style="padding: 20px;">
      <form method="post">
        <label>Thème :</label><br>
        <select name="theme">
          <option value="clair" <?php if(isset($parametres->theme) && $parametres->theme == 'clair') echo 'selected'; ?>>Clair</option>
          <option value="sombre" <?php if(isset($parametres->theme) && $parametres->theme == 'sombre') echo 'selected'; ?>>Sombre</option>
        </select><br><br>
        <label>Notifications :</label><br>
        <select name="notifications">
          <option value="on" <?php if(isset($parametres->notifications) && $parametres->notifications == 'on') echo 'selected'; ?>>Activées</option>
          <option value="off" <?php if(isset($parametres->notifications) && $parametres->notifications == 'off') echo 'selected'; ?>>Désactivées</option>
        </select><br><br>
        <button type="submit">Sauvegarder</button>
      </form>
    </div>
  </div>
</div>
