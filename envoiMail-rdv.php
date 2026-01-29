<?php

/************************************************************
 * CONFIGURATION GLOBALE
 ************************************************************/

include("autoload.php");

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$urlService = $protocol . $_SERVER['HTTP_HOST'];

$services = "YAKO AFRICA ASSURANCES VIE";
$lienYako = "www.yakoafricassur.com";

define('MAIL_TEST_MODE', false); // true = aucun mail envoyé
define('MAIL_MAX_RETRY', 3);

define('MAIL_FROM_NAME', 'YAKO AFRICA ASSURANCES VIE');
define('MAIL_FROM_EMAIL', 'support.enov@yakoafricassur.com');

define('MAIL_LOG_PATH', __DIR__ . '/logs/mail/');
define('MAIL_QUEUE_PATH', __DIR__ . '/queue/mail/');


/************************************************************
 * LOG CENTRALISÉ
 ************************************************************/
function logMail($status, $subject, $to, $message)
{
    if (!is_dir(MAIL_LOG_PATH)) {
        mkdir(MAIL_LOG_PATH, 0777, true);
    }

    $log = sprintf(
        "[%s] [%s] TO:%s | SUBJECT:%s\n%s\n\n",
        date('Y-m-d H:i:s'),
        strtoupper($status),
        $to,
        $subject,
        strip_tags($message)
    );

    file_put_contents(
        MAIL_LOG_PATH . 'mail_' . date('Y-m-d') . '.log',
        $log,
        FILE_APPEND
    );
}


/************************************************************
 * TEMPLATE EMAIL (GMAIL / OUTLOOK SAFE)
 ************************************************************/
function buildEmailTemplate($destinataire, $title, $content)
{
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
    </head>
    <body style='margin:0;padding:0;background-color:#f4f4f4;'>

    <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#f4f4f4'>
    <tr>
    <td align='center'>

    <table width='600' cellpadding='0' cellspacing='0' bgcolor='#ffffff'
        style='margin:20px auto;border:1px solid #dddddd;'>

    <tr>
        <td align='center' style='padding:20px;'>
            <img src='https://www.yakoafricassur.com/gestion-demande-compte/vendors/images/entete-yako-africa.png'
                width='100%' alt='YAKO AFRICA'>
        </td>
    </tr>

    <tr>
        <td style='padding:20px;font-family:Arial,Helvetica,sans-serif;color:#333;'>
            <h2 style='margin:0;'>Bonjour " . htmlspecialchars($destinataire) . ",</h2>
            <p style='font-size:16px;font-weight:bold;color:#033f1f;'>
                " . htmlspecialchars($title) . "
            </p>
        </td>
    </tr>

    <tr>
        <td style='padding:20px;font-family:Arial,Helvetica,sans-serif;
                font-size:15px;color:#555;line-height:22px;'>
            {$content}
        </td>
    </tr>

    <tr>
        <td style='padding:20px;background-color:#f9f9f9;
                font-family:Arial,Helvetica,sans-serif;
                font-size:13px;color:#777;'>
            Cordialement,<br>
            <b>YAKO AFRICA ASSURANCES</b><br>
            <a href='https://www.yakoafricassur.com'
            style='color:#2F67F6;text-decoration:none;'>
                www.yakoafricassur.com
            </a>
        </td>
    </tr>

    </table>

    </td>
    </tr>
    </table>

    </body>
    </html>";
}


/************************************************************
 * ENVOI EMAIL (PIÈCES JOINTES + TEST MODE)
 ************************************************************/
function sendMail(array $mail)
{
    if (MAIL_TEST_MODE) {
        logMail('TEST', $mail['subject'], $mail['to'], $mail['content']);
        return true;
    }

    $boundary = md5(uniqid());

    $headers  = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";
    if (!empty($mail['cc'])) {
        $headers .= "Cc: {$mail['cc']}\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $body .= buildEmailTemplate(
        $mail['to_name'],
        $mail['title'],
        $mail['content']
    );
    $body .= "\r\n";

    // 📎 PIÈCES JOINTES
    if (!empty($mail['attachments'])) {
        foreach ($mail['attachments'] as $file) {
            if (!file_exists($file)) continue;

            $data = chunk_split(base64_encode(file_get_contents($file)));
            $name = basename($file);

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: application/octet-stream; name=\"{$name}\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"{$name}\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $data . "\r\n";
        }
    }

    $body .= "--{$boundary}--";

    //print_r($mail);
    print_r($body);
    $sent = mail($mail['to'], $mail['subject'], $body, $headers);

    logMail(
        $sent ? 'SENT' : 'FAILED',
        $mail['subject'],
        $mail['to'],
        $mail['content']
    );

    return $sent;
}


/************************************************************
 * QUEUE EMAIL
 ************************************************************/
function queueMail(array $mail)
{

    print_r($mail);
    if (!is_dir(MAIL_QUEUE_PATH)) {
        mkdir(MAIL_QUEUE_PATH, 0777, true);
    }

    $mail['retry'] = 0;
    $filename = MAIL_QUEUE_PATH . uniqid('mail_', true) . '.json';
    file_put_contents($filename, json_encode($mail));
}


/************************************************************
 * TRAITEMENT QUEUE + RETRY (CRON)
 ************************************************************/
function processMailQueue()
{
    foreach (glob(MAIL_QUEUE_PATH . '*.json') as $file) {

        $mail = json_decode(file_get_contents($file), true);
        $sent = sendMail($mail);

        if ($sent) {
            unlink($file);
        } else {
            $mail['retry']++;

            if ($mail['retry'] >= MAIL_MAX_RETRY) {
                logMail('ABANDON', $mail['subject'], $mail['to'], 'Max retry atteint');
                unlink($file);
            } else {
                file_put_contents($file, json_encode($mail));
            }
        }
    }
}


