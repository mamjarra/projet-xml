<?php
$xml = simplexml_load_file('plateforme.xml');
$participantActif = 'p1';

foreach ($xml->participants->participant as $p) {
    if ($p['id'] == $participantActif) {
        $participant = $p;
        break;
    }
}

if ($_POST && $participant) {
    $participant->nom = $_POST['nom'];
    $participant->email = $_POST['email'];
    $participant->statut = $_POST['statut'];
    $xml->asXML('plateforme.xml');
    echo "✅ Profil mis à jour ! <a href='profil.php'>Retour</a>";
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
    <div class="chat-header">Mon Profil</div>
    <div style="padding: 20px;">
      <form method="post">
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?php echo $participant->nom; ?>"><br><br>
        <label>Email :</label><br>
        <input type="text" name="email" value="<?php echo $participant->email; ?>"><br><br>
        <label>Statut :</label><br>
        <input type="text" name="statut" value="<?php echo $participant->statut; ?>"><br><br>
        <button type="submit">Enregistrer</button>
      </form>
    </div>
  </div>
</div>
