<?php


// function __autoload($class_name){

//     require_once"config/".$class_name.".php";
// }


// function __autoload($class) {
//	 include 'classes/' . $class . '.class.php';
// }

// function my_autoloader($class) {
// 	include 'classes/' . $class . '.class.php';
// }

// spl_autoload_register('my_autoloader');

// Ou, en utilisant une fonction anonyme à partir de PHP 5.3.0

spl_autoload_register(function ($class) {
    include 'class/class.' . $class . '.php';
});


$request = new Request();
$logger = new Logger($request);
$fonction = new Fonction($logger);

$maintenant = date('Y-m-d H:i:s');




function notificationAdminMail($message, $destinataire, $nomDestinataire)
{
    $subject = 'Demande de compte YNOV - ' . $nomDestinataire;
    //$nomDestinataire = "SUPPORT YNOV ";
    //$destinataire = "support.enov@yakoafricassur.com";
    //echo $message;
    notificationMail($subject, $nomDestinataire, $destinataire, $message);
}


function notificationMail($subject, $nomDestinataire, $destinataire, $message)
{
    // Plusieurs destinataires
    //$to  = 'johny@example.com'; // notez la virgule
    $to = $destinataire;

    // Sujet

    // message


    // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';

    // En-têtes additionnels
    //$headers[] = 'Cc: BOGA AHIGBE VANESSA BELARICE <vanessa.boga@yakoafricassur.com>';

    $headers[] = 'To: ' . $nomDestinataire . ' <' . $destinataire . '>';
    $headers[] = 'From: SUPPORT YNOV <no-reply@yakoafricassur.com>';
    //$headers[] = 'Cc: BOGA AHIGBE VANESSA BELARICE <vanessa.boga@yakoafricassur.com> , BENOIT N\'GORAN <benoit.ngoran@yakoafricassur.com>';
    $headers[] = 'Cc: HELP DESK YAKO AFRICA <vanessa.boga@yakoafricassur.com> ';
    // Envoi



    if (mail($to, $subject, $message, implode("\r\n", $headers))) // Envoi du message
    {
        $to_log = __FUNCTION__ . ' | Message de notification a bien été envoyé à ' . $nomDestinataire . " ( " . $destinataire . " ) ";
        print $to_log;
    } else // Non envoyé
    {
        $to_log = __FUNCTION__ . ' | Le message de notification n\'a pas pu être envoyé à ' . $nomDestinataire . " ( " . $destinataire . " ) ";
        print $to_log;
    }
    //logger($to_log, $message, "genererFichierImpayes");
}



