<?php
$xml = simplexml_load_file('plateforme.xml');
$participantActif = 'p1';

foreach ($xml->participants->participant as $p) {
    if ($p['id'] == $participantActif) {
        if (!isset($p->parametres)) {
            $p->addChild('parametres');
        }
        $parametres = $p->parametres;
        break;
    }
}

if ($_POST) {
    $parametres->theme = $_POST['theme'];
    $parametres->notifications = $_POST['notifications'];
    $xml->asXML('plateforme.xml');
    echo "✅ Paramètres sauvegardés ! <a href='parametres.php'>Retour</a>";
    exit;
}
?>
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
          <option value="clair">Clair</option>
          <option value="sombre">Sombre</option>
        </select><br><br>
        <label>Notifications :</label><br>
        <select name="notifications">
          <option value="on">Activées</option>
          <option value="off">Désactivées</option>
        </select><br><br>
        <button type="submit">Sauvegarder</button>
      </form>
    </div>
  </div>
</div>
