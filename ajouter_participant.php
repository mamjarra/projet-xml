<?php
if ($_POST) {
    $xml = simplexml_load_file('plateforme.xml');

    $id = 'p' . (count($xml->participants->participant) + 1);
    $newParticipant = $xml->participants->addChild('participant');
    $newParticipant->addAttribute('id', $id);
    $newParticipant->addChild('nom', $_POST['nom']);
    $newParticipant->addChild('email', $_POST['email']);
    $newParticipant->addChild('statut', $_POST['statut']);
    $newParticipant->addChild('contacts');
    $newParticipant->addChild('groupes');

    $xml->asXML('plateforme.xml');
    echo "✅ Participant ajouté ! <a href='index.php'>Retour</a>";
    exit;
}
?>
<h2>Ajouter un participant</h2>
<form method="post">
  Nom: <input type="text" name="nom" required><br>
  Email: <input type="text" name="email" required><br>
  Statut: <input type="text" name="statut" value="Disponible"><br>
  <input type="submit" value="Ajouter">
</form>
