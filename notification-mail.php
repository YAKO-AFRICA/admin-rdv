<?php


include("autoload.php");

$services = "YAKO AFRICA ASSURANCES VIE";
$lienYako = "www.yakoafricassur.com";
$lienService =  "";

$action = (isset($_REQUEST["action"]) ? trim($_REQUEST["action"]) : NULL);
$idprestation = (isset($_REQUEST["idprestation"]) ? trim($_REQUEST["idprestation"]) : NULL);
$idusers = (isset($_REQUEST["idusers"]) ? trim($_REQUEST["idusers"]) : NULL);

$subject = "";

$mail = false;
$lien = "https://yakoafricassur.com/espace-client/login.php";


if ($idprestation != null) {

    $retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idprestation) . "'");
    if ($retour != null) {
        $prestation = new tbl_prestations($retour[0]);
        $retour_documents = $fonction->_getListeDocumentPrestation(1);

        $i = 0;
        $docs = "";
        $docs_etatPrestation = "";
        if ($retour_documents !== null) {
            for ($i = 0; $i <= count($retour_documents) - 1; $i++) {

                $tablo = $retour_documents[$i];

                $id_prestation = $tablo["idPrestation"];
                $path_doc = trim($tablo["path"]);
                $type_doc = trim($tablo["type"]);
                $doc_name = trim($tablo["libelle"]);
                $ref_doc = trim($tablo["id"]);
                $datecreation_doc = trim($tablo["created_at"]);
                $documents = Config::URL_PRESTATION_RACINE . $path_doc;
                $values = $id_prestation . "-" . $documents;

                if ($type_doc == "RIB") $nom_document = "RIB";
                elseif ($type_doc == "Police") $nom_document = "Police du contrat d'assurance";
                elseif ($type_doc == "bulletin") $nom_document = "bulletin de souscription";
                elseif ($type_doc == "AttestationPerteContrat") $nom_document = "Attestation de declaration de perte";
                elseif ($type_doc == "CNI") $nom_document = "CNI";
                elseif ($type_doc == "etatPrestation") {
                    $nom_document = "Fiche de demande de prestation";
                    $docs_etatPrestation = '<ul> <li>' . $nom_document . ' : <b><a href="' . $documents . '" target="_blank"> ' . $nom_document . ' </a></b></li></ul> ';
                }
                else $nom_document = "Fiche d'identification du numero de paiement";

                $docs .= '<ul> <li>' . $nom_document . ' : <b><a href="' . $documents . '" target="_blank"> ' . $nom_document . ' </a></b></li></ul> ';
            }
            $listes_documents = '<div class="card-body p-2" style="background-color:bisque ; font-weight:bold;">
            <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;">Documents joints</h4> <hr>' . $docs . ' </div> ';
        }

        switch ($action) {

            case "confirmerRejet":

                $ListeMotifRejet = $fonction->_GetListeMotifRejetPrestation($prestation->code, null, true);

                $subject = "Prestation -  " . strtoupper($prestation->code);

                $titre = "<label style='color:red'> La demande de prestation <b>" . $prestation->code . "</b>  a été rejetée</label>,</br>";


                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                               " . $titre . " <br>                                
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                            <li>Montant souhaité : <b>" . $prestation->montantSouhaite . " FCFA</b></li>
                                            <li>Moyen de paiement : <b>" . $prestation->lib_moyenPaiement . "</b></li>
                                            
                                        </ul>
                                        $docs_etatPrestation
                                        <br>
                                        <div class='card-body p-2' style='background-color:bisque ; font-weight:bold;'>
            <h4 class='text-center p-4' style='color:#033f1f ; font-weight:bold;'>Motif de rejet</h4> <hr>$ListeMotifRejet</div>                          
                                </div>
                                ";

                $message = format_mail_by_NISSA($prestation->souscripteur2, $text_form, $titre);

                //message = format_mail($prestation, $titre, $listes_documents);

                $to = $prestation->email;
                $mail = true;

                break;

            case "validerprestation":


                $subject = "Prestation -  " . strtoupper($prestation->code);
                $titre = "<label style='color:green'> La demande de prestation <b>" . $prestation->code . "</b>  est en cours de traitement </label></br>";

                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                        " . $titre . " <br>                                   
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                            <li>Montant souhaité : <b>" . $prestation->montantSouhaite . "</b></li>
                                            <li>Moyen de paiement : <b>" . $prestation->lib_moyenPaiement . "</b></li>
                                            
                                        </ul>
                                        <br>
                                        " . $listes_documents . "                          
                                </div>";

                $message = format_mail_by_NISSA($prestation->souscripteur2, $text_form, $titre);

                $to = $prestation->email;
                $mail = true;

                break;
        }
    }
}



