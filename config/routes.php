<?php

session_start();

include("../autoload.php");
/*$fonction = new  fonction();
$dbAcces = new dbAcess();
*/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];

$maintenant =  @date('Y-m-d H:i:s');
$lienEnvoiMail = "$url/notification-mail.php?";


if ($request->action != null) {
    switch ($request->action) {
        case "connexion":

            $passW = GetParameter::FromArray($_REQUEST, 'passW');
            $login = GetParameter::FromArray($_REQUEST, 'login');

            if ($passW != null && $login != null) {
                $password = md5($passW);
                $plus = " AND login = '$login' AND password='$password'  ";
                $retourUsers = $fonction->_GetUsers($plus);
                if ($retourUsers != NULL) {
                    $_SESSION["id"] = $retourUsers->id;
                    $_SESSION["typeCompte"] = $retourUsers->typeCompte;
                    $_SESSION["paramCompte"] = $retourUsers->paramCompte;
                    $_SESSION["utilisateur"] = $retourUsers->userConnect;
                    $_SESSION["profil"] = $retourUsers->profil;
                    $_SESSION["cible"] = $retourUsers->cible;
                    $_SESSION["codeagent"] = $retourUsers->codeagent;
                    $_SESSION["infos"] = $retourUsers->infos;

                    echo json_encode($retourUsers->paramCompte);
                } else echo json_encode("-1");
            } else echo json_encode("-1");

            break;

        case 'motdepasseOublie':

            $loginPO = GetParameter::FromArray($_REQUEST, 'loginPO');
            $email = GetParameter::FromArray($_REQUEST, 'email');

            if (isset($loginPO) && $loginPO != null) {
                $plus = " AND login = '$loginPO'  ";
                $retourUsers = $fonction->_GetUsers($plus);
                if ($retourUsers != null) {
                    echo json_encode($retourUsers);
                } else {
                    echo json_encode("-1");
                }
            } else {
                echo json_encode("-1");
            }

            break;

        case "intro":

            $type = GetParameter::FromArray($_REQUEST, 'type');
            //print_r($_REQUEST);

            if ($type == Config::TYPE_SERVICE_PRESTATION) {
                $retourStatut = $fonction->_recapGlobalePrestations();
                $global = $fonction->pourcentageAllTypePrestation();

                $result = array(
                    "retourStatut" => $retourStatut,
                    "global" => $global
                );
                echo json_encode($result);
            } elseif ($type == Config::TYPE_SERVICE_RDV) {

                $retourStatut = $fonction->pourcentageRDVBy("statut");
                $retourStatutVille = $fonction->pourcentageRDVBy("ville");
                $retourStatutuser = $fonction->pourcentageRDVBy("user");
                $retourStatutType = $fonction->pourcentageRDVBy("type");
                $result = array(
                    "retourStatut" => $retourStatut,
                    "retourStatVille" => $retourStatutVille,
                    "retourStatuser" => $retourStatutuser,
                    "retourStatutType" => $retourStatutType
                );
                echo json_encode($result);
                //$global = $fonction->pourcentageAllTypeRDV();
            } else {


                echo json_encode("-1");
            }


            break;




        case "modifierPasse":

            $id = GetParameter::FromArray($_REQUEST, 'idusers');
            $pass2 = GetParameter::FromArray($_REQUEST, 'pass2');
            $pass1 = GetParameter::FromArray($_REQUEST, 'pass1');

            $plus = " AND id = '$id'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                $result = $fonction->_UpdateMotDePasse($retourUsers, $pass1);
                echo json_encode($result);
            } else {
                echo json_encode("-1");
            }

            break;


        case "passeOublie":

            $emailPro = GetParameter::FromArray($_REQUEST, 'emailPro');

            $plus = " AND `login` = '$emailPro'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                if (strlen($retourUsers->password) > 10) {

                    if (isset($retourUsers->telephone) && $retourUsers->telephone != "") $newpasse =  substr($retourUsers->telephone, -8);
                    else $newpasse = "1234567";

                    $result = $fonction->_UpdateMotDePasse($retourUsers, $newpasse);
                } else {
                    $newpasse = $retourUsers->password;
                }

                echo json_encode($retourUsers->userConnect);
                //$url_notification = "http://localhost/mes-projets/yako-africa/admin-prestation/notification-mail.php?action=passeOublie&id=" . trim($retourUsers->id);
                //file_get_contents($url_notification);
            } else {
                echo json_encode("-1");
            }
            break;


        case "checkUsers":
            $idusers = GetParameter::FromArray($_REQUEST, 'idusers');
            $plus = " AND `id` = '$idusers'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {
                echo json_encode($retourUsers);
            } else {
                echo json_encode("-1");
            }
            break;


        case "ModifierMesInfos":

            $idusers = GetParameter::FromArray($_REQUEST, 'idusers');
            $nom = GetParameter::FromArray($_REQUEST, 'nom');
            $prenoms = GetParameter::FromArray($_REQUEST, 'prenoms');
            $telephone = GetParameter::FromArray($_REQUEST, 'telephone');
            $email = GetParameter::FromArray($_REQUEST, 'email');
            $mobile2 = GetParameter::FromArray($_REQUEST, 'mobile2');

            $plus = " AND `id` = '$idusers'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                $result = $fonction->_UpdateInformationUsers($retourUsers, $nom, $prenoms, $telephone, $email, $mobile2);
                echo json_encode($result);
            } else {
                echo json_encode("-1");
            }
            break;

        case "tableauSuivi":
            $service = GetParameter::FromArray($_REQUEST, 'service');
            $filtreuse = GetParameter::FromArray($_REQUEST, 'filtreuse');

            //print_r($_REQUEST);
            if ($service != null) {

                $paramCompte = $_SESSION['paramCompte'];
                list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

                if ($service == "rdv") {

                    if ($profil == "gestionnaire" || $profil == "agent") {
                        $filtreuse  = " WHERE tblrdv.gestionnaire = '$usersid' ";
                    }

                    $sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $filtreuse ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
                    $tableauSuivi = $fonction->_getSelectDatabases($sqlSelect);
                } else if ($service == "prestation") {

                    $sqlSelectAdminstratif = "SELECT * FROM " . Config::TABLE_PRESTATION . " $filtreuse AND prestationlibelle = 'Autre'  ";
                    $tableauSuiviAdminstratif = $fonction->_getSelectDatabases($sqlSelectAdminstratif);

                    $sqlSelectNonAdminstratif = "SELECT * FROM " . Config::TABLE_PRESTATION . " $filtreuse AND prestationlibelle != 'Autre' ";
                    $tableauSuiviNonAdminstratif = $fonction->_getSelectDatabases($sqlSelectNonAdminstratif);

                    $sqlSelectPrestationRDV = "SELECT tbl_prestations.* , tblrdv.motifrdv FROM `tbl_prestations` INNER JOIN tblrdv  WHERE YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE())   AND tblrdv.idCourrier = tbl_prestations.id AND tbl_prestations.etape !='-1';";
                    $tableauSuiviPrestationRDV = $fonction->_getSelectDatabases($sqlSelectPrestationRDV);


                    $sqlSelectTotal = Config::SqlSelect_ListPrestations . " $filtreuse   ORDER BY id DESC  ";
                    $tableauSuiviTotal = $fonction->_getSelectDatabases($sqlSelectTotal);

                    $tableauSuivi = array(
                        "tableauSuiviAdminstratif" => $tableauSuiviAdminstratif,
                        "tableauSuiviNonAdminstratif" => $tableauSuiviNonAdminstratif,
                        "tableauSuiviPrestationRDV" => $tableauSuiviPrestationRDV,
                        "tableauSuivi" => $tableauSuiviTotal
                    );
                } else if ($service == "sinistre") {
                    $tableauSuivi = "";
                } else {
                    $tableauSuivi = "";
                }
                echo json_encode($tableauSuivi);
            } else echo json_encode("-1");
            break;
        case "afficherGestionnaire":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $sqlQuery = "SELECT users.id , users.email , users.codeagent ,  TRIM(CONCAT(users.nom ,' ', users.prenom)) as gestionnairenom ,tblvillebureau.libelleVilleBureau as villeEffect FROM users INNER JOIN tblvillebureau ON tblvillebureau.idVilleBureau = users.ville WHERE  users.etat='1' AND tblvillebureau.idVilleBureau='$idVilleEff'  ORDER BY users.id ASC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;
        case "receptionJourRdv":
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $retourJourReception = $fonction->getRetourJourReception($idVilleEff);
            $tablo = [];
            if ($retourJourReception != null) {
                foreach ($retourJourReception as $key => $value) {
                    $tablo[$key] = (int)$value->codejour;
                }
            }
            echo json_encode($tablo);
            break;
        case "compteurRdv":

            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdv = GetParameter::FromArray($_REQUEST, 'daterdv');
            $parms = GetParameter::FromArray($_REQUEST, 'parms');
            $retourJourReception = $fonction->getRetourJourReception($idVilleEff);


            // if ($parms == '1') {
            //     $daterdv = $daterdveff;
            //     list($annee, $mois, $jour) = explode('-', $daterdveff, 3);
            //     $daterdv = trim($jour . '/' . $mois . '/' . $annee);/**/
            // } else {
            //     /**/
            //     list($jour, $mois, $annee) = explode('/', $daterdv, 3);
            //     $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
            //     $daterdv_affiche = date_format($daterdv, "d/m/Y");
            //     $daterdv = date_format($daterdv, "Y-m-d");
            // }
            // list($jour, $mois, $annee) = explode('-', $daterdveff, 3);
            // $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
            // $daterdv_affiche = date_format($daterdv, "d/m/Y");
            // $daterdv = date_format($daterdv, "Y-m-d");
            $daterdv = $daterdveff;

            // // ✅ Récupération du jour de la semaine en français
            // setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
            // $timestamp = strtotime($daterdv);


            // // Affichage (pour test)
            // echo "Date : $daterdv<br>";

            $dateAfficher = date('d/m/Y', strtotime($daterdv));
            $sqlQuery = "SELECT  COUNT(*) AS totalrdv, tblrdv.villeEffective,  tblrdv.daterdveff,  MIN(tblrdv.daterdv) AS daterdv_min FROM tblrdv WHERE tblrdv.villeEffective = '$idVilleEff'   AND DATE(tblrdv.daterdveff) = '" . $daterdv . "' GROUP BY tblrdv.villeEffective, tblrdv.daterdveff ORDER BY totalrdv DESC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => count($resultat), "data" => $resultat[0], "retourJourReception" => $retourJourReception);
            } else {
                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => "0", "data" => null, "retourJourReception" => $retourJourReception);
            }
            echo json_encode($retour);

            break;
        case "tableauSuiviConfirmation":

            $_REQUEST["etat"] = null;
            $retourPlus = $fonction->getFiltreuseRDV();
            $filtre = $retourPlus["filtre"];
            $libelle = $retourPlus["libelle"];

            $filtreuse = "";
            if ($filtre) {
                list(, $conditions) = explode('AND', $filtre, 2);
                $filtreuse = " WHERE $conditions ";
            }

            // $paramCompte = $_SESSION['paramCompte'];
            // list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

            // if ($profil == "gestionnaire" || $profil == "agent") {
            //     $filtreuse  = " WHERE tblrdv.gestionnaire = '$usersid' ";
            // }

            $sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $filtreuse ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
            $tableauSuivi = $fonction->_getSelectDatabases($sqlSelect);

            echo json_encode($tableauSuivi);

            break;
        case "rechercherRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            //$sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $sqlSelect = "SELECT tblrdv.*,	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire, TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id LEFT JOIN tblvillebureau 	ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE tblrdv.idrdv= '" . trim($idrdv) . "'  ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                echo json_encode($retour[0]);
            } else echo json_encode("-1");
            break;


        case "priseRdvExceptionnel":

            $idContrat = GetParameter::FromArray($_REQUEST, 'idContrat');
            $telephone = GetParameter::FromArray($_REQUEST, 'telephone');
            $email = GetParameter::FromArray($_REQUEST, 'email');
            $statutDemandeur = GetParameter::FromArray($_REQUEST, 'statutDemandeur');
            $typePrestation = GetParameter::FromArray($_REQUEST, 'typePrestation');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $villesRDV = GetParameter::FromArray($_REQUEST, 'villesRDV');
            $ListeGest = GetParameter::FromArray($_REQUEST, 'ListeGest');
            $nomclient = GetParameter::FromArray($_REQUEST, 'nomclient');
            $datenaissance = GetParameter::FromArray($_REQUEST, 'datenaissance');
            $lieuResidence = GetParameter::FromArray($_REQUEST, 'lieuResidence');

            $sqlSelect = "SELECT * FROM tblrdv WHERE police='$idContrat' AND etat !='1' ORDER BY idrdv DESC LIMIT 1 ";
            $resultat = $fonction->_getSelectDatabases($sqlSelect);
            if ($resultat != null) {
                $retour = $fonction->priseRdvExceptionnel($idContrat, $telephone, $email, $statutDemandeur, $typePrestation, $daterdveff, $villesRDV, $ListeGest, $nomclient, $datenaissance, $lieuResidence);
                $idrdv =  $retour["LastInsertId"];
                notificationRDV_gestionnaireByNissa($daterdveff, $ListeGest, $telephone, $idrdv, $url);
                echo json_encode($idrdv);
                /*
                list($idGestionnaire, $gestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode("|", $ListeGest, 4);
                $sqlQuery2 = "SELECT id , email , codeagent , telephone,  TRIM(CONCAT(nom ,' ', prenom)) as gestionnairenom FROM users WHERE  id='" . $idGestionnaire . "' ";
                $result2 = $fonction->_getSelectDatabases($sqlQuery2);
                if ($result2 != NULL) {
                    $retourGestionnaire = $result2[0];

                    $dateeffective = date('d/m/Y', strtotime($daterdveff));
                    $telGestionnaire = $retourGestionnaire->telephone;
                    $emailGestionnaire = $retourGestionnaire->email;
                    $nomGestionnaire = $retourGestionnaire->gestionnairenom;
                    $codeagent = $retourGestionnaire->codeagent;

                    $retour_agent = $fonction->getRetourneContactInfosGestionnaire($codeagent);

                    if (isset($retour_agent["telephone"]) && !empty($retour_agent["telephone"])) {
                        $message = "Cher(e) client(e), suite à votre demande de rendez-vous, un conseiller vous recevra le " . $dateeffective . "." . PHP_EOL . "Pour plus d' information , veuillez contacter le " . $retour_agent["telephone"] . ".";
                    } else {
                        $message = "Votre RDV est prévu le $dateeffective à $villesGestionnaire. Un conseiller client vous recevra. Pour plus d'informations, Consultez votre espace client: urlr.me/9ZXGSr . ";
                    }

                    $numero = "225" . substr($telephone, -10);
                    $ref_sms = "RDV-" . $idrdv;

                    $sms_envoi = new SMSService();
                    if (strlen($message) > 160) $message = substr($message, 0, 160);
                    $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                    $sqlUpdateRdvUpdate = "UPDATE tblrdv SET etatSms =?  WHERE idrdv = ?";
                    $queryOptions = array(
                        '1',
                        intval($idrdv)
                    );
                    $result = $fonction->_Database->Update($sqlUpdateRdvUpdate, $queryOptions);

                    if (isset($retour_agent["email_final"]) && !empty($retour_agent["email_final"])) {

                        //envoi mail au gestionnaire 
                        $lienEnvoiMail = "$url/envoiMail-rdv.php?";
                        $url_notification = $lienEnvoiMail . "action=transmettreRDV&data=[idrdv:" . trim($idrdv) . "]";
                        $retour = file_get_contents($url_notification);
                    }

                    echo json_encode($idrdv);
                }
                */
            } else {
                echo json_encode("-1");
            }

            break;
        case "transmettreRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'gestionnaire');
            $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            $dateRDVEff = GetParameter::FromArray($_REQUEST, 'daterdveff');

            if ($idrdv != null  && $gestionnaire != null && $objetRDV != null && $dateRDVEff != null) {
                $datetraitement = date('d/m/Y à H:i:s');
                $etat = "2";
                $traiterpar = $_SESSION["id"];
                $reponse = "";

                list($idVilleEff, $VilleEff) = explode(';', $objetRDV, 2);
                list($idgestionnaire, $nomgestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode('|', $gestionnaire, 4);

                $sqlSelect = "SELECT *  FROM tblrdv WHERE idrdv = '" . $idrdv . "' ";
                $retour = $fonction->_getSelectDatabases($sqlSelect);
                if ($retour != null) {
                    $rdv = $retour[0];
                    $result = $fonction->_TransmettreRDVGestionnaire($etat, $reponse, $dateRDVEff, $datetraitement, $idgestionnaire, $idrdv, $idVilleEff, $traiterpar);
                    notificationRDV_gestionnaireByNissa($dateRDVEff, $gestionnaire, $rdv->tel, $idrdv, $url);
                }
                echo json_encode($idrdv);
            } else {
                echo json_encode("-1");
            }

            break;
        case "confirmerReceptionRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $idgestionnaire = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idvilleEff');
            $dateRDVEff = GetParameter::FromArray($_REQUEST, 'daterdvEff');


            if ($idrdv != null && $idgestionnaire != null && $dateRDVEff != null && $idVilleEff != null) {
                $datetraitement = date('d/m/Y à H:i:s');
                $reponse = $observation;
                $etat = "2";
                $traiterpar = $idgestionnaire;
                $result = $fonction->_TransmettreRDVGestionnaire($etat, $reponse, $dateRDVEff, $datetraitement, $idgestionnaire, $idrdv, $idVilleEff, $traiterpar);
                if ($result) {
                    $sqlSelect = " SELECT tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '$idrdv' ";
                    $retour = $fonction->_getSelectDatabases($sqlSelect);
                    if ($retour != null) {
                        $rdv = $retour[0];
                        $villesGestionnaire = $rdv->villes;
                        $telephone = $rdv->tel;

                        $emailgestionnaire = $rdv->emailgestionnaire;
                        if (isset($rdv->daterdveff) && !empty($rdv->daterdveff)) {

                            $dateeffective = date('d/m/Y', strtotime($rdv->daterdveff));
                            $message = "Votre RDV est prévu le $dateeffective à $villesGestionnaire. Un conseiller client vous recevra. Pour plus d'informations, Consultez votre espace client: urlr.me/9ZXGSr . ";
                            envoyerSMS_RDV($telephone, $message, $idrdv);
                        }

                        if (isset($emailgestionnaire) && !empty($emailgestionnaire)) {

                            //envoi mail au gestionnaire 
                            $lienEnvoiMail = "$url/envoiMail-rdv.php?";
                            $url_notification = $lienEnvoiMail . "action=transmettreRDV&data=[idrdv:" . trim($idrdv) . "]";
                        }

                        echo json_encode($idrdv);
                    } else {
                        echo json_encode("-1");
                    }
                } else {
                    echo json_encode("-1");
                }
            } else {
                echo json_encode("-1");
            }

            break;
        case "modifierRDVByGestionnaire":


            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $motifmodif = GetParameter::FromArray($_REQUEST, 'motifmodif');
            $telGestionnaire = "";

            //$sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $sqlSelect = " SELECT tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '$idrdv' ";

            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];

                $retourGestionnaire = $fonction->getRetourneContactInfosGestionnaire($rdv->codeagentgestionnaire);
                $telGestionnaire = $retourGestionnaire["telephone"];


                $sqlUpdate = "UPDATE tblrdv SET daterdveff = ? , daterdv=? , etat='2' , etatTraitement='' , libelleTraitement='' , estPermit=null , reponseGest=? , traiterLe=now() , updatedAt=now()  WHERE idrdv = ? ";
                $queryOptions = array(
                    $daterdveff,
                    $daterdveff,
                    "La date du RDV prevu le " . date("d/m/Y", strtotime($rdv->daterdveff)) . "  .  a ete modifier par le gestionnaire " . $rdv->nomgestionnaire . " pour la ville de " . $rdv->villes . " le " . $daterdveff . ".  Motif de modification : " . $motifmodif,
                    $idrdv
                );
                $result = $fonction->_Database->Update($sqlUpdate, $queryOptions);
                if ($result != null) {
                    $message = "Votre RDV prévu le " . date("d/m/Y", strtotime($rdv->daterdveff)) . " a ete modifier pour le " . date("d/m/Y", strtotime($daterdveff)) . " à " . $rdv->villes . ". Merci de contacter le " . $telGestionnaire . " pour toute information complémentaire.";
                    envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);

                    echo json_encode($idrdv);
                } else echo json_encode("-1");
            } else {
                echo json_encode("-1");
            }

            break;
        case "operationRDVReception":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'gestionnaire');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $resultatOpe = GetParameter::FromArray($_REQUEST, 'resultatOpe');
            $observation = GetParameter::FromArray($_REQUEST, 'obervation');
            $optionadditif = GetParameter::FromArray($_REQUEST, 'optionadditif');

            $traiterpar = $_SESSION["id"];

            $sqlSelect = " SELECT tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				  users.typeCompte AS typeCompteGestionnaire,  TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '$idrdv' ";


            $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour_rdv != null) {
                $rdv = $retour_rdv[0];

                $telGestionnaire = "2720259082";
                if ($rdv->typeCompteGestionnaire == "gestionnaire") {
                    $retourGestionnaire = $fonction->getRetourneContactInfosGestionnaire($rdv->codeagentgestionnaire);
                    $telGestionnaire = $retourGestionnaire["telephone"];
                    if ($telGestionnaire == null) $telGestionnaire = "2720259082";
                }

                $tablo_issue_rdv = explode("|", $optionadditif);
                $resultatOpe = $tablo_issue_rdv[0];
                $tabloOperation = Config::tablo_resultat_entretien_rdv[$resultatOpe];

                $etatTraitement = $tabloOperation["etat"];
                $libelleTraitement = $tabloOperation["libelle"];
                $operation = $tabloOperation["operation"];
                $resultat = $tabloOperation["resultat"];
                $permission = $tabloOperation["permission"];
                $retour = "";

                switch (strtolower($resultatOpe)) {

                    case "partielle":
                    case "avance":
                        // Cas partielle ou avance 
                        $optionOperation = "";
                        $motantOperation = "";
                        if (!empty($optionadditif)) {

                            list($optionOperation, $motantOperation) = explode("|", $optionadditif, 2);
                            $observation = "operation : " . $optionOperation . " ,  valeur : " . $motantOperation;
                        }

                        $retour = optionAvancePartielleApresReceptionRDVbyNissa($rdv, $optionOperation, $motantOperation, $observation, $tabloOperation, $traiterpar);
                        echo json_encode($retour);
                        break;

                    case "transformation":
                        // Cas transformation 

                        if (!empty($optionadditif)) {
                            $tablo = explode("|", $optionadditif);
                            list($optionOperation, $produittransformation, $montanttransformation, $motantclient) = explode("|", $optionadditif, 4);
                            $observation = "produit : " . $produittransformation . " ,  montant à transformé : " . $montanttransformation . " ,  montant du client : " . $motantclient;
                        } else {
                            $optionOperation = "";
                            $produittransformation = "";
                            $montanttransformation = "";
                            $motantclient = "";
                            $observation = "";
                        }

                        issueApresReceptionRDV($rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $traiterpar);

                        $message = "Cher client(e), votre demande de " . strtoupper($resultatOpe) . " du " . date('d/m/Y', strtotime($rdv->daterdveff)) . " a bien été autorisée. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
                        envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);

                        echo json_encode($rdv->idrdv);

                        break;
                    case "absent":

                        if (!empty($optionadditif)) {
                            $retour =  optionAbscenceApresReceptionRDVbyNISSA($optionadditif, $daterdveff, $rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $telGestionnaire, $traiterpar);
                            echo json_encode($retour);
                        } else {
                            echo json_encode("-1");
                        }

                        break;
                    default:
                        // Traitement classique
                        issueApresReceptionRDV($rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $traiterpar);

                        if ($resultatOpe == "renonce" || $resultatOpe == "conserver") {
                            $message = "Cher(e) client(e), votre demande de conservation du contrat n° " . strtoupper($rdv->police) . " a bien été enregistrée. Plus d’infos sur votre espace client : https://urlr.me/9ZXGSr";
                        } else {
                            $message = "Cher client(e), après analyse, votre demande de " . strtoupper($rdv->motifrdv) . " du " . $rdv->daterdv . " n'a pas abouti. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
                        }
                        envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);
                        echo json_encode($rdv->idrdv);
                        break;
                }
            }

            break;

        case "confirmerPermissionDepotRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $motif = GetParameter::FromArray($_REQUEST, 'motif');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');
            $motantOperation = GetParameter::FromArray($_REQUEST, 'motantOperation');

            $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $sqlSelect = " SELECT tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '$idrdv' ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];

                $idmotif = "";
                $etatTraitement = "1";
                $libelleTraitement = "Le client a la permission de faire une demande de " . $rdv->motifrdv;

                $result_typeprestation = $fonction->getRetourneTypePrestation(" AND LOWER(libelle) like '%" . strtolower($rdv->motifrdv) . "%' ");
                if ($result_typeprestation != null) {
                    $typeprestation = $result_typeprestation[0]->libelle;
                } else {
                    $typeprestation = $rdv->motifrdv;
                }

                $sqlInsertPrestation = "INSERT INTO tbl_prestations(idcontrat,typeprestation,prestationlibelle,montantSouhaite,etape,estMigree,created_at) VALUES (?,?,?,?,?,?, NOW() )";
                $queryOptionsPrestations = array(
                    $rdv->police,
                    $typeprestation,
                    $typeprestation,
                    $motantOperation,
                    '-1',
                    '0'
                );
                $rrr = $fonction->_Database->Update($sqlInsertPrestation, $queryOptionsPrestations);
                $idprestation = $rrr["LastInsertId"];

                $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement= ?, libelleTraitement=?, reponseGest=?, datetraitement=NOW(), traiterLe=NOW(), gestionnaire=?, updatedAt =NOW() , etatSms =? , idCourrier=? , estPermit=? WHERE idrdv = ?";
                $queryOptions = array(
                    "3",
                    $etatTraitement,
                    $libelleTraitement,
                    addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
                    $rdv->gestionnaire,
                    '1',
                    intval($idprestation),
                    '1',
                    intval($rdv->idrdv)
                );

                $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                $message = "Cher client(e), votre demande de " . strtoupper($typeprestation) . " n° " . $rdv->idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";

                envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);

                $lienEnvoiMail = "$url/envoiMail-rdv.php?";
                $url_notification = $lienEnvoiMail . "action=permissionDepotRDV&data=[idrdv:" . trim($idrdv) . "]";
                $retour = file_get_contents($url_notification);

                echo json_encode($rdv->idrdv);
            } else echo json_encode("-1");

            break;
        case "rejeterRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $motif = GetParameter::FromArray($_REQUEST, 'motif');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');

            $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];
                $idmotif = "";

                $sqlUpdatePrestation = "UPDATE tblrdv SET etat= ?, reponse=?, datetraitement=now(), gestionnaire=?,  traiterLe=now() , updatedAt=now(), etatSms =? WHERE idrdv = ?";
                $queryOptions = array(
                    '0',
                    addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
                    $gestionnaire,
                    '1',
                    intval($idrdv)
                );
                $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                if ($result != null) {
                    $retour = $idrdv;
                    $message = "Cher client(e), votre demande de rdv n° " . $rdv->codedmd . "  du " . date('d/m/Y', strtotime($rdv->daterdv)) . " a été rejetée." . PHP_EOL . "Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                    envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);
                }
                echo json_encode($idrdv);
            } else echo json_encode("-1");
            break;
        case "ListCompteurGestionnaireByNISSA":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $idgestionnaire = GetParameter::FromArray($_REQUEST, 'idusers');

            $totalrdv = 0;
            $sqlQuery = "SELECT 
                            u.id,  u.email,  u.codeagent,  TRIM(CONCAT(u.nom, ' ', u.prenom)) AS gestionnairenom,
                            v.libelleVilleBureau AS villeEffect,    COUNT(r.idrdv) AS totalrdv FROM users u
                        INNER JOIN tblvillebureau v ON v.idVilleBureau = u.ville LEFT JOIN tblrdv r 
                            ON r.gestionnaire = u.id     AND DATE(r.daterdveff) = '$daterdveff'      AND r.villeEffective = '$idVilleEff'
                        WHERE     u.etat = '1'   AND v.idVilleBureau = '$idVilleEff' GROUP BY u.id, u.email, u.codeagent, u.nom, u.prenom, v.libelleVilleBureau ORDER BY u.id ASC ";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "extraireBordereau":

            $rdvLe = GetParameter::FromArray($_REQUEST, 'rdvLe');
            $rdvAu = GetParameter::FromArray($_REQUEST, 'rdvAu');
            $ListeGest = GetParameter::FromArray($_REQUEST, 'ListeGest');
            $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            $_REQUEST['etat'] = null;
            // Periode
            if (!empty($rdvLe) && !empty($rdvAu)) {
                $periode = date('d/m/Y', strtotime($rdvLe)) . " - " . date('d/m/Y', strtotime($rdvAu));
                $lib_periode = date('Ymd', strtotime($rdvLe)) . " - " . date('Ymd', strtotime($rdvAu));
            } elseif (!empty($rdvLe)) {
                $periode = date('d/m/Y', strtotime($rdvLe));
                $lib_periode = date('Ymd', strtotime($rdvLe));
            } elseif (!empty($rdvAu)) {
                $periode = date('d/m/Y', strtotime($rdvAu));
                $lib_periode = date('Ymd', strtotime($rdvAu));
            }
            // Gestionnaire
            if (!empty($ListeGest)) {
                [$idGest, $nomGest, $idVilleGest, $VilleGest] = explode('|', $ListeGest, 4);
            }

            // VilleRDV
            if (!empty($objetRDV)) {
                [$idVille, $villesRDV] = explode(';', $objetRDV, 2);
            }

            // Application du filtre
            $plus = "";
            $retourPlus = $fonction->getFiltreuseRDV();
            $filtre     = $retourPlus["filtre"] ?? "";
            $libelle    = $retourPlus["libelle"] ?? "";

            if (!empty($filtre)) {
                [, $conditions] = explode('AND', $filtre, 2);
                $plus = " WHERE $conditions  AND tblrdv.etat = '2'  ";
            }

            $sqlSelect = " SELECT tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire, users.id AS idgestionnaire,
            	    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id
                    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau $plus  ORDER BY idgestionnaire DESC ";
            $resultat = $fonction->_getSelectDatabases($sqlSelect);
            if ($resultat != null) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }

            break;
        case "importBordereau":

            //print_r($_REQUEST);

            $dataATraiter = GetParameter::FromArray($_REQUEST, 'params');
            $data = json_decode($dataATraiter, true);
            if (!is_array($data)) {
                http_response_code(400);
                //echo json_encode(["error" => "Données JSON invalides"]);
                $response = ["success" => false, "error" => "Données JSON invalides"];
                echo json_encode($response);
            } else {

                $inserted = 0;
                $ref = date('Ymd');
                // generer le numero de bordereau unique
                $reference = uniqid('BRD-' . $ref . '-', false);

                for ($i = 0; $i <= count($data) - 1; $i++) {
                    if ($i == 0) continue;

                    $ligneBordereau = new BordereauRDV($data[$i]);

                    //print_r($ligneBordereau);

                    if (isset($ligneBordereau->NumeroRdv) && $ligneBordereau->NumeroRdv != null) {

                        $sqlQuery = "SELECT * FROM tblrdv WHERE idrdv = '" . $ligneBordereau->NumeroRdv . "' ORDER BY idrdv ";
                        $result_rdv = $fonction->_getSelectDatabases($sqlQuery);
                        if ($result_rdv != null) {

                            $inserted = $inserted + 1;
                            $rdv = $result_rdv[0];
                            $auteur = $_SESSION["utilisateur"];
                            $id_users = $_SESSION["id"];
                            _insertBordereauRDV($ligneBordereau, $rdv, $reference, $id_users, $auteur);
                        }
                    }
                }

                $response = ["success" => true, "inserted" => $inserted, "total" => count($data), "reference" => $reference];
                echo json_encode($response);
            }

            break;
        default:
            echo json_encode("0");
            break;
    }
}



