<?php
session_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("../autoload.php");
include("../vendor/autoload.php");

try {
    // 1️⃣ Récupérer tous les RDV encore actifs (etat = 1 ou 2)
    $sql = "
        SELECT *
        FROM tblrdv
        WHERE etat = 1 OR etat = 2
    ";

    $liste_rdvs = $fonction->_getSelectDatabases($sql);
    $rdvs_expires = [];

    if ($liste_rdvs != null && is_array($liste_rdvs)) {
        
        // Filtrer les RDV expirés depuis plus de 10 jours
        foreach ($liste_rdvs as $rdv) {
            // Vérifier si la date existe
            $date_rdv = $rdv->daterdveff ?? $rdv->daterdv ?? null;
            
            if ($date_rdv) {
                $delai = $fonction->getDelaiRDV($date_rdv);
                
                // Si RDV expiré depuis plus de 10 jours
                if ($delai['etat'] === 'expire' && isset($delai['jours']) && $delai['jours'] >= 10) {
                    $rdvs_expires[] = $rdv;
                }
            }
        }
    }

    $ids_a_expirer = [];

    // 2️⃣ Créer le fichier Excel si des RDV expirés existent
    if (count($rdvs_expires) > 0) {
        $spreadsheet = new Spreadsheet();
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
        
        foreach ($rdvs_expires as $rdv) {
            $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';
            $delai = $fonction->getDelaiRDV($rdv->daterdveff ?? $daterdv);
            
            // Enregistrer l'ID pour mise à jour
            $ids_a_expirer[] = (int)$rdv->idrdv;

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

        // Créer le dossier exports s'il n'existe pas
        $export_dir = __DIR__ . '/exports/';
        if (!is_dir($export_dir)) {
            mkdir($export_dir, 0777, true);
        }

        // Sauvegarde
        $filename = 'rdv_expire_' . date('Ymd_His') . '.xlsx';
        $path = $export_dir . $filename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        // 3️⃣ Mise à jour des RDV expirés
        if (!empty($ids_a_expirer)) {
            $ids = implode(',', $ids_a_expirer);
            
            // Mettre à jour tous les RDV expirés
            $sqlUpdate = "UPDATE tblrdv SET etat = ?, reponse = ?, datetraitement = NOW(), traiterLe = NOW(), updatedAt = NOW(), etatSms = ? WHERE idrdv IN ($ids)";
            
            foreach ($rdvs_expires as $rdv) {
                $message_reponse = addslashes(htmlspecialchars(trim(ucfirst(strtolower('Annulation automatique du rendez-vous n° ' . $rdv->idrdv . ' du ' . date('d/m/Y', strtotime($rdv->daterdv)) . ' par le système. Date expirée avant la date de traitement du gestionnaire.')))));
                
                $queryOptions = array(
                    '0',
                    $message_reponse,
                    '1'
                );
                
                $result = $fonction->_Database->Update($sqlUpdate, $queryOptions);
                
                if ($result != null) {
                    // Envoyer SMS
                    $message_sms = "Cher client(e), votre demande de rdv n° " . $rdv->codedmd . " du " . date('d/m/Y', strtotime($rdv->daterdv)) . " a été rejetée." . PHP_EOL . "Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                    
                    $numero = "225" . substr($rdv->tel, -10);

                    // $sms_envoi = new SMSService();
                    // if (strlen($message) > 160) $message = substr($message, 0, 160);
                    // $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");
                    
                    // Mettre à jour le statut SMS
                    $sqlUpdateRdvUpdate = "UPDATE tblrdv SET etatSms = ? WHERE idrdv = ?";
                    $queryOptionsSms = array('1', intval($rdv->idrdv));
                    $fonction->_Database->Update($sqlUpdateRdvUpdate, $queryOptionsSms);
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Traitement terminé avec succès',
            'total_rdv' => count($rdvs_expires),
            'expired_updated' => count($ids_a_expirer),
            'ids' => $ids_a_expirer,
            'file' => $filename
        ]);
        
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Aucun RDV expiré trouvé',
            'total_rdv' => 0,
            'expired_updated' => 0,
            'ids' => []
        ]);
    }

} catch (Throwable $e) {
    error_log("Erreur dans expire_rdv.php : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>