if ($idusers != null) {

    $retourUsers = $fonction->_GetUsers(" AND `id` = '$idusers'  ");
    if ($retourUsers != NULL) {


        switch ($action) {

            case "passeOublie":

                $userConnect = strtoupper($retourUsers->userConnect);
                $subject = "Mot de passe oublie -  " . $userConnect;

                $titre = "";
                $to = $retourUsers->email;
                $mail = true;

                $text_form = "
                <div style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                    <div class='content'>
                        <div class='title'>
                            Bonjour chèr(e) agent <span class='important'>" . $userConnect . "</span> !
                        </div>  <br>
                        
                        Retrouver vos paramètres d’accès ci-dessous  :<br>
                        <ul>
                            <li>Login : <b>" . $retourUsers->email . "</b></li>
                            <li>Mot de passe : <b>" . $retourUsers->password . "</b></li>
                            <li>Lien de connexion : <b>" . $lienService . "</b></li>
                                                                                       
                        </ul>
                    </div>
                </div>";

                $message = format_mail_by_NISSA($userConnect, $text_form, $titre);
                break;
        }
    }
}


if ($mail && $to != "") {

    $boundary = md5(uniqid(microtime(), TRUE));

    // Headers
    $headers = 'From: ' . $services . ' <support.enov@yakoafricassur.com>' . "\r\n";
    $headers .= 'Cc: HELP DESK YAKO AFRICA <support.enov@yakoafricassur.com> , SUPPORT YNOV <no-reply@yakoafricassur.com>';
    $headers .= 'Mime-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: multipart/mixed;boundary=' . $boundary . "\r\n";
    $headers .= "\r\n";

    // Message
    //$msg = 'Texte affiché par des clients mail ne supportant pas le type MIME.'."\r\n\r\n";
    // Message HTML

    $msg = '--' . $boundary . "\r\n";
    $msg .= 'Content-type: text/html; charset=utf-8' . "\r\n\r\n";
    $msg .= $message;

    //print $msg . PHP_EOL;

    // Fin
    $msg .= '--' . $boundary . "\r\n";

    // Function mail()

    if (mail($to, $subject, $msg, $headers)) // Envoi du message
    {
        $to_log = __FUNCTION__ . ' | Message de notification a bien été envoyé à ' . $to . " ( " . $message . " ) ";
        print $to_log;

        $fonction->_UpdateStatutEnvoiMail($prestation, "1", $contenu = "");
        //echo json_encode($resultat);
    } else // Non envoyé
    {
        $to_log = __FUNCTION__ . ' | Le message de notification n\'a pas pu être envoyé à ' . $to . " ( " . $message . " ) ";
        print $to_log;
    }

    //print $to;
    $log_message = $message . PHP_EOL . $to_log;
    file_put_contents(__DIR__ . "/config/log/notification_mail_" . @date('Y-m-d') . ".txt", $log_message . PHP_EOL, FILE_APPEND);
} else {
    $to_log = "desole le mail a echoue";
}



