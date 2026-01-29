<?php

$host = 'localhost';
$db = 'laloyale_bdrh';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

$csvFile = fopen('liste_agents_yako.csv', 'r');

if ($csvFile === false) {
    die("Impossible d'ouvrir le fichier.");
}

$firstLine = true;
$i = 0;

while (($data = fgetcsv($csvFile, 1000, ",")) !== false) {
    if ($firstLine) {
        $firstLine = false;
        continue;
    }

    $data = explode(';', $data[0]);

    // Extraction des données
    $matricule = $data[0];
    $nomPrenom = $data[1];
    $date_embauche = $data[2];
    $fonctions = $data[3];
    $direction = $data[4];
    $contrat = $data[5];
    $superieur = $data[6];
    $email = $data[7];
    $contact = $data[8];

    $identifiant_agent = "yaav-" . $matricule;

    if (!empty($contact)) {
        $contact = str_replace('-', '', $contact);
    }

    if (!empty($date_embauche)) {
        $date_embauche = date('Y-m-d', strtotime($date_embauche));
    }

    // Séparation nom/prénom avec prise en compte des noms courts
    $nom = '';
    $prenom = '';

    if (!empty($nomPrenom)) {
        $parts = explode(' ', trim($nomPrenom));
        $parts = array_filter($parts);
        $parts = array_values($parts);

        if (count($parts) >= 2) {
            if (strlen($parts[0]) < 3) {
                $nom = $parts[1];
                $prenom = implode(' ', array_slice($parts, 2));
            } else {
                $nom = $parts[0];
                $prenom = implode(' ', array_slice($parts, 1));
            }
        } elseif (count($parts) === 1) {
            $nom = $parts[0];
            $prenom = '';
        }
    }

    if (empty($email)) {
        $email = "yaav." . $matricule . "@yakoafricassur.com";
    }

    // Insertion ou mise à jour dans la table `agents`
    $stmt = $pdo->prepare("
        INSERT INTO agents (matricule, nom, prenom, date_embauche, poste, departement, type_contrat, superieur_hierarchique, email, telephone, identifiant_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE matricule = ?, nom = ?, prenom = ?, date_embauche = ?, poste = ?, departement = ?, type_contrat = ?, superieur_hierarchique = ?, email = ?, telephone = ?, identifiant_agent = ?
    ");

    $paramsAgent = [
        $matricule, $nom, $prenom, $date_embauche, $fonctions, $direction, $contrat, $superieur, $email, $contact, $identifiant_agent,
        $matricule, $nom, $prenom, $date_embauche, $fonctions, $direction, $contrat, $superieur, $email, $contact, $identifiant_agent
    ];

    ///$stmt->execute($paramsAgent);

    // 🔐 Hash du mot de passe
    $hashedPassword = password_hash('12345678', PASSWORD_DEFAULT);

    // Insertion ou mise à jour dans la table `users`
    $stmt2 = $pdo->prepare("
        INSERT INTO users (name, email, type, password)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email), type = VALUES(type), password = VALUES(password)
    ");

    $paramsUser = [$nomPrenom, $email, "agent", $hashedPassword];
    $stmt2->execute($paramsUser);

    // 🔁 Récupération de l’ID utilisateur
    /*$userId = $pdo->lastInsertId();
    if (!$userId) {
        $stmt3 = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt3->execute([$email]);
        $userId = $stmt3->fetchColumn();
    }

    echo "Ligne $i : $nomPrenom ($email) → ID utilisateur = $userId\n";

    // Insertion ou mise à jour dans la table `agents_users`
    $stmt3 = $pdo->prepare("
        update agents set uuid = ? where matricule = ?
    ");
    $stmt3->execute([$userId, $matricule]);*/
    $i++;
}

fclose($csvFile);

echo "\nImport terminé avec succès !";
