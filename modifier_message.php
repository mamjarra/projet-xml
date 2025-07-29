<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $dest = $_POST['destinataire'] ?? '';

    if ($id !== '' && $contenu !== '') {
        $xml = simplexml_load_file('plateforme.xml');
        foreach ($xml->messages->message as $msg) {
            if ((string)$msg['id'] === $id) {
                $msg->contenu = $contenu;
                break;
            }
        }
        $xml->asXML('plateforme.xml');
    }

    if ($dest) {
        header('Location: index.php?dest=' . urlencode($dest));
    } else {
        header('Location: index.php');
    }
    exit;
}
