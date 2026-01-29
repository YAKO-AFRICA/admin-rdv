<?php

class Config
{

    const Societe = "YAKO AFRICA";
    const plateformeName = "RDV-ADMIN";
    const URL_YAKO = "https://www.yakoafricassur.com";

    const Version = "1.0.0";

    const LogDirectory = 'log/';
    const DatabaseHost = '51.255.64.8';
    const DatabaseName = 'laloyale_bdwebdev';
    //const DatabaseName = 'laloyale_bdweb';
    const DatabaseUser = 'laloyale_masterdev';
    const DatabasePass = '1Mot2Passe.DSI';

    // laloyale_masterdev
    // 1Mot2Passe.DSI
    // const DatabaseUser = 'laloyale_llvweb';
    // const DatabasePass = 'j2y1YgwwIQ6u';
    // const LogDirectory = 'log/';
    // const DatabaseHost = 'localhost';
    // const DatabaseName = 'laloyale_bdweb';
    // const DatabaseUser = 'root';
    // const DatabasePass = '';

    // const DatabaseName = 'laloyale_bdweb';
    // const DatabaseUser = 'laloyale_llvweb';
    // const DatabasePass = 'j2y1YgwwIQ6u';

    // fcea920f7412b5da7be0cf42b8c93759

    const MessageServiceIndisponible = 'Desole, le service est momentanement indisponible. Veuillez reessayer plus tard.';

    const pageAccueil = "./index.php";
    const lib_pageAccueil = "Accueil";
    const lib_pageAccueil2 = "Accueil";

    const pageListePRESTATION = "./prestations";
    const lib_pageListePRESTATION = "Liste des demandes de prestation en attente";
    const lib_pageListePRESTATION2 = "Liste demande prestation";

    const pageRecherchePRESTATION = "./rechercher-prestations";
    const lib_pageRecherchePRESTATION = "rechercher une demande de prestation";

    const pageDetailPRESTATION = "./detail-prestations";
    const lib_pageDetailPRESTATION = "Ma Fiche de demande de prestation";
    const lib_pageDetailPRESTATION2 = "Fiche de demande de prestation";


    const pageListTraitementEffectuesPRESTATION = "./traitement-prestations";
    const lib_pageListTraitementEffectuesPRESTATION = "Traitement(s) Effectue(s)";
    const lib_pageListTraitementEffectues2PRESTATION = "Liste des demandes de Prestations traitées";


    const pageListeRDV = "./prestations";
    const lib_pageListeRDV = "Liste des RDVs en attente";
    const lib_pageListeRDV2 = "Liste RDV";

    const pageRechercheRDV = "./rechercher-prestations";
    const lib_pageRechercheRDV = "rechercher un RDV";

    const pageDetailRDV = "./detail-prestations";
    const lib_pageDetailRDV = "Ma Fiche de RDV";
    const lib_pageDetailRDV2 = "Fiche RDV";


    const pageListTraitementEffectuesRDV = "./traitement-prestations";
    const lib_pageListTraitementEffectuesRDV = "Traitement(s) Effectue(s)";
    const lib_pageListTraitementEffectues2RDV = "Liste des demandes de Prestations traitées";


    const EN_ATTENTE = "En attente";
    const VALIDER2 = "acceptee(s) - MIGRATION NSIL";
    const VALIDER = "acceptee(s)";
    const TRANSMIS = "transmis";
    const EN_COURS = "cours de traitement";
    const REJETE = "Rejetee(s)";
    const TRAITER = "Traite(s)";
    const TOTAL_PRESTATION = "Total Demande de Prestation(s)";

    const EN_SAISIE = "En saisie - Non transmis";
    const SAISIE_INACHEVEE = "Saisie inachevee";


    const LIB_EN_ATTENTE = "EN ATTENTE";
    const LIB_VALIDER = "ACCEPTE(S)";
    const LIB_REJETE = "REJETE(S)";
    const LIB_TRANSMIS = "TRANSMIS";
    const LIB_TRAITER = "TRAITE(S)";
    const LIB_SAISIE_INACHEVEE = "Saisie inachevee";

    const etat_ATTENTE = "1";
    const etat_VALIDER = "2";
    const etat_TRANSMIS = "2";
    const etat_REJETE = "3";
    const etat_TRAITER = "3";


    const etat_EN_SAISIE = "0";
    const etat_SAISIE_INACHEVEE = "-1";


    const color_NOUVEAU = "badge badge-secondary";
    const color_SUCCESS = "badge badge-success";
    const color_REJETE = "badge badge-warning";
    const color_DARK = "badge badge-dark";

    const color_EN_SAISIE = "badge badge-primary";
    const color_SAISIE_INACHEVEE = "badge badge-danger";

