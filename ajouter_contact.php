<?php
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

header('Location: contacts.php');
exit;
?>
