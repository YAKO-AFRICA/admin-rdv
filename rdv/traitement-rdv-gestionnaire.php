<?php

use PSpell\Config;

session_start();


if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}



include("../autoload.php");


$plus = "";
$resultat = "";
$afficheuse = FALSE;

$tablo_doc_attendu = array();

if (isset($_COOKIE["idrdv"])) {


    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idrdv = GetParameter::FromArray($_COOKIE, 'idrdv');
    $action = GetParameter::FromArray($_COOKIE, 'action');


    $sqlSelect = "SELECT tblrdv.* , TRIM(libelleVilleBureau) as villes , concat(users.nom,' ',users.prenom) as nomgestionnaire , users.codeagent as codeagent FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau INNER JOIN users ON tblrdv.gestionnaire = users.id WHERE tblrdv.idrdv = '" . $idrdv . "'";
    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);
    if ($retour_rdv == null) {
        // header('Location: liste-rdv-attente');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $rdv = $retour_rdv[0];
    if ($rdv->etat != "2") {
        // header('Location: liste-rdv-attente');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';
    $daterdveff = isset($rdv->daterdveff) ? date('Y-m-d', strtotime($rdv->daterdveff)) : $daterdv;
    $minDate = date('Y-m-d', strtotime($daterdveff . ' +1 day'));
    $maxDate = date('Y-m-d', strtotime($daterdveff . ' +14 days'));

    $infosBordereaux = $fonction->getRetourneInfosBordereaux(" WHERE NumeroRdv = '" . $rdv->idrdv . "'");
    $montantTotal = 0;
    $montantMaxAvance = 0;
    $MontantTransformation = 0;
    $valeurRachat = 0;
    $valeurMaxAvance = 0;

    if (!empty($infosBordereaux[0])) {


        $montantTotal = (int) ($infosBordereaux[0]->valeurRachat ?? 0);
        $montantMaxAvance = (int) ($infosBordereaux[0]->valeurMaxAvance ?? 0);
        $MontantTransformation = (int) ($infosBordereaux[0]->MontantTransformation ?? 0);
        $valeurRachat = (int) ($infosBordereaux[0]->valeurRachat ?? 0);
        $valeurMaxAvance = (int) ($infosBordereaux[0]->valeurMaxAvance ?? 0);
    }
    $afficheuse = TRUE;
} else {
    header("Location:deconnexion.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "../include/entete.php"; ?>
</head>

<body>

    <?php include "../include/header.php";  ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <!-- Page Header -->
                <div class="page-header mb-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-primary font-weight-bold">Traitement des demandes</h4>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0">
                                    <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                    <li class="breadcrumb-item">Liste des demandes</li>
                                    <li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Bouton retour -->
                <div class="mb-3">
                    <button class="btn btn-warning" onclick="retour()">
                        <i class="fa fa-arrow-left"></i> Retour
                    </button>
                </div>

                <!-- Titre RDV -->
                <div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
                    <div class="card-body text-center ">
                        <h3 style="color:white">Traitement de la demande de RDV N°
                            <span class="text-warning">
                                <?= strtoupper($rdv->idrdv) . " du  " . $rdv->daterdv ?>
                            </span>
                        </h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Informations RDV -->
                    <div class="col-md-6">
                        <div class="card mb-4 bg-light">
                            <div class="card-header bg-white">
                                <h4 class="text-center text-info font-weight-bold" style="color:#033f1f!important;">Information sur la demande de RDV</h4>
                            </div>
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Titre du demandeur :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->titre ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Prestation souhaitée :</label>
                                        <input type="text" class="form-control" id="motifrdv" name="motifrdv" value="<?= $rdv->motifrdv ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>ID contrat / N° de police(s) :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->police ?? '' ?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label>Nom & Prénom(s) :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->nomclient ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Date de naissance :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->datenaissance ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Téléphone :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->tel ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Email :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->email ?? '' ?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Ville choisie :</label>
                                        <input type="text" class="form-control" id="villes" name="villes" value="<?= $rdv->villes ?? '' ?>" disabled>
                                        <input type="hidden" id="idVilleBureau" name="idVilleBureau" value="<?= $rdv->idVilleBureau ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV souhaitée :</label>
                                        <input type="text" class="form-control" id="daterdv" name="daterdv" value="<?= $rdv->daterdv ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Ville effective :</label>
                                        <?= $fonction->getVillesBureau($rdv->idTblBureau, "disabled") ?>

                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV Effective <span class="text-danger">*</span> :</label>
                                        <input type="date" class="form-control" id="daterdveff" name="daterdveff" onblur="checkDate('1')" value="<?= $daterdveff ?>" readonly>

                                    </div>
                                    <div class="form-group col-md-12" hidden>
                                        <label>Gestionnaire <span class="text-danger">*</span> :</label>
                                        <input type="text" class="form-control" id="gestionnaire" name="gestionnaire" value="<?= $rdv->gestionnaire . '|' . $rdv->nomgestionnaire . '|' . $rdv->idTblBureau . '|' . $rdv->villes ?>" readonly>
                                    </div>
                                    <input type="hidden" value="<?= $rdv->idrdv ?? '' ?>">
                                </div>
                                <div id="infos-compteurRDV"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contrat NSIL -->
                    <div class="col-md-6">
                        <div class="card mb-4 bg-light" style="background-color:#f2f2f2">
                            <div class="card-header" style="background-color: #033f1f;">
                                <h4 class="text-center font-weight-bold" style="color:white!important;">Information bordereau sur le contrat</h4>
                            </div>
                            <div class="card-body" style="color:#033f1f!important; background-color:#f2f2f2" style="font: size 14px; font-weight:bold;">
                                <div class="row" id="infos-contrat"></div>
                                <?php if ($infosBordereaux != null) : ?>
                                    <?php foreach ($infosBordereaux as $bordereau) : ?>

                                        <div class="row w-100">
                                            <div class="form-group col-md-7">
                                                <p class="mb-0">
                                                    Souscripteur : <span class="text-info font-weight-bold"> <?= empty($bordereau->souscripteur) ? "" : $bordereau->souscripteur ?> </span>
                                                </p>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <p class="mb-0">
                                                    Assuré : <span class="text-info font-weight-bold"> <?= empty($bordereau->assure) ? "" : $bordereau->assure ?> </span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row w-100">
                                            <div class="form-group col-md-7">
                                                <p class="mb-0">
                                                    Produit : <span class="text-info font-weight-bold"><?= empty($bordereau->produit) ? "" : $bordereau->produit ?></span>
                                                </p>
                                                <p class="mb-0">
                                                    Type d’opération : <span class="text-info font-weight-bold"><?= empty($bordereau->typeOperation) ? "" : $bordereau->typeOperation ?></span>
                                                </p>

                                                <p class="mb-0">
                                                    Durée du contrat : <span class="text-info font-weight-bold"> <?= empty($bordereau->dureeContrat) ? "" : $bordereau->dureeContrat ?></span>
                                                </p>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <p class="mb-0">
                                                    Date d’effet : <span class="text-info font-weight-bold"><?= empty($bordereau->dateEffet) ? "" : date("d/m/Y", strtotime($bordereau->dateEffet)) ?></span>
                                                </p>
                                                <p class="mb-0">
                                                    Date d’échéance : <span class="text-info font-weight-bold"><?= empty($bordereau->dateEcheance) ? "" : date("d/m/Y", strtotime($bordereau->dateEcheance)) ?></span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row w-100">
                                            <div class="form-group col-md-7">
                                                <p class="mb-0">
                                                    Valeur de rachat : <span class="text-info font-weight-bold"><?= empty($bordereau->valeurRachat) ? "" : number_format($bordereau->valeurRachat, 0, ',', ' ') ?> FCFA</span>
                                                    <input type="text" class="form-control" id="valeurRachat" name="valeurRachat" value="<?= $bordereau->valeurRachat ?? '0' ?>" hidden>
                                                </p>
                                                <p class="mb-0">
                                                    Provision nette : <span class="text-info font-weight-bold"><?= empty($bordereau->provisionNette) ? "" : number_format($bordereau->provisionNette, 0, ',', ' ') ?> FCFA</span>
                                                </p>
                                                <p class="mb-0">
                                                    Cumul rachats partiels : <span class="text-info font-weight-bold"><?= empty($bordereau->cumulRachatsPartiels) ? "" : number_format($bordereau->cumulRachatsPartiels, 0, ',', ' ') ?> FCFA</span>
                                                </p>
                                            </div>

                                            <div class="form-group col-md-5">
                                                <p class="mb-0">
                                                    Cumul avances : <span class="text-info font-weight-bold"><?= empty($bordereau->cumulAvances) ? "" : number_format($bordereau->cumulAvances, 0, ',', ' ') ?> FCFA</span>
                                                </p>
                                                <p class="mb-0">
                                                    Valeur max avance : <span class="text-info font-weight-bold"><?= empty($bordereau->valeurMaxAvance) ? "" : number_format($bordereau->valeurMaxAvance, 0, ',', ' ') ?> FCFA</span>
                                                </p>
                                                <p class="mb-0">
                                                    Montant transformation : <span class="text-info font-weight-bold"> <?= empty($bordereau->MontantTransformation) ? "" : number_format($bordereau->MontantTransformation, 0, ',', ' ') ?> FCFA </span>
                                                    <input type="text" class="form-control" id="valeurTransformation" name="valeurTransformation" value="<?= $bordereau->MontantTransformation ?? '0' ?>" hidden>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row w-100">
                                            <div class="form-group col-md-7">
                                                <p class="mb-0">
                                                    Auteur : <span class="text-info font-weight-bold"> <?= empty($bordereau->auteur) ? "" : $bordereau->auteur ?> </span>
                                                </p>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <p class="mb-0">
                                                    Date création : <span class="text-info font-weight-bold"> <?= empty($bordereau->created_at) ? "" : date("d/m/Y", strtotime($bordereau->created_at)) ?></span>
                                                </p>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center">Aucun bordereau pour ce RDV</p>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($infosBordereaux != null): ?>
                <?php endif; ?>
                <!-- Traitement RDV -->
                <div class="card mb-4">
                    <div class="card-header text-center bg-white">
                        <h4 class="font-weight-bold" style="border:1px solid gray;background:#033f1f!important; color:white;">Traitement du RDV</h4>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Traité le : <span id="dateheure" class="font-weight-bold"> <?= date('d/m/Y H:i:s') ?> </span></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <p>Traité par : <span class="font-weight-bold"><?= $_SESSION['utilisateur'] ?></span></p>
                            </div>
                        </div>
                        <input type="hidden" id="valideur" value="<?= $_SESSION['utilisateur'] ?>">

                        <div class="row card-body" style="color: #033f1f;">
                            <div class=" offset-md-6 col-md-6">
                                <div class="form-group">
                                    <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Voulez vous autoriser le client à déposer son courrier pour <span style="color:red"> <?= strtoupper($rdv->motifrdv) ?> </span> ? </label>
                                </div>
                                <div class="form-group row">
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                        <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">NON , autres propositions</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                        <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">OUI , le client est permit à déposer son courrier pour <?= strtoupper($rdv->motifrdv) ?> </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="afficheuseusers"></div>
                        <div id="afficheuse" class="mt-3"></div>

                        <div class="modal-footer mt-4">
                            <label id="lib"></label>
                            <div id="optionTraitement"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="footer-wrap pd-20 mb-20">
                <?php include "../include/footer.php"; ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content ">
                <div class="modal-body text-center">
                    <div class="card-body" id="iframeAfficheDocument">

                    </div>
                    <input type="text" class="form-control" id="val_doc2" name="val_doc3" hidden>
                    <input type="text" class="form-control" id="document" name="document" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" name="valid_download" id="valid_download" class="btn btn-success" style="background: #033f1f !important;">VALIDER DOCUMENT</button>
                    <button type="button" name="annuler_download" id="annuler_download" class="btn btn-danger" style="background:red !important;">REJETER DOCUMENT</button>
                    <button type="button" id="closeAfficheDocument" name="closeAfficheDocument" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center font-18">
                    <h4 class="padding-top-30 mb-30 weight-500">
                        Voulez vous enregistrer la demande de rdv <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                    </h4>
                    <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                    <span style='color:seagreen'>le client sera notifier du resultat de la demande de rdv</span>
                    </hr>
                    <input type="text" id="idprestation" name="idprestation" hidden>
                    <input type="text" id="motif" name="motif" hidden>
                    <input type="text" id="traiterpar" name="traiterpar" hidden>
                    <input type="text" id="observations" name="observations" hidden>

                    <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                        <div class="col-6">
                            <button type="button" id="annulerRejet" name="annulerRejet" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                            NON
                        </div>
                        <div class="col-6">
                            <button type="button" id="confirmerPermissionDepot" name="confirmerPermissionDepot" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
                            OUI
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content ">
                <div class="modal-body text-center">
                    <div class="card-body" id="msgEchec">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success" style="background: #033f1f !important;">OK</button>
                    <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                </div>
            </div>
        </div>
    </div>

    <!-- js -->
    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>
    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>
    <script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <!-- buttons for Export datatable -->
    <script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="../src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
    <!-- Datatable Setting js -->
    <script src="../vendors/scripts/datatable-setting.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


    <script>
        const tabloResulatOperation = {
            partielle: {
                etat: "2",
                libelle: "Le client a demandé un rachat partiel",
                operation: "Rachat partiel",
                permission: "1",
                resultat: "conservation"
            },
            avance: {
                etat: "2",
                libelle: "Le client a demandé une avance / prêt",
                operation: "Avance ou prêt",
                permission: "1",
                resultat: "conservation"
            },
            renonce: {
                etat: "3",
                libelle: "Le client décide de conserver son contrat",
                operation: "Renonce",
                permission: "1",
                resultat: "conservation"
            },
            absent: {
                etat: "5",
                libelle: "Le client ne s'est pas présenté",
                operation: "Absent",
                permission: "0",
                resultat: "absent"
            },
            transformation: {
                etat: "4",
                libelle: "Le client a demandé une transformation",
                operation: "Transformation",
                permission: "1",
                resultat: "transformation"
            },
            autres: {
                etat: "3",
                libelle: "Autre observation",
                operation: "Autres",
                permission: "0",
                resultat: "autres"
            }
        };


        const montantTotal = <?= $montantTotal ?>;
        const montantMaxAvance = <?= $montantMaxAvance ?>;
        const MontantTransformation = <?= $MontantTransformation ?>;
        const valeurRachat = <?= $valeurRachat ?>;

        // const $mtTransformation = $('#montanttransformation');
        // const $mtClient = $('#motantclient');

        $(document).ready(function() {



            const etape = <?= $rdv->etat ?>;
            const idcontrat = "<?= $rdv->police ?>";
            const idVilleEff = "<?= $rdv->idTblBureau ?>";

            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;
            //var valeurTransformation = document.getElementById("valeurTransformation").value;
            //var valeurRachat = document.getElementById("valeurRachat").value;
            var valeurTransformation = MontantTransformation;

            // if (idcontrat !== "") {
            //     remplirModalEtatComtrat(idcontrat);
            // }


            console.log(objetRDV + " " + dateRDVEffective);

            $('input[name="customRadio"]').change(function() {
                const valeur = $(this).val();

                if (valeur === '3') {
                    getMenuValider();
                } else if (valeur === '2') {
                    getMenuRejeter();
                } else {
                    //alert('Le RDV est en attente.');
                    $("#afficheuse").empty();
                    $("#color_button").text("#F9B233");
                    $("#optionTraitement").empty();
                }
            });
        });



        $(document).on("click", "#valider", function(evt) {

            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();

            const resultatOpe = $('#resultatOpe').val();
            const obervation = $('#obervation').val();

            let date_report_rdv = '';
            let produittransformation = '';
            let montanttransformation = '';
            let motantclient = '';
            let optionadditif = resultatOpe;
            let montantsouhaiteAvance = '';
            let montantsouhaitePartiel = '';
            //const gestionnaire = $('#ListeGest').val();



            if (resultatOpe == 'absent') {
                var optradio = document.getElementsByName("action_absent");
                let action_absent = getRecupRadio(optradio);
                //alert("action_absent ====> " + action_absent);
                if (action_absent == "reprogrammer") {
                    date_report_rdv = $('#nouvelle_date_rdv').val();
                    // console.log("date rdv ===> " + dateRDV + " <==== nouvelle date rdv " + date_report_rdv);
                    optionadditif += "|" + action_absent + "|" + date_report_rdv;
                } else {
                    optionadditif += "|" + action_absent;
                }
            } else if (resultatOpe == 'transformation') {
                produittransformation = document.getElementById("produittransformation").value;
                montanttransformation = document.getElementById("montanttransformation").value;
                motantclient = document.getElementById("motantclient").value;

                if (produittransformation == '') {
                    alert("Veuillez renseigner le produit de transformation SVP !!");
                    document.getElementById("produittransformation").focus();
                    return false;
                }
                if (montanttransformation == '') {
                    alert("Veuillez renseigner le montant de transformation SVP !!");
                    document.getElementById("montanttransformation").focus();
                    return false;
                }
                if (motantclient == '') {
                    alert("Veuillez renseigner le montant du client SVP !!");
                    document.getElementById("motantclient").focus();
                    return false;
                }
                //console.log("transformation ====> " + produittransformation + " m1 ====> " + montanttransformation + " m2 ====> " + motantclient);
                optionadditif += "|" + produittransformation + "|" + montanttransformation + "|" + motantclient;
            } else if (resultatOpe == 'avance') {

                //alert("resultatOpe ====> " + resultatOpe);
                montantsouhaiteAvance = document.getElementById("montantsouhaiteAvance").value;
                if (montantsouhaiteAvance == '') {
                    alert("Veuillez renseigner le montant de l'avance SVP !!");
                    document.getElementById("montantsouhaiteAvance").focus();
                    return false;
                }

                //alert("montantsouhaiteAvance ====> " + montantsouhaiteAvance);
                optionadditif += "|" + montantsouhaiteAvance;
            } else if (resultatOpe == 'partielle') {
                //alert("resultatOpe ====> " + resultatOpe);
                montantsouhaitePartiel = document.getElementById("montantsouhaitePartiel").value;
                if (montantsouhaitePartiel == '') {
                    alert("Veuillez renseigner le montant du rachat partiel SVP !!");
                    document.getElementById("montantsouhaitePartiel").focus();
                    return false;
                }
                //alert("montantsouhaitePartiel ====> " + montantsouhaitePartiel);
                optionadditif += "|" + montantsouhaitePartiel;
            }


            console.log(" ===> " + objetRDV + " " + dateRDV + " " + etat + " // " + resultatOpe + " //==> " + optionadditif);
            //const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = gestionnaire.split("|");
            //console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);


            $.ajax({
                url: "../config/routes.php",
                method: "post",
                dataType: "json",
                data: {
                    idrdv: "<?= $rdv->idrdv ?>",
                    idcontrat: "<?= $rdv->police ?>",
                    //gestionnaire: gestionnaire,
                    objetRDV: objetRDV,
                    daterdveff: dateRDV,
                    resultatOpe: resultatOpe,
                    obervation: obervation,
                    optionadditif: optionadditif,
                    etat: "operationRDVReception",
                },
                success: function(response) {

                    console.log("response", response);

                    if (response != '-1' && response != '0') {
                        let code = response;

                        if (code == "transformation") {
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2> Merci de vous connecter a la plateforme de <span class="text-success">` + code + `</span> afin de poursuivre le traitement !!</h2></div>`
                        } else {
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>Traitement de la demande de rdv <span class="text-success">` + code + `</span> a bien été enregistrée  !</h2></div>`
                        }

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du traitement de la demande de rdv <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                    }
                    $("#msgEchec").html(a_afficher)
                    $('#notificationValidation').modal("show")

                },
                error: function(response) {
                    console.error("Erreur AJAX :", response);
                }
            })

        })


        $(document).on("click", "#confirmerRejet", function(evt) {


            let observation = $('#obervation').val();
            const dateRDV = $('#daterdveff').val();
            var valideur = document.getElementById("valideur").value;

            if (observation.length > 0) {
                let libMotif = "Motif de rejet : " + observation;

                let idrdv = "<?= $rdv->idrdv ?>"
                let idcontrat = "<?= $rdv->police ?>"

                let aff = `n° ${idrdv} du ${dateRDV}`

                $("#observations").val(observation)
                $("#idprestation").val(idrdv)
                $("#traiterpar").val(valideur)

                $("#a_afficher_1").text(aff)
                $("#a_afficher_2").text(libMotif)
                $('#confirmation-modal').modal('show')
            } else {
                alert("Veuillez renseigner le motif après reception du rdv svp !!");
                return;
            }
        })

        $("#confirmerPermissionDepot").click(function(evt) {

            var idrdv = document.getElementById("idprestation").value;
            var motif = document.getElementById("motif").value;
            var traiterpar = document.getElementById("traiterpar").value;
            var observation = document.getElementById("observations").value;

            let valideur = <?= $_SESSION['id'] ?>

            console.log(idrdv, motif, valideur, traiterpar, observation);
            $.ajax({
                url: "../config/routes.php",
                data: {
                    idrdv: idrdv,
                    motif: motif,
                    traiterpar: valideur,
                    observation: observation,
                    etat: "confirmerPermissionDepotRDV"
                },
                dataType: "json",
                method: "post",
                //async: false,
                success: function(response, status) {
                    //console.log(response)

                    let a_afficher = ""
                    if (response != '-1' && response != '0') {
                        let code = response;

                        a_afficher = `<div class="alert alert-success" role="alert">
								<h2>Traitement de la demande de rdv <span class="text-success">` + code + `</span> a bien été enregistrée  !</h2></div>`

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du traitement de la demande de rdv <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                    }
                    $("#msgEchec").html(a_afficher)
                    $('#notificationValidation').modal("show")

                },
                error: function(response, status, etat) {
                    console.log(etat, response)
                }
            })
        })


        $("#retourNotification").click(function() {

            $('#notificationValidation').modal('hide')
            location.href = "detail-rdv";

        })

        $(document).on('change', 'input[name="action_absent"]', function() {

            if ($(this).val() === 'reprogrammer') {
                $('#blocReprogrammation').slideDown();
            } else {
                $('#blocReprogrammation').slideUp();
            }
        });

        // Quand le montant de la transformation est modifié "montanttransformation"
        $(document).on('input', '#montanttransformation', function() {

            const mtTransformation = parseFloat($(this).val()) || 0;

            if (mtTransformation > montantTotal) {
                alert("Le montant de la transformation ne peut pas depasser le montant total : " + montantTotal);
                $('#montanttransformation').val(montantTotal);
                $('#motantclient').val('0');
            }
            const reste = montantTotal - mtTransformation;
            if (reste >= 0) {
                $('#motantclient').val(reste);
            }
        });

        // Quand le montant client est modifié "motantclient"
        $(document).on('input', '#motantclient', function() {

            const mtClient = parseFloat($(this).val()) || 0;
            const reste = montantTotal - mtClient;

            if (mtClient > montantTotal) {
                alert("Le montant du client ne peut pas depasser le montant total : " + mtClient);
                $('#motantclient').val(montantTotal);
                $('#montanttransformation').val('0');
            }
            if (reste >= 0) {
                $('#montanttransformation').val(reste);
            }
        });

        // Quand le montant souhaité est modifié "montantsouhaite"
        $(document).on('input', '#montantsouhaiteAvance', function() {

            const montantsouhaite = parseFloat($(this).val()) || 0;
            const reste = montantMaxAvance - montantsouhaite;

            if (montantsouhaite > montantMaxAvance) {
                alert("Le montant shouhaite ne peut pas depasser le montant total : " + montantMaxAvance);
                //$('#montantsouhaite').val(montantMaxAvance);
                $("#valider").prop("disabled", true);
            } else {
                $("#valider").prop("disabled", false);
            }
        });

        // Quand le montant souhaité est modifié "montantsouhaitePartiel"
        $(document).on('input', '#montantsouhaitePartiel', function() {

            const montantsouhaite = parseFloat($(this).val()) || 0;
            const reste = montantTotal - montantsouhaite;

            if (montantsouhaite > montantTotal) {
                alert("Le montant shouhaite ne peut pas depasser la valeur de rachat : " + montantTotal);
                //$('#montantsouhaite').val(montantMaxAvance);
                $("#valider").prop("disabled", true);
            } else {
                $("#valider").prop("disabled", false);
            }
        });



        // Quand un resultatOp est sélectionné
        $(document).on('change', '#resultatOpe', function() {
            let resultatOp = $(this).val();
            if (resultatOp == "transformation") {
                let htmlTransformation = `
                    <!-- Le reste de ton contenu ici -->
                    <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Operation de transformation </h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="tel" class="col-form-label">Veuillez selectionnez le produit de transformation :</label>
                                    <select class="form-control" id="produittransformation" name="produittransformation">
                                        <option value="" selected disabled >Veuillez selectionnez le produit de transformation</option>
                                        <option value="invest">Invest +</option>
                                        <option value="gagnant">100% GAGNANT</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="tel" class="col-form-label">Motant de la transformation :</label>
                                    <input type="number" class="form-control" id="montanttransformation" name="montanttransformation" placeholder="Montant de la transformation" value="<?php echo intval($MontantTransformation); ?>">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="tel" class="col-form-label">Motant a payer au client :</label>
                                    <input type="number" class="form-control" id="motantclient" name="motantclient" placeholder="Montant a payer au client">
                                </div>
                            </div>
                    `
                $('#resultatEntretienClient').html(htmlTransformation);
            } else if (resultatOp == "avance") {
                let tabloOperation = tabloResulatOperation[resultatOp];
                console.log(tabloOperation)
                let htmlAvance = `
                    <!-- Le reste de ton contenu ici -->
                    <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Operation ${tabloOperation.operation} </h4>
                    <p>Le montant maximum ${tabloOperation.operation} est de : <span class="text-danger font-weight-bold"><?php echo number_format($valeurMaxAvance, 0, ',', ' '); ?> FCFA </span> </p>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="tel" class="col-form-label">Motant souhaité de ${tabloOperation.operation} :</label>
                                    <input type="number" class="form-control" id="montantsouhaiteAvance" name="montantsouhaiteAvance" placeholder="Montant souhaité de ${tabloOperation.operation}" value="<?php echo intval($valeurMaxAvance); ?>" max="<?php echo intval($valeurMaxAvance); ?>">
                                </div>
                            </div>
                    `
                $('#resultatEntretienClient').html(htmlAvance);

            } else if (resultatOp == "partielle") {
                let tabloOperation = tabloResulatOperation[resultatOp];
                console.log(tabloOperation)
                let htmlAvance = `
                    <!-- Le reste de ton contenu ici -->
                    <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Operation ${tabloOperation.operation} </h4>
                    <p>Le montant maximum ${tabloOperation.operation} est de : <span class="text-danger font-weight-bold"><?php echo number_format($valeurRachat, 0, ',', ' '); ?> FCFA </span> </p>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="tel" class="col-form-label">Motant souhaité de ${tabloOperation.operation} :</label>
                                    <input type="number" class="form-control" id="montantsouhaitePartiel" name="montantsouhaitePartiel" placeholder="Montant souhaité de ${tabloOperation.operation}" value="<?php echo intval($valeurRachat); ?>" max="<?php echo intval($valeurRachat); ?>">
                                </div>
                            </div>
                    `
                $('#resultatEntretienClient').html(htmlAvance);

            } else if (resultatOp == "absent") {
                let htmlAbsent = `
                    <!-- Le reste de ton contenu ici -->
                   <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Issu de l'absence</h4>
                   <div style="border-top:4px solid #033f1f; width:100%; margin-bottom:15px;"></div>
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="custom-control custom-radio mb-3">
                                <input
                                    type="radio"
                                    id="absent_annuler"
                                    name="action_absent"
                                    class="custom-control-input"
                                    value="annuler"
                                >
                                <label class="custom-control-label font-weight-bold text-danger" for="absent_annuler">
                                    Annuler le rendez-vous
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio mb-3">
                                <input
                                    type="radio"
                                    id="absent_reprogrammer"
                                    name="action_absent"
                                    class="custom-control-input"
                                    value="reprogrammer"
                                >
                                <label class="custom-control-label font-weight-bold text-success" for="absent_reprogrammer">
                                    Reprogrammer le rendez-vous
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-radio mb-3">
                                <input
                                    type="radio"
                                    id="absent_attente"
                                    name="action_absent"
                                    class="custom-control-input"
                                    value="attente"
                                >
                                <label class="custom-control-label font-weight-bold text-warning" for="absent_attente">
                                    Mettre en attente le rendez-vous
                                </label>
                            </div>
                        </div>

                    </div>
                    <div id="blocReprogrammation" class="mt-3" style="display:none;">
                        <label>Nouvelle date du RDV</label>
                        <input type="date" class="form-control" name="nouvelle_date_rdv" id="nouvelle_date_rdv" min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>" onchange="checkDate('1')"  required>
                        <small id="errorDate2" class="text-danger"></small>
                    </div>
                    `
                $('#resultatEntretienClient').html(htmlAbsent);
            } else {
                $('#resultatEntretienClient').html('');
            }

        });

        // Quand un gestionnaire est sélectionné
        $(document).on('change', '#ListeGest', function() {
            verifierActivationBouton();
        });

        function retour() {
            window.history.back();
        }

        // Génère le formulaire de validation
        function getMenuValider() {

            let notif = `
                    <!-- Le reste de ton contenu ici -->
                    <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Observation apres reception du RDV </h4>
                                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                                    </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;">Resultat apres entretien du client (<span style="color:red;">*</span>)</label>
                                        <select name="resultatOpe" id="resultatOpe" class="form-control "  data-rule="required">
                                            <option value="">...</option>
                                            <option value="transformation">Le client souhaite effectuer une Transformation</option>
                                            <option value="partielle">Le client a demandé un rachat partiel</option>
                                            <option value="avance">Le client a demandé une avance / pret</option>
                                            <option value="renonce">Le client a decidé de conserver son contrat</option>
                                            <option value="absent">Le client ne s'est pas presenté</option>
                                            <option value="autres">Autres</option>
                                        </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="tel" class="col-form-label">commentaires :</label>
                                    <textarea class="form-control" id="obervation" name="obervation"></textarea>
                                </div>
                                
                            </div>
                            
                            <div id="resultatEntretienClient"></div>`;

            let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Enregistrer le traitement</button> 
                <div id="spinner" style="display: none; margin-top: 10px;">
                  <div class="spinner-border" style="color: #076633;" role="status">
                    <span class="visually-hidden"></span>
                  </div>
                </div>
                `


            $("#color_button").text(`#033f1f`)
            $("#nom_button").text(`Enregistrer le traitement`)
            $("#afficheuse").html(notif)
            $("#optionTraitement").html(bouton_valider)

        }


        function getAPIVerificationProfil(keys) {
            let resultat;
            $.ajax({
                url: "https://api.laloyalevie.com/oldweb/encaissement-bis",
                data: {
                    idContrat: keys
                },
                dataType: "json",
                method: "post",
                async: false,
                success: function(response, status) {
                    console.log(response)
                    resultat = response

                    if (resultat.error != false) {
                        console.log(response)
                    }
                },
                error: function(response, status, etat) {
                    resultat = '-1';
                }
            })
            return resultat
        }


        function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number).toFixed(decimals);

            let parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

            return parts.join(dec_point);
        }

        function formatDateJJMMAA(dateString) {
            if (!dateString) return '';
            const [year, month, day] = dateString.split("-");
            return `${day}/${month}/${year}`;
        }



        function remplirModalEtatComtrat(idProposition) {

            let retour = getAPIVerificationProfil(idProposition);
            let details;
            let confirmer
            let nonRegle
            let enc
            let infos = ""
            //console.log(retour)

            if (retour) {

                //console.log(retour)

                details = retour["details"]
                confirmer = retour['regle']
                enc = retour['enc']
                nonRegle = enc["nonRegle"]

                let contactsPersonne = retour["contactsPersonne"]

                let numero = ''

                $.each(contactsPersonne, function(indxct, contactP) {

                    if (contactP.contact != "" && contactP.contact != null) {
                        numero += `Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span><br>`
                    }
                })

                $.each(details, function(indx, data) {

                    let prime = number_format(data.TotalPrime, 2, ',', ' ');
                    let capital = number_format(data.CapitalSouscrit, 2, ',', ' ');

                    infos += `
                        <div class="row w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Nom et prenom : <span class="text-infos" style="font-weight:bold;"> ${data.nomSous+" "+data.PrenomSous} </span></p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Date de naissance : <span class="text-infos" style="font-weight:bold;"> ${data.DateNaissance} </span></p>
                            </div>
                        </div>
                        <div class="row  w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Produit :<span class="text-infos" style="font-weight:bold;"> ${data.produit} FCFA</span></p>
                                <p class="mb-0 " style="font-size: 0.7em;"> Capital :<span class="text-infos" style="font-weight:bold;"> ${data.CapitalSouscrit} FCFA</span></p>
                                <p class="mb-0 " style="font-size: 0.7em;">Prime :<span class="text-infos" style="font-weight:bold;"> ${data.TotalPrime} FCFA</span> </p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Effet Reel :<span class="text-infos" style="font-weight:bold;"> ${data.DateEffetReel} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Fin Adhesion :<span class="text-infos" style="font-weight:bold;"> ${data.FinAdhesion} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Duree du contrat :<span class="text-infos" style="font-weight:bold;"> ${data.DureeCotisationAns} ans </span> </p>
                            </div>
                        </div>
                        <div class="row  w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre d'emission :<span class="text-infos" style="font-weight:bold;"> ${data.NbreEmission} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre Encaissment :<span class="text-infos" style="font-weight:bold;"> ${data.NbreEncaissment} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre Impayes :<span class="text-infos" style="font-weight:bold;"> ${data.NbreImpayes} </span> </p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Total Emission :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalEmission)} FCFA </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Total Encaissment :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalEncaissement)} FCFA </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Total Impayes :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalImpayes)} FCFA </span> </p>
                            </div>
                        </div>`
                })

                console.log(details)
                $("#infos-contrat").html(infos);
            } else {
                $("#infos-contrat").html(" pas d'informations disponible");
            }
        }

        function verifierActivationBouton() {
            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();

            const toutRempli = objetRDV && objetRDV !== "null" && dateRDV && gestionnaire && etat === "2";
            $("#valider").prop("disabled", !toutRempli);
        }

        function getMenuRejeter() {
            const objetRDV = $("#villesRDV").val();
            const dateRDVEffective = $("#daterdveff").val();
            const motifrdv = $("#motifrdv").val();
            const gestionnaire = $("#gestionnaire").val();

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesRDVGestionnaire] = gestionnaire.split("|");

            let notif = `
                <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Vous autorisez le client a faire son depot de courrier : <br>Libelle du courrier : <span style="color:red"> ${motifrdv} </span>  </h4>
                
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <input type="hidden" class="form-control" id="resultatOpe" name="resultatOpe" value="${motifrdv}">
                    <div class="form-group col-md-12 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner le motif après reception <span style="color:red;">*</span> :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"></textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

            let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    ENREGISTRER 
                </button>`;

            $("#afficheuse").html(notif);
            $("#color_button").text("red");
            $("#optionTraitement").html(bouton_rejet);
        }

        function getRecupRadio(radios) {

            var found = 1;
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    found = 0;
                    return radios[i].value;
                    break;
                }
            }
            if (found == 1) {
                return "1";
            }
        }

        function checkDate() {

            const objetRDV = $('#villesRDV').val();
            const dateStr = $('#nouvelle_date_rdv').val();

            console.log(objetRDV)
            let tablo = objetRDV.split(";");
            var idVilleEff = tablo[0];
            var villesR = tablo[1];


            if (!dateStr) {
                alert("Veuillez choisir une date.");
                return;
            }

            // Récupération du numéro du jour
            const parts = dateStr.split("-");
            const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
            const dayNumber = dateObj.getDay(); // 0=Dim, 6=Sam

            const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            const jourNom = jours[dayNumber];

            console.log("Date sélectionnée :", dateStr);
            console.log("Jour :", jourNom, "| Numéro :", dayNumber);

            // Bloquer weekend
            if (dayNumber === 0 || dayNumber === 6) {

                alert("❌ Les rendez-vous ne peuvent pas être pris le week-end.");
                $("#errorDate2").html("❌ Les rendez-vous ne peuvent pas etre pris le week-end.");
                //	desactiver le bouton modifierRDV
                $("#valider").prop("disabled", true);
                return;
            }

            // Récupérer les jours autorisés depuis l'API
            getJoursReception(idVilleEff, function(joursAutorises) {

                console.log("Jours autorisés :", joursAutorises);
                // Vérification : est-ce que dayNumber est dans les jours autorisés ?
                const autorise = joursAutorises.includes(dayNumber);

                if (autorise) {
                    //	activer le bouton modifierRDV
                    $("#valider").prop("disabled", false);
                    //alert("✅ Le jour " + jourNom + " est autorisé pour la réception !");
                    $("#errorDate2").html("✅ <span style='color:green;'> Le " + jourNom + " est autorisé pour la réception pour la ville de <b>" + villesR + "</b>!</span>");
                } else {

                    //alert("❌ Le jour " + jourNom + " n’est pas autorisé pour la réception.");
                    $("#errorDate2").html("❌ <span style='color:red;'> Le " + jourNom + " n’est pas autorisé pour la réception pour la ville de <b>" + villesR + "</b>.</span>");
                    //	desactiver le bouton modifierRDV
                    $("#valider").prop("disabled", true);
                }
            });
        }

        function getJoursReception(idVilleEff, callback) {
            $.ajax({
                url: "../config/routes.php",
                type: "POST",
                dataType: "json",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "receptionJourRdv"
                },
                success: function(response) {
                    console.log("Jours autorisés reçus :", response);

                    // Nettoyage : tableau de nombres
                    const joursAutorises = response.map(j => Number(j));

                    callback(joursAutorises);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", error);
                    callback([]); // Aucun jour autorisé si erreur
                }
            });
        }
    </script>


</body>

</html>