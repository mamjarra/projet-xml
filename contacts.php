<?php
session_start();
$xml = simplexml_load_file('plateforme.xml');
$participantActif = $_SESSION['id'] ?? 'p1'; // Fallback pour test si pas connecté

// Ajouter un contact existant
if ($_POST && isset($_POST['contact_id'])) {
    $contactId = $_POST['contact_id'];
    foreach ($xml->participants->participant as $p) {
        if ((string)$p['id'] === $participantActif) {
            // Vérifie si déjà dans les contacts
            $dejaAjoute = false;
            foreach ($p->contacts->contact as $c) {
                if ((string)$c['id'] === $contactId) {
                    $dejaAjoute = true;
                    break;
                }
            }
            if (!$dejaAjoute) {
                $p->contacts->addChild('contact')->addAttribute('id', $contactId);
                $xml->asXML('plateforme.xml');
                echo "✅ Contact ajouté ! <a href='contacts.php'>Retour</a>";
                exit;
            } else {
                echo "⚠️ Ce contact est déjà dans votre liste. <a href='contacts.php'>Retour</a>";
                exit;
            }
        }
    }
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
    <div class="chat-header">Gérer mes contacts</div>
    <div style="padding: 20px;">

      <form method="post">
        <label>Ajouter un contact existant :</label><br>
        <select name="contact_id">
          <?php foreach ($xml->participants->participant as $p): ?>
            <?php if ((string)$p['id'] !== $participantActif): ?>
              <option value="<?php echo $p['id']; ?>"><?php echo $p->nom; ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Ajouter contact</button>
      </form>

      <h3>Ou ajouter un nouveau contact</h3>
      <form method="post" action="ajouter_contact.php">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>
        <label>Statut :</label><br>
        <input type="text" name="statut" value="Disponible"><br><br>
        <button type="submit">Créer et ajouter</button>
      </form>

      <h3>Mes contacts</h3>
      <?php
      foreach ($xml->participants->participant as $me) {
          if ((string)$me['id'] === $participantActif) {
              foreach ($me->contacts->contact as $ref) {
                  $id = (string)$ref['id'];
                  foreach ($xml->participants->participant as $contact) {
                      if ((string)$contact['id'] === $id) {
                          echo "<div class='contact'>
                                  <strong>{$contact->nom}</strong>
                                  <a href='supprimer_contact.php?contact={$id}' onclick=\"return confirm('Supprimer ce contact ?')\" style='color:red;'>❌</a>
                                </div>";
                          break;
                      }
                  }
              }
              break;
          }
      }
      ?>
    </div>
  </div>
</div>