function traitementApresReceptionRDVAutres($rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $etat = '3')
{
    global $fonction, $maintenant, $lienEnvoiMail;

    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement=?, libelleTraitement=?, reponseGest=?, datetraitement=?, gestionnaire=?,  traiterLe=now() , updatedAt=now(), etatSms =? WHERE idrdv = ?";
    $queryOptions = array(
        $etat,
        $etatTraitement,
        $libelleTraitement,
        addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
        $maintenant,
        $rdv->gestionnaire,
        '1',
        intval($rdv->idrdv)
    );

    $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
    if ($result != null) {
        $retour = $rdv->idrdv;
        //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));

        if ($etatTraitement == "3") {
            $message = "Cher client(e), après analyse, votre demande de " . strtoupper($rdv->motifrdv) . " du " . $rdv->daterdv . " n'a pas abouti. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
        } else {
            if ($resultatOpe == "transformation") {
                $message = "Cher client(e), votre demande de " . strtoupper($resultatOpe) . " du " . $rdv->daterdv . " a bien été autorisée. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
            } else {
                $message = "Cher client(e), votre demande de " . strtoupper($rdv->motifrdv) . " n° " . $rdv->idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";
            }
        }
        envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);
    } else $retour = 0;
    return $retour;
}


function issueApresReceptionRDV($rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $traiterpar, $etat = '3')
{
    global $fonction, $maintenant, $lienEnvoiMail;

    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement=?, libelleTraitement=?, reponseGest=?, datetraitement=now(), gestionnaire=?,  traiterLe=now() , updatedAt=now(), etatSms =? WHERE idrdv = ?";
    $queryOptions = array(
        $etat,
        $etatTraitement,
        $libelleTraitement,
        addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
        $traiterpar,
        '1',
        intval($rdv->idrdv)
    );

    $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
    return $rdv->idrdv;
}


