<?php
session_start();
$xml = simplexml_load_file('plateforme.xml');
$participantActif = $_SESSION['id'] ?? 'p1';

if (isset($_GET['contact'])) {
    $contactId = $_GET['contact'];

    foreach ($xml->participants->participant as $p) {
        if ((string)$p['id'] === $participantActif) {
            $indexToRemove = -1;
            foreach ($p->contacts->contact as $i => $c) {
                if ((string)$c['id'] === $contactId) {
                    $indexToRemove = $i;
                    break;
                }
            }
            if ($indexToRemove !== -1) {
                unset($p->contacts->contact[$indexToRemove]);
                $xml->asXML('plateforme.xml');
            }
            break;
        }
    }
}

header('Location: contacts.php');
exit;