    const tablo_statut_prestation = array(
        "1" => array("lib_statut" => self::EN_ATTENTE, "libelle" => self::LIB_EN_ATTENTE, "statut_traitement" => "1", "color_statut" => self::color_NOUVEAU, "color" => "gray", "url" => "liste-prestation-attente", "icone" => "micon dw dw-edit"),
        "2" => array("lib_statut" => self::VALIDER, "libelle" => self::LIB_VALIDER, "statut_traitement" => "2", "color_statut" => self::color_SUCCESS, "color" => "#033f1f", "url" => "liste-prestation-traite", "icone" => "micon fa fa-check"),
        "3" => array("lib_statut" => self::REJETE, "libelle" => self::LIB_REJETE, "statut_traitement" => "3", "color_statut" => self::color_REJETE, "color" => "#F9B233", "url" => "liste-prestation-rejet", "icone" => "micon fa fa-close")
    );

    const tablo_statut_rdv = array(
        "1" => array("lib_statut" => self::EN_ATTENTE, "libelle" => self::LIB_EN_ATTENTE, "statut_traitement" => "1", "color_statut" => self::color_NOUVEAU, "color" => "gray", "url" => "liste-rdv-attente", "icone" => "micon dw dw-edit"),
        "2" => array("lib_statut" => self::TRANSMIS, "libelle" => self::LIB_TRANSMIS, "statut_traitement" => "2", "color_statut" => self::color_SUCCESS, "color" => "#033f1f", "url" => "liste-rdv-transmis", "icone" => "micon fa fa-forward fa-2x"),
        "0" => array("lib_statut" => self::REJETE, "libelle" => self::LIB_REJETE, "statut_traitement" => "0", "color_statut" => self::color_SAISIE_INACHEVEE, "color" => "red", "url" => "", "icone" => "micon fa fa-close"),
        "3" => array("lib_statut" => self::TRAITER, "libelle" => self::LIB_TRAITER, "statut_traitement" => "3", "color_statut" => self::color_REJETE, "color" => "#F9B233", "url" => "liste-rdv-traite", "icone" => "micon fa fa-check"),
        "-1" => array("lib_statut" => self::SAISIE_INACHEVEE, "libelle" => self::LIB_SAISIE_INACHEVEE, "statut_traitement" => "-1", "color_statut" => self::color_DARK, "color" => "black", "url" => "liste-rdv-rejet", "icone" => "micon fa fa-close"),
        "4" => array("lib_statut" => self::TRAITER, "libelle" => self::LIB_TRAITER, "statut_traitement" => "3", "color_statut" => self::color_REJETE, "color" => "#F9B233", "url" => "liste-rdv-traite", "icone" => "micon fa fa-check"),
    );

    const tablo_resultat_entretien_rdv = array(
        "partielle" => array("etat" => "2", "libelle" => "Le client a demandé un rachat partiel", "operation" => "Rachat partiel", "permission" => "1", "resultat" => "conservation"),
        "avance" => array("etat" => "2", "libelle" => "Le client a demandé une avance / pret", "operation" => "Avance ou prêt", "permission" => "1", "resultat" => "conservation"),
        "conserver" => array("etat" => "3", "libelle" => "Le client décide de conserver son contrat", "operation" => "Renonce", "permission" => "0", "resultat" => "conservation"),
        "renonce" => array("etat" => "3", "libelle" => "Le client décide de conserver son contrat", "operation" => "Renonce", "permission" => "0", "resultat" => "conservation"),
        "absent" => array("etat" => "5", "libelle" => "Le client ne s'est pas présenté", "operation" => "Absent", "permission" => "0", "resultat" => "absent"),
        "transformation" => array("etat" => "4", "libelle" => "Le client a demandé une transformation", "operation" => "transformation", "permission" => "0", "resultat" => "conservation"),
        "rachat" => array("etat" => "6", "libelle" => "Le client desire un rachat total", "operation" => "rachat", "permission" => "1", "resultat" => "sortie"),
        "terme" => array("etat" => "7", "libelle" => "Le contrat est terminé", "operation" => "terme", "permission" => "1", "resultat" => "sortie"),
        "autres" => array("etat" => "3", "libelle" => "Autre observation", "operation" => "Autres", "permission" => "0", "resultat" => "conservation")
    );

    const TABLE_USER = "users";
    const TABLE_PRESTATION = "tbl_prestations";
    const TABLE_RDV = "tblrdv";
    const TABLE_DETAIL_COURRIER = "tbldetailcourrier";
    const TABLE_TYPE_PRESTATION = "tbl_type_prestations";
    const TABLE_VILLE = "tblvillebureau";

