<?php
session_start();

$xml = simplexml_load_file('plateforme.xml');

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit;
}

$participantId = $_SESSION['id'];
$participant = null;

// Recherche du participant connecté dans le XML
foreach ($xml->participants->participant as $p) {
    if ((string)$p['id'] === $participantId) {
        $participant = $p;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil utilisateur</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <img src="icone-du-logo-whatsapp-bleu.png" alt="Logo">
    <a href="index.php">💬</a>
    <a href="contacts.php">📒</a>
    <a href="creer_groupe.php">👥</a>
    <a href="profil.php">👤</a>
    <a href="parametres.php">⚙️</a>
  </div>

  <!-- Zone Profil -->
  <div class="chat-area">
    <div class="chat-header">Mon Profil</div>

    <?php if ($participant): ?>
      <form method="post" action="modifier_profil.php" style="padding:20px;">
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?php echo $participant->nom; ?>" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" value="<?php echo $participant->email; ?>"><br><br>

        <label>Statut :</label><br>
        <input type="text" name="statut" value="<?php echo $participant->statut; ?>"><br><br>

        <button type="submit">Enregistrer</button>
      </form>
    <?php else: ?>
      <p style="color:red; padding:20px;">Erreur : utilisateur introuvable.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