function optionAbscenceApresReceptionRDVbyNISSA($optionadditif, $daterdveff, $rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $telGestionnaire, $traiterpar, $etat = '3')
{

    global $fonction, $maintenant, $lienEnvoiMail;

    $tablo_optionadditif = explode("|", $optionadditif);
    if (count($tablo_optionadditif) > 1) {
        $optionAbscent = $tablo_optionadditif[1];
        if ($optionAbscent == "reprogrammer") {

            //print "rdv a reprogrammer pour le " . $dateNewRDV;
            $dateNewRDV = $tablo_optionadditif[2];

            $sqlUpdate = "UPDATE tblrdv SET daterdveff = ? , etat=? , etatTraitement=? , libelleTraitement=? , gestionnaire=? , estPermit=null , reponseGest=? , datetraitement=now(), traiterLe=now() , updatedAt=now()  WHERE idrdv = ? ";
            $queryOptions = array(
                $daterdveff,
                "2",
                $etatTraitement,
                $libelleTraitement,
                $traiterpar,
                "La date du RDV prevu le " . date("d/m/Y", strtotime($rdv->daterdveff)) . "  .  a ete modifier par le gestionnaire " . $rdv->nomgestionnaire . " pour la ville de " . $rdv->villes . " le " . date("d/m/Y", strtotime($dateNewRDV)),
                $rdv->idrdv
            );
            $result = $fonction->_Database->Update($sqlUpdate, $queryOptions);
            $message = "Votre RDV du " . date("d/m/Y", strtotime($rdv->daterdveff)) . " a été modifié au " . date("d/m/Y", strtotime($dateNewRDV)) . " à " . $rdv->villes . ". Merci de contacter le : " . $telGestionnaire . ".";
        } elseif ($optionAbscent == "annuler") {
            if (empty($observation)) $observation = "RDV " . $optionAbscent . " car " . $libelleTraitement;
            issueApresReceptionRDV($rdv, $etatTraitement, $libelleTraitement, $observation, $resultatOpe, $traiterpar, '0');
            $message = "Votre RDV n° " . $rdv->codedmd . " du " . $rdv->daterdv . " a été annulé(e) pour absence. Détails : https://urlr.me/9ZXGSr";
        } else {
            $message = "Cher client(e), merci de contacter le " . $telGestionnaire . " pour plus d'informations sur votre RDV du " . date("d/m/Y", strtotime($rdv->daterdveff)) . " .";
        }

        envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);

        return $rdv->idrdv;
    }
    return null;
}

