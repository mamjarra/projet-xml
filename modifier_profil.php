<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit;
}

$xmlFile = 'plateforme.xml';
$participantId = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $statut = trim($_POST['statut']);

    if ($nom && file_exists($xmlFile)) {
        $xml = simplexml_load_file($xmlFile);
        $participant = null;

        // Recherche du participant
        foreach ($xml->participants->participant as $p) {
            if ((string)$p['id'] === $participantId) {
                $participant = $p;
                break;
            }
        }

        // Mise à jour des informations
        if ($participant) {
            $participant->nom = htmlspecialchars($nom);
            $participant->email = htmlspecialchars($email);
            $participant->statut = htmlspecialchars($statut);

            $xml->asXML($xmlFile);
            header("Location: profil.php");
            exit;
        }
    }
}

// En cas d’erreur
echo "⚠️ Une erreur est survenue. <a href='profil.php'>Retour au profil</a>";
