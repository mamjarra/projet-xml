<?php
session_start();

$xml = simplexml_load_file('plateforme.xml');

// Redirection si non connecté
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit;
}

$participantActif = $_SESSION['id'];
$destinataire = isset($_GET['dest']) ? $_GET['dest'] : null;

$messages = [];
if ($destinataire) {
    foreach ($xml->messages->message as $msg) {
        $exp = (string)$msg->expediteur['id'];
        $dest = (string)$msg->destinataire['id'];
        if (($exp == $participantActif && $dest == $destinataire) || ($exp == $destinataire && $dest == $participantActif)) {
            $messages[] = $msg;
        }
    }
}

$contactChoisi = null;
foreach ($xml->participants->participant as $p) {
    if ($p['id'] == $destinataire) {
        $contactChoisi = $p;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Messagerie XML</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container">

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="icone-du-logo-whatsapp-bleu.png" alt="Logo" />
    <a href="index.php">💬</a>
    <a href="contacts.php">📒</a>
    <a href="creer_groupe.php">👥</a>
    <a href="profil.php">👤</a>
    <a href="parametres.php">⚙️</a>
    <a href="deconnexion.php" onclick="return confirm('Voulez-vous vous déconnecter ?')">🚪</a>
  </div>

  
      <!-- Conversations actives -->
  <div class="contacts-list">
    <?php
    $contactsAffiches = [];

    foreach ($xml->messages->message as $msg) {
        $exp = (string)$msg->expediteur['id'];
        $dest = (string)$msg->destinataire['id'];

        if ($exp === $participantActif) {
            $idContact = $dest;
        } elseif ($dest === $participantActif) {
            $idContact = $exp;
        } else {
            continue;
        }

        if (in_array($idContact, $contactsAffiches)) continue;
        $contactsAffiches[] = $idContact;

        foreach ($xml->participants->participant as $c) {
            if ((string)$c['id'] === $idContact) {
                ?>
                <a href="?dest=<?php echo $c['id']; ?>" style="text-decoration:none; color:black;">
                  <div class="contact">
                    <img src="profile.jfif" alt="Profil" />
                    <strong><?php echo $c->nom; ?> <span class="flag">🇸🇳</span></strong>
                  </div>
                </a>
                <?php
                break;
            }
        }
    }
    ?>
  </div>

<!-- Section Nouveau message -->
<div class="contacts-list" style="border-top: 1px solid #ccc; margin-top: 20px; padding: 10px;">
  <button onclick="toggleNouveauxContacts()" style="background: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer;">
    ➕ Nouveau message
  </button>

  <div id="nouveauxContacts" style="display: none; margin-top: 10px;">
    <?php
    // Trouver tous les contacts sans conversation
    $contactsAvecMessages = $contactsAffiches; // déjà rempli au-dessus
    foreach ($xml->participants->participant as $contact) {
        if ($contact['id'] != $participantActif && !in_array((string)$contact['id'], $contactsAvecMessages)) {
            ?>
            <a href="?dest=<?php echo $contact['id']; ?>" style="text-decoration:none; color:black;">
              <div class="contact">
                <img src="profile.jfif" alt="Profil" />
                <strong><?php echo $contact->nom; ?> <span class="flag">🇸🇳</span></strong>
              </div>
            </a>
            <?php
        }
    }
    ?>
  </div>
</div>


  <!-- Zone de chat -->
  <div class="chat-area">
    <div class="chat-header">
      <?php echo $contactChoisi ? $contactChoisi->nom : "Choisir un contact"; ?>
    </div>

    <div class="messages">
      <?php foreach ($messages as $index => $msg): ?>
        <?php 
          $classe = ($msg->expediteur['id'] == $participantActif) ? 'sent' : 'received'; 
          $checkmark = ($classe === 'sent') ? '<span class="status"></span>' : '';
        ?>
        <div class="message <?php echo $classe; ?>" id="msg-<?php echo $index; ?>" onclick="selectMessage(<?php echo $index; ?>)">
          <div class="message-body">
            <?php echo $msg->contenu . $checkmark; ?>
            <?php if (isset($msg->fichier)): ?>
              <br><a href="<?php echo $msg->fichier; ?>" target="_blank">📎 Fichier joint</a>
            <?php endif; ?>
            <small style="display:block; font-size:11px; color:#64748b;">Delivered to <?php echo $destinataire; ?>@mail.com</small>
          </div>

          <div class="actions-bar-inline" id="actions-<?php echo $index; ?>" style="display:none;">
            <button onclick="replyMessage('<?php echo $msg['id']; ?>', '<?php echo addslashes($msg->contenu); ?>', <?php echo $index; ?>)">↩️</button>
            <button onclick="editMessage('<?php echo $msg['id']; ?>', '<?php echo addslashes($msg->contenu); ?>', '<?php echo $destinataire; ?>')">✏️</button>
            <a href="supprimer_message.php?id=<?php echo $msg['id']; ?>&dest=<?php echo $destinataire; ?>" onclick="return confirm('Supprimer ce message ?')">🗑️</a>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Zone de réponse -->
    <div id="reply-context" class="reply-context" style="display:none;">
      <span>↩️ <strong id="reply-snippet"></strong></span>
      <button onclick="cancelReply()">❌</button>
      <input type="hidden" name="reply_to" id="reply_to">
    </div>

    <!-- Saisie -->
    <form class="input-area" action="envoyer_message.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="expediteur" value="<?php echo $participantActif; ?>">
      <input type="hidden" name="destinataire" value="<?php echo $destinataire; ?>">
      <input type="hidden" name="reply_to" id="reply_to_field" value="">
      <input type="text" name="contenu" placeholder="Tapez votre message..." required>
      <input type="file" name="fichier" id="file-upload">
      <label for="file-upload">📎</label>
      <span id="file-name" style="margin-left: 10px; font-style: italic; color: #555;"></span>

      <button type="submit">Envoyer</button>
    </form>
  </div>

  <!-- Panneau profil à droite avec bouton flottant -->
  <div class="right-panel" id="rightPanel">
    <button onclick="fermerPanel()" style="float:right; background:none; border:none; font-size:18px; cursor:pointer;">❌</button>
    <?php if ($contactChoisi): ?>
      <h3><?php echo $contactChoisi->nom; ?></h3>
      <p><strong>Email:</strong> <?php echo $contactChoisi->email; ?></p>
      <p><strong>Statut:</strong> <?php echo $contactChoisi->statut; ?></p>
      <hr>
      <p><strong>Groupes:</strong></p>
      <?php foreach ($contactChoisi->groupes->groupe as $g): ?>
        <p>- <?php echo $g['id']; ?></p>
      <?php endforeach; ?>
      <hr>
      <button class="block-button">🚫 Bloquer cet utilisateur</button>
    <?php else: ?>
      <p>Sélectionnez un contact pour voir le profil</p>
    <?php endif; ?>
  </div>

</div>

<!-- Bouton flottant pour rouvrir -->
<button id="ouvrirBtn" onclick="ouvrirPanel()" style="position:fixed; bottom:60px; right:20px; background-color:#3b82f6; color:white; border:none; border-radius:50%; width:45px; height:45px; font-size:20px; cursor:pointer;">
  ℹ️
</button>

<script>
let currentMsg = null;

function selectMessage(index) {
  if (currentMsg !== null) {
    document.getElementById('msg-' + currentMsg).classList.remove('highlighted');
    document.getElementById('actions-' + currentMsg).style.display = 'none';
  }

  document.getElementById('msg-' + index).classList.add('highlighted');
  document.getElementById('actions-' + index).style.display = 'block';
  currentMsg = index;
}

function replyMessage(id, text, index) {
  document.getElementById("reply-context").style.display = "block";
  document.getElementById("reply-snippet").innerText = text.slice(0, 40) + '...';
  document.getElementById("reply_to").value = id;
  document.getElementById("reply_to_field").value = id;
  selectMessage(index);
}

function cancelReply() {
  document.getElementById("reply-context").style.display = "none";
  document.getElementById("reply-snippet").innerText = "";
  document.getElementById("reply_to").value = "";
  document.getElementById("reply_to_field").value = "";
  if (currentMsg !== null) {
    document.getElementById('msg-' + currentMsg).classList.remove('highlighted');
    document.getElementById('actions-' + currentMsg).style.display = 'none';
    currentMsg = null;
  }
}

function editMessage(id, content, dest) {
  const newText = prompt("Modifier le message :", content);
  if (newText !== null) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'modifier_message.php';

    const idField = document.createElement('input');
    idField.type = 'hidden';
    idField.name = 'id';
    idField.value = id;

    const contenuField = document.createElement('input');
    contenuField.type = 'hidden';
    contenuField.name = 'contenu';
    contenuField.value = newText;

    const destField = document.createElement('input');
    destField.type = 'hidden';
    destField.name = 'destinataire';
    destField.value = dest;  // il faut passer la valeur ici

    form.appendChild(idField);
    form.appendChild(contenuField);
    form.appendChild(destField);
    document.body.appendChild(form);
    form.submit();
  }
}


function fermerPanel() {
  document.getElementById("rightPanel").style.display = "none";
  document.getElementById("ouvrirBtn").style.display = "block";
}
function ouvrirPanel() {
  document.getElementById("rightPanel").style.display = "block";
  document.getElementById("ouvrirBtn").style.display = "none";
}


function toggleNouveauxContacts() {
  var section = document.getElementById('nouveauxContacts');
  section.style.display = (section.style.display === 'none') ? 'block' : 'none';
}


  const fileInput = document.getElementById('file-upload');
  const fileNameSpan = document.getElementById('file-name');

  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      fileNameSpan.textContent = fileInput.files[0].name;
    } else {
      fileNameSpan.textContent = '';
    }
  });


</script>

</body>
</html>
