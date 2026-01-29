<?php

$host = 'localhost';
$db = 'togo_togocom_stk';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion OK<br>";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Appel des fonctions
$data = selectData($pdo);

$retrouver = [];
$nonretrouver = [];

if (!empty($data)) {
    foreach ($data as $i => $row) {
        $canton     = strtoupper(removeSpecialChars($row['canton'] ?? '', ''));
        $commune    = removeSpecialChars($row['commune'] ?? '');
        $prefecture = removeSpecialChars($row['prefecture'] ?? '');
        $region     = removeSpecialChars($row['region'] ?? '');
        $pharma_detail     = removeSpecialChars($row['id'] ?? '');

        echo "$i => $canton - $commune - $prefecture - $region<br>" . PHP_EOL;

        $separateurs = [",", ";", "|", "-"];

        $resultat = multiexplode($separateurs, $canton);

        //print_r($resultat);
        $search = $resultat[0];
        $recharche = selectService($pdo, $search);
        if (empty($recharche)) {
            echo "Aucune donnée trouvée dans la table `services2`.<br>" . PHP_EOL;

            //print_r($row);

            $nonretrouver[] = $row;
        } else {

            $retrouver[] = $row;
            /*echo "Donnee trouvée dans la table `services2`.<br>";


            //print_r($recharche);

            //METTRE A JOUR LA TABLE `services2`
            updateService($pdo, $search, $pharma_detail, $canton);

            //METTRE A JOUR LA TABLE `pharmacie_details`
            $id_service = $recharche[0]['id_service'];
            $keyword = $recharche[0]['keyword'];
            updatePharmacieDetail($pdo, $pharma_detail, $id_service, $keyword);
            */
        }
    }
    // Écrire dans deux fichiers séparés
    file_put_contents('retrouves.json', json_encode($retrouver, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    file_put_contents('nonretrouves.json', json_encode($nonretrouver, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    exportToCSV('retrouves.csv', $retrouver);
    exportToCSV('nonretrouves.csv', $nonretrouver);

    echo "<br><strong>✅ Export terminé :</strong><br>";
    echo "- " . count($retrouver) . " trouvés → <code>retrouves.json</code><br>";
    echo "- " . count($nonretrouver) . " non trouvés → <code>nonretrouves.json</code><br>";
} else {
    echo "Aucune donnée trouvée dans la table `pharmacie_details`.";
}


function updatePharmacieDetail(PDO $pdo, $pharma_detail, $id_service, $keyword)
{
    try {
        $sql = "UPDATE pharmacie_details SET id_service=? , keyword=? WHERE id = '$pharma_detail'";
        $params = [$id_service, $keyword];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}


function updateService(PDO $pdo, $search, $pharma_detail, $canton)
{
    try {

        $sql = "UPDATE services2 SET pharma_detail=? , canton=? WHERE node='pharmacie' and libelle = '$search'";
        $params = [$pharma_detail, $canton];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
/**
 * Récupère toutes les données de la table pharmacie_details
 */
function selectData(PDO $pdo)
{
    try {
        $sql = "SELECT * FROM pharmacie_details";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la sélection : " . $e->getMessage();
        return [];
    }
}


function selectService(PDO $pdo, $search)
{
    try {
        $sql = "SELECT libelle, id_service, keyword FROM services2 WHERE  node='pharmacie' and libelle = '$search' ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la sélection : " . $e->getMessage();
        return [];
    }
}

/**
 * Insère les données depuis un fichier JSON dans la base
 */

function insertData($pdo)
{
    // Chemin vers le fichier JSON
    $fichier = 'file-Pharmacies-19-12-2024 11_22_38.json';

    // Vérifier si le fichier existe
    if (!file_exists($fichier)) {
        die("Fichier JSON introuvable.");
    }

    // Lecture du contenu du fichier JSON
    $contenu = file_get_contents($fichier);

    // Décodage JSON en tableau associatif
    $data = json_decode($contenu, true);

    if (is_array($data) && isset($data['features'])) {

        $stmt = $pdo->prepare("
        INSERT INTO pharmacie_details (
            region, prefecture, commune, canton,
            pharmacie_nom, pharmacie_type, pharmacie_secteur,
            ouverture, annee, geometry_name, geometry_type,
            geometry_coordinates0, geometry_coordinates1,
            pharma_id, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE statut='1'
    ");

        foreach ($data['features'] as $donnee) {
            try {
                $properties = $donnee['properties'];
                $geometry   = $donnee['geometry'];

                // Nettoyage et extraction
                $region_nom_bdd     = trim($properties['region_nom_bdd'] ?? '');
                $prefecture_nom_bdd = trim($properties['prefecture_nom_bdd'] ?? '');
                $commune_nom_bdd    = trim($properties['commune_nom_bdd'] ?? '');
                $canton_nom_bdd     = trim($properties['canton_nom_bdd'] ?? '');
                $pharmacie_nom      = trim($properties['pharmacie_nom'] ?? '');
                $pharmacie_type     = trim($properties['pharmacie_type'] ?? '');
                $pharmacie_secteur  = trim($properties['pharmacie_secteur'] ?? '');
                $ouverture          = trim($properties['ouverture'] ?? '');
                $annee              = trim($properties['annee'] ?? '');

                $geometry_name = trim($donnee['geometry_name'] ?? '');
                $geometry_type = trim($geometry['type'] ?? '');
                $coordinates   = $geometry['coordinates'] ?? [0, 0];
                $coordinates0  = $coordinates[0] ?? 0;
                $coordinates1  = $coordinates[1] ?? 0;
                $pharma_id     = trim($donnee['id'] ?? '');

                // Insertion
                $stmt->execute([
                    $region_nom_bdd,
                    $prefecture_nom_bdd,
                    $commune_nom_bdd,
                    $canton_nom_bdd,
                    $pharmacie_nom,
                    $pharmacie_type,
                    $pharmacie_secteur,
                    $ouverture,
                    $annee,
                    $geometry_name,
                    $geometry_type,
                    $coordinates0,
                    $coordinates1,
                    $pharma_id
                ]);
            } catch (Exception $e) {
                echo "Erreur lors de l'insertion de pharma_id = $pharma_id : " . $e->getMessage() . "<br>";
            }
        }

        echo "Importation terminée.";
    } else {
        echo "Erreur : contenu JSON invalide ou vide.";
    }
}

function removeSpecialChars($string, $replace = ' ')
{
    // Remplacer les caractères accentués par leur équivalent non accentué
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

    // Remplacer les caractères non alphanumériques (sauf tirets et underscores) par un espace
    $string = preg_replace('/[^a-zA-Z0-9-_]/', $replace, $string);

    // Supprimer les espaces multiples
    $string = preg_replace('/\s+/', ' ', $string);

    // Supprimer les espaces en début/fin
    $string = trim($string);

    return $string;
}


function multiexplode(array $delimiters, string $string)
{
    // Remplace chaque délimiteur par le premier de la liste
    $mainDelimiter = $delimiters[0];
    $replace = str_replace($delimiters, $mainDelimiter, $string);

    // Exécute explode sur le délimiteur principal
    return explode($mainDelimiter, $replace);
}


function exportToCSV($filename, $data)
{
    if (empty($data)) return;

    $f = fopen($filename, 'w');

    // Écrire l'en-tête (clés du tableau)
    fputcsv($f, array_keys($data[0]));

    // Écrire les lignes
    foreach ($data as $row) {
        fputcsv($f, $row);
    }

    fclose($f);
}