function optionAvancePartielleApresReceptionRDVbyNissa($rdv, $optionOperation, $motantOperation, $observation, $tabloOperation, $traiterpar)
{
    global $fonction;


    $etatTraitement = $tabloOperation["etat"];
    $libelleTraitement = $tabloOperation["libelle"];
    $operation = $tabloOperation["operation"];
    $resultat = $tabloOperation["resultat"];
    $permission = $tabloOperation["permission"];

    $result_typeprestation = $fonction->getRetourneTypePrestation(" AND LOWER(libelle) like '%" . strtolower($operation) . "%' ");

    if ($result_typeprestation != null) {
        $typeprestation = $result_typeprestation[0]->libelle;
    } else {
        $typeprestation = $rdv->motifrdv;
    }

    if ($etatTraitement == "1") {
        $libelleTraitement = "Le client a la permission de faire une demande de " . $rdv->motifrdv;
    } else {
        $libelleTraitement = $libelleTraitement;
        $typeprestation = $operation;
    }

    $sqlInsertPrestation = "INSERT INTO tbl_prestations(idcontrat,typeprestation,prestationlibelle,montantSouhaite,etape,estMigree,created_at) VALUES (?,?,?,?,?,?, NOW() )";
    $queryOptionsPrestations = array(
        $rdv->police,
        $typeprestation,
        $typeprestation,
        $motantOperation,
        '-1',
        '0'
    );
    $rrr = $fonction->_Database->Update($sqlInsertPrestation, $queryOptionsPrestations);

    $idprestation = $rrr["LastInsertId"];

    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement= ?, libelleTraitement=?, reponseGest=?, datetraitement=NOW(), traiterLe=NOW(), gestionnaire=?, updatedAt =NOW() , etatSms =? , idCourrier=? , estPermit=? WHERE idrdv = ?";
    $queryOptions = array(
        "3",
        $etatTraitement,
        $libelleTraitement,
        addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
        $traiterpar,
        '1',
        intval($idprestation),
        '1',
        intval($rdv->idrdv)
    );

    $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
    if ($result != null) {

        $message = "Cher client(e), votre demande de " . strtoupper($typeprestation) . " n° " . $rdv->idrdv . " du " . date("d/m/Y", strtotime($rdv->daterdv)) . " a ete  a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";
        envoyerSMS_RDV($rdv->tel, $message, $rdv->idrdv);
        echo json_encode($rdv->idrdv);
    }
    return $rdv->idrdv;
}


