<?php
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // On récupère aussi le destinataire pour garder la conversation active
    $dest = isset($_GET['dest']) ? $_GET['dest'] : null;

    $xml = simplexml_load_file('plateforme.xml');
    $index = 0;
    foreach ($xml->messages->message as $msg) {
        if ((string)$msg['id'] === $id) {
            // Optionnel : vérifier que le message appartient bien à l'utilisateur actif
            // Pour cela, il faudrait la session utilisateur et comparaison
            unset($xml->messages->message[$index]);
            break;
        }
        $index++;
    }

    $xml->asXML('plateforme.xml');
}

// Redirection vers la conversation en cours
if ($dest) {
    header('Location: index.php?dest=' . urlencode($dest));
} else {
    header('Location: index.php');
}
exit;