    //const TABLE_PRESTATION = "laloyale_bdwebdev.tbl_prestations";
    //const TABLE_RDV = "laloyale_bdwebdev.tblrdv";
    //const TABLE_DETAIL = "laloyale_bdwebdev.tbldetailcourrier";
    //const TABLE_TYPE_PRESTATION = "laloyale_bdwebdev.tbldetailcourrier";


    const sqlInsert_TABLE_DETAIL_COURRIER = "INSERT INTO " . self::TABLE_DETAIL_COURRIER . " (typeOperation, libelleOperation, delaiTraitement, idProposition, idCourrier, createdAt) VALUES (?,?,?,?,?,?) ";
    const sqlUpdate_TABLE_PRESTATION = "UPDATE " . self::TABLE_PRESTATION . " SET etape = ? , estMigree= ? , traiterle = NOW() , updated_at=NOW() , migreele = ? , traiterpar = ? , observationtraitement= ?  WHERE  id = ? ";
    const sqlUpdate_TABLE_DETAIL_COURRIER = "UPDATE " . self::TABLE_DETAIL_COURRIER . " SET idTblCourrier = ? , codeCourrier = ? , estMigree = ? WHERE idDetail = ? AND idProposition = ? ";


    const SqlSelect_TypePrestations = "SELECT *  FROM " . self::TABLE_TYPE_PRESTATION . " WHERE etat = 'Actif' ORDER BY id ";
    const SqlSelect_DetailPrestations = "SELECT *  FROM " . self::TABLE_DETAIL_COURRIER;
    const SqlSelect_ListPrestations = "SELECT *  FROM " . self::TABLE_PRESTATION;
    const SqlSelect_ListTypePrestations = "SELECT *  FROM " . self::TABLE_TYPE_PRESTATION;

    const SqlSelect_List_Detail_Prestations = "SELECT DISTINCT tbl_prestations.*, TRIM(CONCAT(tbl_prestations.nom ,' ', tbl_prestations.prenom)) as souscripteur , tbldetailcourrier.idProposition, tbldetailcourrier.typeOperation, tbldetailcourrier.idDetail FROM " . self::TABLE_PRESTATION . " INNER JOIN " . self::TABLE_DETAIL_COURRIER . " ON tbl_prestations.id = tbldetailcourrier.idCourrier WHERE tbl_prestations.id=? ORDER BY `tbldetailcourrier`.`createdAt` DESC LIMIT 1";
    const SqlSelect_UsersU = "SELECT *  FROM " . self::TABLE_USER . " WHERE login = ? AND password=? AND etat = '1' ORDER BY id ";
    const SqlSelect_Users = "SELECT *  FROM " . self::TABLE_USER;


    const TYPE_SERVICE_PRESTATION = "prestation";
    const TYPE_SERVICE_RDV = "rdv";
    const TYPE_SERVICE_GESTIONNAIRE = "gestionnaire";
    const TYPE_SERVICE_COURRIER = "courrier";


    const URL_API_OTP_PAYS = "https://apiotp.yakoafricassur.com/api/getAllCountries/";

    // en production
    // const URL_PRESTATION_RACINE = "https://web.yakoafricassur.com/";
    // const URL_DOC_PRESTATION = "https://web.yakoafricassur.com/api/getPrestationsDoc/";
    // const URL_DOC_SINISTRE = "https://web.yakoafricassur.com/api/getSinistreDoc/";

    // en test
    const URL_PRESTATION_RACINE = "https://testsite.yakoafricassur.com/";
    const URL_DOC_PRESTATION = "https://testsite.yakoafricassur.com/api/getPrestationsDoc/";
    const URL_DOC_SINISTRE = "https://testsite.yakoafricassur.com/api/getSinistreDoc/";


    const clauseSelectAnneeEncours2025 = "  ( 	YEAR(
					CASE	WHEN tblrdv.daterdv LIKE '%/%' THEN STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')
						WHEN tblrdv.daterdv LIKE '%-%' THEN STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y')
						ELSE NULL 	END ) = YEAR(CURDATE()) ) ";

    const clauseSelectAnneeEncours = " YEAR(
    CASE
        WHEN tblrdv.daterdv LIKE '%/%' THEN STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')
        WHEN tblrdv.daterdv LIKE '%-%' THEN STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y')
        ELSE NULL
    END
) IN (YEAR(CURDATE()), YEAR(CURDATE()) - 1)";


    const orderBySelectAnneeEncours = " CASE
				WHEN tblrdv.daterdv LIKE '%/%' THEN STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')
				WHEN tblrdv.daterdv LIKE '%-%' THEN STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y')
				ELSE NULL
			END DESC ";

            
}