function notificationRDV_gestionnaireByNissa($daterdveff, $ListeGest, $telephone, $idrdv, $url)
{
    global $fonction;

    list($idGestionnaire, $gestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode("|", $ListeGest, 4);
    $sqlQuery2 = "SELECT id , email , codeagent , telephone,  TRIM(CONCAT(nom ,' ', prenom)) as gestionnairenom FROM users WHERE  id='" . $idGestionnaire . "' ";
    $result2 = $fonction->_getSelectDatabases($sqlQuery2);
    if ($result2 != NULL) {
        $retourGestionnaire = $result2[0];

        $dateeffective = date('d/m/Y', strtotime($daterdveff));
        $telGestionnaire = $retourGestionnaire->telephone;
        $emailGestionnaire = $retourGestionnaire->email;
        $nomGestionnaire = $retourGestionnaire->gestionnairenom;
        $codeagent = $retourGestionnaire->codeagent;

        $retour_agent = $fonction->getRetourneContactInfosGestionnaire($codeagent);

        if (isset($retour_agent["telephone"]) && !empty($retour_agent["telephone"])) {
            $message = "Cher(e) client(e), suite à votre demande de rendez-vous, un conseiller vous recevra le " . $dateeffective . "." . PHP_EOL . "Pour plus d' information , veuillez contacter le " . $retour_agent["telephone"] . ".";
        } else {
            $message = "Votre RDV est prévu le $dateeffective à $villesGestionnaire. Un conseiller client vous recevra. Pour plus d'informations, Consultez votre espace client: urlr.me/9ZXGSr . ";
        }

        envoyerSMS_RDV($telephone, $message, $idrdv);

        if (isset($retour_agent["email_final"]) && !empty($retour_agent["email_final"])) {

            //envoi mail au gestionnaire 
            $lienEnvoiMail = "$url/envoiMail-rdv.php?";
            $url_notification = $lienEnvoiMail . "action=transmettreRDV&data=[idrdv:" . trim($idrdv) . "]";
            $retour = file_get_contents($url_notification);
        }
    }
}

function envoyerSMS_RDV($telephone, $message, $idrdv)
{
    global $fonction;
    $numero = "225" . substr($telephone, -10);
    //$ref_sms = "RDV-" . $idrdv;

    $sms_envoi = new SMSService();
    if (strlen($message) > 160) $message = substr($message, 0, 160);
    $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

    $sqlUpdateRdvUpdate = "UPDATE tblrdv SET etatSms =?  WHERE idrdv = ?";
    $queryOptions = array(
        '1',
        intval($idrdv)
    );
    $fonction->_Database->Update($sqlUpdateRdvUpdate, $queryOptions);
}


function _insertBordereauRDV(BordereauRDV $ligneBordereau, $rdv, $reference, $id_users = null, $auteur = null)
{
    global $fonction;

    if ($id_users == null) $id_users = $_SESSION["id"];
    if ($auteur == null) $auteur = $_SESSION["utilisateur"];

    $idrdv = $rdv->idrdv;
    //$reference = "";

    $sqlQuery = "SELECT * FROM tbl_detail_bordereau_rdv WHERE NumeroRdv = '" . $idrdv . "' ORDER BY NumeroRdv ";
    $result_rdv = $fonction->_getSelectDatabases($sqlQuery);
    if ($result_rdv != null) {
        //update le bordereau

        //print_r($result_rdv);

        //echo "update le bordereau".$idrdv . PHP_EOL;
        $sqlQuery = "UPDATE `tbl_detail_bordereau_rdv` SET `reference`=?, `dureeContrat`=? , `typeOperation` =?, `cumulRachatsPartiels`=?, `cumulAvances`=?, `provisionNette`=?, `valeurRachat`=?, `valeurMaxRachat`=?, `valeurMaxAvance`=?, `observation`=?, `garantieSurete`=?, `MontantTransformation`=?, `conservationCapital`=?, `id_users`=?, `auteur`=?, `created_at`=?";
        $parametreInsert = array(
            $reference,
            $ligneBordereau->dureeContrat,
            $ligneBordereau->typeOperation,
            $ligneBordereau->cumulRachatsPartiels,
            $ligneBordereau->cumulAvances,
            $ligneBordereau->provisionNette,
            $ligneBordereau->valeurRachat,
            $ligneBordereau->valeurMaxRachat,
            $ligneBordereau->valeurMaxAvance,
            $ligneBordereau->observation,
            $ligneBordereau->garantieSurete,
            $ligneBordereau->valeurRachat,
            $ligneBordereau->conservationCapital,
            $id_users,
            $auteur,
            date('Y-m-d H:i:s')
        );
    } else {
        //echo "insert le bordereau".$idrdv . PHP_EOL;
        //$reference = "RDV-" . $idrdv;
        $sqlQuery = "INSERT INTO `tbl_detail_bordereau_rdv`(`reference`, `NumeroOrdre`, `NumeroRdv`, `IDProposition`, `telephone`, `produit`, `souscripteur`, `assure`, `dateEffet`, `dateEcheance`, `dureeContrat`, `typeOperation`, `cumulRachatsPartiels`, `cumulAvances`, `provisionNette`, `valeurRachat`, `valeurMaxRachat`, `valeurMaxAvance`, `observation`, `garantieSurete`, `MontantTransformation`, `conservationCapital`, `id_users`, `auteur`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $parametreInsert = array(
            $reference,
            $ligneBordereau->NumeroOrdre,
            $idrdv,
            $ligneBordereau->IDProposition,
            $ligneBordereau->telephone,
            $ligneBordereau->produit,
            addslashes(trim($ligneBordereau->souscripteur)),
            $ligneBordereau->assure,
            $ligneBordereau->dateEffet,
            $ligneBordereau->dateEcheance,
            $ligneBordereau->dureeContrat,
            $ligneBordereau->typeOperation,
            $ligneBordereau->cumulRachatsPartiels,
            $ligneBordereau->cumulAvances,
            $ligneBordereau->provisionNette,
            $ligneBordereau->valeurRachat,
            $ligneBordereau->valeurMaxRachat,
            $ligneBordereau->valeurMaxAvance,
            $ligneBordereau->observation,
            $ligneBordereau->garantieSurete,
            $ligneBordereau->valeurRachat,
            $ligneBordereau->conservationCapital,
            $id_users,
            $auteur,
            date('Y-m-d H:i:s')
        );
    }

    $tab = $fonction->_Database->Update($sqlQuery, $parametreInsert);
    return $tab;
}
