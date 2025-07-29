<?php 
$xml = simplexml_load_file('plateforme.xml');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = 'g' . (count($xml->groupes->groupe) + 1);
    $nom = $_POST['nom'];

    $newGroupe = $xml->groupes->addChild('groupe');
    $newGroupe->addAttribute('id', $newId);
    $newGroupe->addChild('nom', $nom);
    $membres = $newGroupe->addChild('membres');

    if (isset($_POST['participants'])) {
        foreach ($_POST['participants'] as $participantId) {
            $membres->addChild('participant')->addAttribute('id', $participantId);
        }
    }

    foreach ($xml->participants->participant as $p) {
        if (isset($_POST['participants']) && in_array($p['id'], $_POST['participants'])) {
            $p->groupes->addChild('groupe')->addAttribute('id', $newId);
        }
    }

    $xml->asXML('plateforme.xml');
    echo "✅ Groupe créé ! <a href='index.php'>Retour</a>";
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
    <div class="chat-header">Créer un groupe</div>
    <div style="padding: 20px;">
      <form method="post">
        <label>Nom du groupe :</label><br>
        <input type="text" name="nom" required><br><br>
        <label>Choisir les participants :</label><br>
        <?php foreach ($xml->participants->participant as $p): ?>
          <input type="checkbox" name="participants[]" value="<?php echo $p['id']; ?>">
          <?php echo $p->nom; ?><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Créer le groupe</button>
      </form>

        <h3 style="margin-top: 40px;">Groupes existants :</h3>
  <div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php foreach ($xml->groupes->groupe as $groupe): ?>
      <div style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; width: 250px; background-color: #f9f9f9;">
        <h4 style="margin-top: 0;"><?php echo $groupe->nom; ?> <small style="color: gray;">(<?php echo $groupe['id']; ?>)</small></h4>
        <p style="margin: 0 0 10px 0; font-weight: bold;">Membres :</p>
        <ul style="padding-left: 20px; margin: 0;">
          <?php foreach ($groupe->membres->participant as $membre): ?>
            <?php
              $nomMembre = '';
              foreach ($xml->participants->participant as $p) {
                if ((string)$p['id'] === (string)$membre['id']) {
                  $nomMembre = $p->nom;
                  break;
                }
              }
            ?>
            <li><?php echo $nomMembre; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>

    </div>
  </div>
</div>
