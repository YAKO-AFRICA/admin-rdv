<?php
session_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("../autoload.php");
include("../vendor/autoload.php");
// include("../config/routes.php");

try {

    // 1️⃣ Récupérer tous les RDV encore actifs (etat = 2)
    $sql = "
        SELECT *
        FROM tblrdv
        WHERE etat = 1 OR etat = 2
    ";

    $liste_rdvs = $fonction->_getSelectDatabases($sql);
    $rdvs = []; // Initialize $rdvs as empty array

    if ($liste_rdvs != null) {

        $liste_rdvs = array_filter($liste_rdvs, function ($rdv) use ($fonction) {

            if ($rdv->etat == "" || $rdv->etat == null || $rdv->etat == " ") {
                return false;
            }
            // // On ne filtre que les RDV etat = 2 ou 1
            if ($rdv->etat != "2" && $rdv->etat != "1") {
                return true;
            }
            $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';

            // Si pas de date effective → on garde
            $daterdveff = $rdv->daterdveff ?? $daterdv;
            
            if ($daterdveff == null) {
                return true;
            }

            // Calcul du délai
            $delai = $fonction->getDelaiRDV($rdv->daterdveff ?? $daterdv);

            if (($rdv->etat == "2" || $rdv->etat == "1") && $delai['etat'] !== 'expire') {
                return false;
            }

            return true;
        });
    
        // Réindexation du tableau
        $rdvs = array_values($liste_rdvs);
    }

    $ids_a_expirer = [];

    // 3️⃣ Créer le fichier Excel si rdvs > 0
    if (count($rdvs) > 0) {
        $spreadsheet = new Spreadsheet(); // Move this BEFORE using it
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('RDV expirés +10j');

        // En-têtes
        $headers = [
            'A1' => 'ID RDV',
            'B1' => 'Code demande',
            'C1' => 'Nom client',
            'D1' => 'Téléphone',
            'E1' => 'Date RDV',
            'F1' => 'Date RDV effective',
            'G1' => 'Etat avant',
            'H1' => 'Jours expirés',
            'I1' => 'Motif'
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Données
        $row = 2;
        
        // 2️⃣ Calcul métier AVEC getDelaiRDV()
        foreach ($rdvs as $rdv) {
            $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';

            $delai = $fonction->getDelaiRDV($rdv->daterdveff ?? $daterdv);

            if ($delai['etat'] === 'expire' && isset($delai['jours']) && $delai['jours'] >= 10) {
                $ids_a_expirer[] = (int)$rdv->idrdv;
            }

            if (!in_array((int)$rdv->idrdv, $ids_a_expirer)) {
                continue;
            }

            $delai = $fonction->getDelaiRDV($rdv->daterdveff ?? $rdv->daterdv);

            $sheet->setCellValue("A$row", $rdv->idrdv);
            $sheet->setCellValue("B$row", $rdv->codedmd ?? '');
            $sheet->setCellValue("C$row", $rdv->nomclient ?? '');
            $sheet->setCellValue("D$row", $rdv->tel ?? '');
            $sheet->setCellValue("E$row", $rdv->daterdv ?? '');
            $sheet->setCellValue("F$row", $rdv->daterdveff ?? '');
            $sheet->setCellValue("G$row", $rdv->etat);
            $sheet->setCellValue("H$row", $delai['jours'] ?? '');
            $sheet->setCellValue("I$row", 'Expiration automatique > 10 jours');

            $row++;
        }

        // Auto-size
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sauvegarde
        $filename = 'rdv_expire_' . date('Ymd_His') . '.xlsx';
        $path = __DIR__ . '../exports/' . $filename;

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        // 3️⃣ Mise à jour si nécessaire
        if (!empty($ids_a_expirer)) {

            $ids = implode(',', $ids_a_expirer);

            $sqlUpdate = "UPDATE tblrdv SET etat= ?, reponse=?, datetraitement=now(),  traiterLe=now() , updatedAt=now(), etatSms =? WHERE idrdv IN ($ids)";
            $queryOptions = array(
                '0',
                addslashes(htmlspecialchars(trim(ucfirst(strtolower('Annulation automatique du rendez-vous n° ' . $rdv->idrdv . ' du ' . date('d/m/Y', strtotime($rdv->daterdv)) . ' par le système. Date expirée avant la date de traitement du gestionnaire.'))))),
                '1',
            );

            $result = $fonction->_Database->Update($sqlUpdate, $queryOptions);
            if ($result != null) {
                $retour = $rdv->idrdv;
                $message = "Cher client(e), votre demande de rdv n° " . $rdv->codedmd . "  du " . date('d/m/Y', strtotime($rdv->daterdv)) . " a été rejetée." . PHP_EOL . "Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                // envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);
                global $fonction;
                $numero = "225" . substr($rdv->tel, -10);
                //$ref_sms = "RDV-" . $idrdv;

                $sms_envoi = new SMSService();
                if (strlen($message) > 160) $message = substr($message, 0, 160);
                $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                $sqlUpdateRdvUpdate = "UPDATE tblrdv SET etatSms =?  WHERE idrdv = ?";
                $queryOptions = array(
                    '1',
                    intval($rdv->idrdv)
                );
                $fonction->_Database->Update($sqlUpdateRdvUpdate, $queryOptions);
            }
        }

        echo json_encode([
            'success' => true,
            'rdv' => $rdvs,
            'total_rdv' => count($rdvs),
            'expired_updated' => count($ids_a_expirer),
            'ids' => $ids_a_expirer
        ]);
        
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'No expired RDV found',
            'total_rdv' => 0,
            'expired_updated' => 0,
            'ids' => []
        ]);
    }

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}