function format_mail_by_NISSA($nom_destinataire, $text_form, $docs = null)
{
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #555;
            }
            .title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }
            .content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .important {
                font-weight: bold;
                color: #2C7B77;
            }
            .link {
                color: #2F67F6;
                text-decoration: none;
            }
            .button {
                background-color: #033f1f;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
                border-radius: 3px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>                   

        <div style='background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;'>

            <div class='row'>
                <div class='card-box mb-30'>
                    <div class='row center-block'>
                        <div class='col-lg-3'></div>

                        <div class='col-lg-6'>
                            <center>
                                <img src='www.yakoafricassur.com/gestion-demande-compte/vendors/images/entete-yako-africa.png' width='100%' alt='' srcset=''>
                            </center>
                        </div>
                        <div class='col-lg-3'></div>
                    </div>
                </div>
            </div>
            <table align='center' border='0' cellpadding='0' cellspacing='0' role='presentation'  style='background:#fff;background-color:#fff;width:100%;'>
                <tbody>
                    <tr>
                        <td  style='border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;'>
                            <div class='mj-column-per-100 outlook-group-fix' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;'>

                            <table border='0' cellpadding='0' cellspacing='0' role='presentation'  style='vertical-align:bottom;' width='100%'>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                        <table align='center' border='0' cellpadding='0' cellspacing='0'
                                            role='presentation'
                                            style='border-collapse:collapse;border-spacing:0px; background-color:#033f1f;'>
                                            <tbody>
                                                <tr>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;padding-bottom:40px;word-break:break-word;'>
                                        <div
                                            style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:28px;font-weight:bold;line-height:1;text-align:center;color:#555;'>
                                            Chèr(e) &nbsp; " . $nom_destinataire . "  !!

                                        </div>
                                    </td>
                                </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                " . $text_form . "  
        
                                
                            </td>
                        </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;'>
                                    Best regards,<br><br> YAKOAFRICASSUR<br>DSI conception<br>
                                    <a href='https://www.yakoafricassur.com'
                                        style='color:#2F67F6'>yakoafricassur.com</a>
                                </div>
                                 <div class='content'>
            Cordialement,<br>
            L'équipe de YAKOAFRICASSUR<br>
            <a href='https://www.yakoafricassur.com' class='link'>www.yakoafricassur.com</a>
        </div>

                            </td>
                        </tr>

                    </table>

                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
    </body>
    </html>
";

    return $message;
}



function format_mail(tbl_prestations $prestation, $message, $docs = null)
{
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #555;
            }
            .title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }
            .content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .important {
                font-weight: bold;
                color: #2C7B77;
            }
            .link {
                color: #2F67F6;
                text-decoration: none;
            }
            .button {
                background-color: #033f1f;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
                border-radius: 3px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>                   

        <div style='background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;'>

            <div class='row'>
                <div class='card-box mb-30'>
                    <div class='row center-block'>
                        <div class='col-lg-3'></div>

                        <div class='col-lg-6'>
                            <center>
                                <img src='www.yakoafricassur.com/gestion-demande-compte/vendors/images/entete-yako-africa.png' width='100%' alt='' srcset=''>
                            </center>
                        </div>
                        <div class='col-lg-3'></div>
                    </div>
                </div>
            </div>
            <table align='center' border='0' cellpadding='0' cellspacing='0' role='presentation'  style='background:#fff;background-color:#fff;width:100%;'>
                <tbody>
                    <tr>
                        <td  style='border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;'>
                            <div class='mj-column-per-100 outlook-group-fix' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;'>

                            <table border='0' cellpadding='0' cellspacing='0' role='presentation'  style='vertical-align:bottom;' width='100%'>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                        <table align='center' border='0' cellpadding='0' cellspacing='0'
                                            role='presentation'
                                            style='border-collapse:collapse;border-spacing:0px; background-color:#033f1f;'>
                                            <tbody>
                                                <tr>


                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;padding-bottom:40px;word-break:break-word;'>
                                        <div
                                            style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:28px;font-weight:bold;line-height:1;text-align:center;color:#555;'>
                                            Chèr(e) &nbsp; " . strtoupper($prestation->souscripteur) . "  !!

                                        </div>
                                    </td>
                                </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                        " . $message . "<br>
                                        
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                            <li>Montant souhaité : <b>" . $prestation->montantSouhaite . "</b></li>
                                            <li>Moyen de paiement : <b>" . $prestation->lib_moyenPaiement . "</b></li>
                                            
                                        </ul>
                                        <br>
                                        " . $docs . "                          
                                </div>
        
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;'>
                                    Best regards,<br><br> YAKOAFRICASSUR<br>DSI conception<br>
                                    <a href='https://www.yakoafricassur.com'
                                        style='color:#2F67F6'>yakoafricassur.com</a>
                                </div>
                                 <div class='content'>
            Cordialement,<br>
            L'équipe de YAKOAFRICASSUR<br>
            <a href='https://www.yakoafricassur.com' class='link'>www.yakoafricassur.com</a>
        </div>

                            </td>
                        </tr>

                    </table>

                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
    </body>
    </html>
";

    return $message;
}
?>