function formulaireMail($demandeur, $message)
{

    $form = '
    <!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Mail demande de compte ynov </title>
        <style>
             body{
                margin:0px;
                background-color: azure;
                padding: 15px 0px;
            }
            
           
            .main{
                width: 60%;
                margin-right: auto;
                margin-left: auto;
                background-color: white;
               
            }
            
            .navbarStyle{
                background-color: #033f1f; 
                padding: 15px 0px; 
                color: #777; 
                font-size: 12px;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                
            }
            .footerStyle{
                text-align: center; 
                background-color: #033f1f; 
                padding: 15px 0px; 
                color:white; 
                font-size: 3vw;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
            }
            .card-box{
             
                -webkit-box-shadow: 0 0 28px rgba(0, 0, 0, .08);
                box-shadow: 0 0 28px rgba(0, 0, 0, .08)
            }
            .card-body { padding: 0; }
            .text-center{
                text-align: center;
            }
            .rowStyle{
                display:flex;
                flex-wrap:wrap;
            }
            .col-sm-6{-ms-flex:0 0 50%;flex:0 0 50%;max-width:50%}
            .col-sm-3{-ms-flex:0 0 25%;flex:0 0 50%;max-width:25%}

            .text-left {text-align: left !important; }
            .text-right {text-align: right !important; }
            .p-2 { padding: 0.5rem !important; }
            p {
                margin-top: 0;
                margin-bottom: 1rem;
            }
            h1{
            font-size: 3vw;
            text-align: center;
            color: #033f1f
            }
            
            h2{
            font-size: 3vw;
            text-align: left;
             margin:10px; 
            padding:10px;
            }
            p{
               
                /*padding: 5px;*/
                margin-top: auto;
                padding: 1% ;
            }
            .flex-row-style {
                -ms-flex-direction: row !important;
                -webkit-box-orient: horizontal !important;
                -webkit-box-direction: normal !important;
                flex-direction: row !important; 
            }
            .download{
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    align-items: center;
                }
            a{
                text-decoration: none;
            }
            .body-card{
                    padding: 0 10px;
                }
            @media (max-width:1024px){
                body{
                    padding: 0px;
                }
                .main{
                    width: 100%;
                  
                }
                
                h1{
                    font-size: 2rem;
                }
                h3{
                    font-weight:bold;
                }
                .download{
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="main">
            <div class="navbarStyle" >
                <div class="rowStyle">
                    <div class="col-sm-6">
                        <div class="card-body text-left p-2" style="margin:10px; padding:10px;">
                            <img
                                src="https://yakoafricassur.com/storage/demande-compte/images/mail/logo.png"
                                width="30%"
                                alt=""
                                class="dark-logo"
                            >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body text-right p-2" style="margin:10px; padding:10px;">
                            <img
                                src="https://yakoafricassur.com/storage/demande-compte/images/mail/logoYnovWhite.png"
                                width="30%"
                                alt=""
                                class="dark-logo"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="body-card">
            ' . $message . '
            
            <div class="card-body text-center p-2">

                <div class="download">
                    <span style="font-size:18px;">T&#233;l&#233;charger l\'application YNOV</span>
                    <a href="https://bit.ly/3xdzhcq" title="Télécharger l\' application YNOV" target="_blank">
                        <img src="https://yakoafricassur.com/storage/demande-compte/images/mail/playstore.png" alt="HTML tutorial">
                    </a>
                </div>

                <p class="mb-0 text-center" style="font-size:14px;color:#033f1f">
                    <i>Si vous souhaitez vous desinscrire des services YNOV ,</i>
                    <a     class="text-white"
                        href="https://yakoafricassur.com/desabonnement-ynov"
                        title="Pour vous desabonner de l\' Application YNOV cliquez ici"
                        target="_blank"
                    >
                        <span style="color:#F9B233;font-size:14px;font-weight:bold;">CLIQUEZ ICI</span>
                    </a>
                </p>
            </div>
            </div>
            <div class="footer-wrap pd-20 mb-20 text-center" >
                <div class="footer-wrap pd-20 mb-20 card-box footerStyle" >
                    <div class="row" >
                        <div class="col-lg-3"></div>
                        <div class="col-lg-6">
                            <p class="mb-0 text-center" style="font-size:14px;color:white">
                                <a
                                    href="https://yakoafricassur.com/conditions-generales-utilisation-ynov.html"
                                    title="Politique de confidentialite de l\'application YNOV"
                                    target="_blank"
                                    style="color: white;"
                                >Politique de confidentialit&#233; de l \'application YNOV</a>
                            </p>
                            <p class="mb-0 text-center" style="font-size:14px;color:white">
                                <a
                                    href="https://yakoafricassur.com/conditions-generales-utilisation-ynov.html"
                                    title="Conditions générales utilisation Application Ynov"
                                    target="_blank"
                                    style="color: white;"
                                >Conditions g&#233;n&#233;rales utilisation Application YNOV</a>
                            </p>
                            <p class="mb-0 text-center" style="font-size:14px;">
                                Copyright © 2022
                                <a
                                    class="text-white"
                                    href="https://yakoafricassur.com/"
                                    target="_blank"
                                    title="YAKO AFRICA ASSURANCES VIE"
                                >
                                    <span style="color:#F9B233;">YAKO AFRICA ASSURANCES VIE</span>
                                </a>
                                ,Tous droits reserv&#233;s
                            </p>
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

';
    //echo htmlentities($form);
    //@file_put_contents(__DIR__ . "/log/envoi_mail.html", $form);
    return $form;
}
