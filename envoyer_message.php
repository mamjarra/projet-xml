<?php
$xml = simplexml_load_file('plateforme.xml');

$expediteur = $_POST['expediteur'];
$destinataire = $_POST['destinataire'];
$contenu = $_POST['contenu'];
$replyTo = isset($_POST['reply_to']) ? $_POST['reply_to'] : null;
$editId = isset($_POST['edit_id']) ? $_POST['edit_id'] : null;

if ($editId) {
    // Modifier message existant
    foreach ($xml->messages->message as $msg) {
        if ((string)$msg['id'] === $editId && (string)$msg->expediteur['id'] === $expediteur) {
            $msg->contenu = $contenu;
            $msg->date = date('c');
            break;
        }
    }
} else {
    // Ajouter un nouveau message
    $newId = 'm' . (count($xml->messages->message) + 1);
    $message = $xml->messages->addChild('message');
    $message->addAttribute('id', $newId);
    $message->addChild('expediteur')->addAttribute('id', $expediteur);
    $message->addChild('destinataire')->addAttribute('id', $destinataire);

    if ($replyTo) {
        $message->addChild('reponse')->addAttribute('id', $replyTo);
    }

    $message->addChild('contenu', htmlspecialchars($contenu));
    $message->addChild('date', date('c'));

    // Gestion fichier
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['fichier']['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['fichier']['tmp_name'], $filePath);
        $message->addChild('fichier', $filePath);
    }
}

$xml->asXML('plateforme.xml');

header('Location: index.php?dest=' . $destinataire);
exit;
?>