print_r($_REQUEST);
$action = (isset($_REQUEST["action"]) ? trim($_REQUEST["action"]) : NULL);
$data = (isset($_REQUEST["data"]) ? trim($_REQUEST["data"]) : NULL);


if ($data != null) {
    $data = str_replace("[", "", $data);
    $data = str_replace("]", "", $data);

    list($champ, $refchamp) = explode(":", $data, 2);
} else {
    $champ = null;
    $refchamp = null;
}

if ($champ == "idrdv" && $refchamp != null) {

    $idrdv = $refchamp;
    $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
    $sqlSelect = "
			SELECT 
				tblrdv.*,
				CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv
			LEFT JOIN users ON tblrdv.gestionnaire = users.id
			LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE tblrdv.idrdv = '$idrdv' 
			
			ORDER BY tblrdv.idrdv DESC	";
    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);

    if ($retour_rdv != null) {
        $rdv = $retour_rdv[0];

        switch ($action) {

            case "transmettreRDV":

                $result = $fonction->getRetourneContactInfosGestionnaire($rdv->codeagentgestionnaire);
                $email_final = trim($result["email_final"]);
                $telephoneGestionnaire = trim($result["telephone"]);
                $contactGestionnaire = trim($result["contacts_html"]);
                $emailgestionnaire = $rdv->emailgestionnaire;
                if (empty($email_final)) $email_final = $emailgestionnaire;
                $mailCopie = ", " . $rdv->nomgestionnaire . " <" . $email_final . ">";
                $mailCopie2 = ", KOUAKOU CARELLE <carelle.kouakou@yakoafricassur.com> , MAMA FOFANA <mama.fofana@yakoafricassur.com> , 'N'Guessan Jean-Baptiste KONAN' <nguessan.konan@yakoafricassur.com>, " . $rdv->nomgestionnaire . " <" . $email_final . ">";

                $content = "
                <p style='color:green;font-weight:bold;'>
                    Votre demande de rendez-vous <b>{$rdv->codedmd}</b> a bien été confirmé.
                </p>
                <p><b>Détails du rendez-vous :</b></p>
                        <ul>
                            <li>Date : <b>" . date('d/m/Y', strtotime($rdv->daterdveff)) . "</b></li>
                            <li>Motif : <b>{$rdv->motifrdv}</b></li>
                            <li>Contrat : <b>{$rdv->police}</b></li>
                            <li>Ville : <b>{$rdv->villes}</b></li>
                        </ul>
                        <div style='background-color:bisque;padding:10px;margin-top:15px;'>
                            <b>Gestionnaire :</b> {$rdv->nomgestionnaire}<br>
                            <b>Téléphone :</b> {$telephoneGestionnaire}
                        </div>

                ";

                $content2 = "
                <p style='color:green;font-weight:bold;'>
                   La demande de Rendez-vous <b>" . htmlspecialchars($rdv->codedmd) . " - n° " . htmlspecialchars($idrdv) . " du " .  date('d/m/Y', strtotime($rdv->daterdveff)) . "</b> vous a été affectée pour traitement .
                </p>
                
                <p><b>Détails du rendez-vous :</b></p>
                        <div style='background-color:bisque;padding:10px;margin-top:15px;'>
                            <ul>
                                        <li>Nom et Prenom  : <b>" . htmlspecialchars($rdv->nomclient) . "</b></li>
                                        <li>Contact  : <b>" . htmlspecialchars($rdv->tel) . "</b></li>
                                        <li>Date RDV : <b>" . htmlspecialchars(date('d/m/Y', strtotime($rdv->daterdveff))) . "</b></li>
                                        <li>Motif : <b>" . htmlspecialchars($rdv->motifrdv) . "</b></li>
                                        <li>Code RDV : <b>" . htmlspecialchars($rdv->codedmd) . "</b></li>
                                        <li>Id du contrat : <b>" . htmlspecialchars($rdv->police) . "</b></li>
                                        <li>Ville RDV : <b>" . htmlspecialchars($rdv->villes) . "</b></li>
                                    </ul>
                        </div>
                        <br>
                                    <div class='card-body p-2 text-center' 
                                        style='background-color: white; font-weight:bold; padding:15px; text-align:center;'>
                                        Merci de vous connecter à la plateforme de gestion des RDV pour le traitement.
                                        <br><br>
                                        <a href='$urlService' target='_blank'
                                        style='color:#d35400; text-decoration:underline; font-size:16px;'>
                                            Plateforme de gestion des RDV
                                        </a>
                                        <br>
                                    </div>
                ";

                sendMail([
                    'to'          => $email_final,
                    'to_name'     => $rdv->nomgestionnaire,
                    'subject'     => "RDV confirmé – {$rdv->codedmd}",
                    'title'       => "Confirmation de rendez-vous " . htmlspecialchars($rdv->codedmd) . " - n° " . htmlspecialchars($idrdv),
                    'content'     => $content2,
                    'cc'          => $mailCopie2 ?? null,
                    'attachments' => [] // chemins fichiers si besoin
                ]);


                sendMail([
                    'to'          => $rdv->email,
                    'to_name'     => $rdv->nomclient,
                    'subject'     => "RDV confirmé – {$rdv->codedmd}",
                    'title'       => "Confirmation de rendez-vous du " . date('d/m/Y', strtotime($rdv->daterdveff)),
                    'content'     => $content,
                    'cc'          => $mailCopie ?? null,
                    'attachments' => [] // chemins fichiers si besoin
                ]);

                break;
            case 'permissionDepotRDV':
                break;
        }
    }
}