<!--!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">


<head>
    <title><?= $subject ?>
    </title>
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/logo-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="vendors/images/logo-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="vendors/images/logo-icon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
   
    <style type="text/css">
        @media only screen and (max-width:480px) {
            @-ms-viewport {
                width: 320px;
            }

            @viewport {
                width: 320px;
            }
        }
    </style>

    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
            }
        }
    </style>


    <style type="text/css">
    </style>

</head>

<body style="background-color:#f9f9f9;">


    <div style="background-color:#f9f9f9;">

        <div style="background:#f9f9f9;background-color:#f9f9f9;Margin:0px auto;max-width:600px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                style="background:#f9f9f9;background-color:#f9f9f9;width:100%;">
                <tbody>
                    <tr>
                        <td
                            style="border-bottom:#333957 solid 5px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">

                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div style="background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;">

            <div class="row">
                <div class="card-box mb-30">
                    <div class="row center-block">
                        <div class="col-lg-3"></div>

                        <div class="col-lg-6">
                            <center>
                                <img src="vendors/images/entete-yako-africa.png" width="100%" alt="" srcset="">
                            </center>
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                </div>
            </div>
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                style="background:#fff;background-color:#fff;width:100%;">
                <tbody>
                    <tr>
                        <td

                            style="border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                            <div class="mj-column-per-100 outlook-group-fix"
                                style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">


                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="vertical-align:bottom;" width="100%">

                                    <tr>
                                        <td align="center"
                                            style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <table align="center" border="0" cellpadding="0" cellspacing="0"
                                                role="presentation"
                                                style="border-collapse:collapse;border-spacing:0px; background-color:#033f1f;">
                                                <tbody>
                                                    <tr>


                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center"
                                            style="font-size:0px;padding:10px 25px;padding-bottom:40px;word-break:break-word;">
                                            <div
                                                style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:28px;font-weight:bold;line-height:1;text-align:center;color:#555;">
                                                Chèr(e) Client(e)&nbsp; <?= $utilisateur ?> !!

                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <div
                                                style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;">
                                                <?= $message ?>
                                            </div>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td align="center"
                                            style="font-size:0px;padding:10px 25px;padding-top:30px;padding-bottom:50px;word-break:break-word;">

                                            <table align="center" border="0" cellpadding="0" cellspacing="0"
                                                role="presentation" style="border-collapse:separate;line-height:100%;">
                                                <tr>
                                                    <td align="center" bgcolor="#033f1f" role="presentation"
                                                        style="border:none;border-radius:3px;color:#ffffff;cursor:auto;padding:15px 25px;"
                                                        valign="middle">
                                                        <a href="https://yakoafricassur.com/espace-client/login.php">
                                                            <p
                                                                style="background:#033f1f;color:#ffffff;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;font-weight:normal;line-height:120%;Margin:0;text-decoration:none;text-transform:none;">
                                                                Retour espace client
                                                            </p>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>




                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <div
                                                style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;">
                                                Best regards,<br><br> YAKOAFRICASSUR<br>DSI conception<br>
                                                <a href="https://www.yakoafricassur.com"
                                                    style="color:#2F67F6">yakoafricassur.com</a>
                                            </div>

                                        </td>
                                    </tr>

                                </table>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div style="Margin:0px auto;max-width:600px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                            <div class="mj-column-per-100 outlook-group-fix"
                                style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">

                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:bottom;padding:0;">

                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    width="100%">

                                                    <tr>
                                                        <td align="center"
                                                            style="font-size:0px;padding:0;word-break:break-word;">

                                                            <div
                                                                style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;font-weight:300;line-height:1;text-align:center;color:#575757;">
                                                                ----------
                                                            </div>

                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="center"
                                                            style="font-size:0px;padding:10px;word-break:break-word;">

                                                            <div
                                                                style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;font-weight:300;line-height:1;text-align:center;color:#575757;">
                                                                <a href="https://yakoafricassur.com/desabonnement-ynov" style="color:#575757">Unsubscribe</a> from
                                                                our emails
                                                            </div>

                                                        </td>
                                                    </tr>

                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>

</body>

</html-->