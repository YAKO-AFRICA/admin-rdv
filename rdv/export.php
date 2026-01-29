<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require "../vendor/autoload.php";

$rdvLe     = $_POST['rdvLe'] ?? null;
$rdvAu     = $_POST['rdvAu'] ?? null;
$villesRDV = $_POST['villesRDV'] ?? null;
$ListeGest = $_POST['ListeGest'] ?? null;

$plus = " WHERE etape != '1' ";

if (!empty($rdvLe)) {
    $plus .= " AND daterdveff >= '" . addslashes($rdvLe) . "'";
}
if (!empty($rdvAu)) {
    $plus .= " AND daterdveff <= '" . addslashes($rdvAu) . "'";
}
if (!empty($villesRDV)) {
    [$idVille] = explode(";", $villesRDV);
    $plus .= " AND idTblBureau = " . intval($idVille);
}
if (!empty($ListeGest)) {
    [$idGest] = explode("|", $ListeGest);
    $plus .= " AND gestionnaire = " . intval($idGest);
}

$sql = "
    SELECT 
        dateajou,
        nomclient,
        tel,
        police,
        motifrdv,
        daterdveff,
        TRIM(tblvillebureau.libelleVilleBureau) AS ville,
        CONCAT(users.nom,' ',users.prenom) AS gestionnaire,
        etat
    FROM tblrdv
    LEFT JOIN users ON users.id = tblrdv.gestionnaire
    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
    $plus
    ORDER BY idrdv DESC
";

$data = $fonction->_getSelectDatabases($sql);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Aucune donnée"]);
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Entêtes
$headers = [
    "Date demande",
    "Demandeur",
    "Téléphone",
    "Contrat",
    "Motif RDV",
    "Date RDV",
    "Ville",
    "Gestionnaire",
    "État"
];

$sheet->fromArray($headers, null, 'A1');

// Données
$row = 2;
foreach ($data as $rdv) {
    $sheet->fromArray([
        $rdv->dateajou,
        $rdv->nomclient,
        $rdv->tel,
        $rdv->police,
        $rdv->motifrdv,
        date('d/m/Y', strtotime($rdv->daterdveff)),
        $rdv->ville,
        $rdv->gestionnaire,
        Config::tablo_statut_rdv[$rdv->etat]['libelle'] ?? 'Inconnu'
    ], null, "A{$row}");
    $row++;
}

// Sauvegarde
$filename = "export_bordereau_rdv_" . date("Ymd_His") . ".xlsx";
$path = "../exports/tmp/" . $filename;

$writer = new Xlsx($spreadsheet);
$writer->save($path);

// Retour AJAX
echo json_encode([
    "status" => "success",
    "file" => $path
]);
