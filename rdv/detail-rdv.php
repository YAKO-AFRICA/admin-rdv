<?php

session_start();


if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
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



    $retour_rdv = $fonction->_getRetourneDetailRDV($idrdv);
    if ($retour_rdv == null) {
        // header('Location: liste-rdv-attente');
        if (isset($_SERVER['HTTP_REFERER'])) {
            // Redirect back to the previous page
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Fallback if no referrer is set
            header('Location: index.php'); // or any default page
            exit();
        }
        //exit;
    }

    $rdv = $retour_rdv[0];
    $retourEtat = Config::tablo_statut_rdv[$rdv->etat];
    $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';
    if ($rdv->etatSms == "1") {
        $lib_etatSms = "Oui";
        $color_etatSms = "badge badge-success";
    } else {
        $lib_etatSms = "Non";
        $color_etatSms = "badge badge-danger";
    }

    if ($rdv->estPermit == "1") {
        $lib_estPermit = "Oui";
        $color_estPermit = "badge badge-success";
    } else {
        $lib_estPermit = "Non";
        $color_estPermit = "badge badge-danger";
    }

    $afficheuse = TRUE;
} else {
    header("Location:deconnexion.php");
}

//exit;

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "../include/entete.php"; ?>

</head>

<style>
    .card {
        border-radius: 8px;
    }

    .card-header {
        border-bottom: none;
    }

    .small p {
        font-size: 13px;
        line-height: 1.3;
    }
</style>

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
                                    <li class="breadcrumb-item active" aria-current="page">Détail Rdv</li>
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
                        <h3 style="color:white">Détail RDV N°
                            <span class="text-warning">
                                <?= strtoupper($rdv->idrdv) . " du  " . $rdv->daterdv ?>
                            </span>
                        </h3>
                    </div>
                </div>


                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4" style="color:#033f1f!important;">Detail du client</h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                    </div>
                    <div class="row pd-20">
                        <div class="col-md-6">
                            <p><span class="text-color">Titre :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->titre ?? '' ?></span></p>
                            <p><span class="text-color">Nom & Prenom :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomclient ?? '' ?></span></p>
                            <p><span class="text-color">Date de naissance :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->datenaissance ?? '' ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->lieuresidence ?? '' ?></span></p>
                            <p><span class="text-color">Numero de téléphone :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->tel ?? ''  ?></span></p>
                            <p><span class="text-color">E-mail :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->email ?? '' ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Rendez-vous</h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                    </div>
                    <div class="row pd-20">
                        <div class="col-md-6">
                            <p><span class="text-color">Rdv pris le </span> : <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->dateajou ?></span></p>
                            <p><span class="text-color">ID contrat / N° de police(s) :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->police ?? "---" ?></span></p>
                            <p><span class="text-color">Ville du Rdv choisie :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeChoisie ?? "--" ?></span></p>
                            <p><span class="text-color">Date de Rdv choisie:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->daterdv ?? "--" ?></span></p>

                        </div>
                        <div class="col-md-6">
                            <p><span class="text-color">Code RDV :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->codedmd ?? "---" ?></span></p>
                            <!-- <p><span class="text-color">Traiter le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->datetraitement ?? "--" ?></span></p>
                            <p><span class="text-color">Traiter par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= ($rdv->etat == "3") ? strtoupper($rdv->nomgestionnaire) :  strtoupper($rdv->nomAdmin . " " . $rdv->prenomAdmin) ?? "--" ?></span></p> -->
                            <p><span class="text-color">Motif du Rdv :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->motifrdv ?? "---" ?></span></p>
                            <p><span class="text-color">Ville du Rdv effective:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeEffective ?? "--" ?></span></p>
                            <p><span class="text-color">Date de Rdv effective:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($rdv->daterdveff) ? "--" : date("d/m/Y", strtotime($rdv->daterdveff)) ?></span></p>

                            <p><span class="text-color">Etat du rdv :</span> <span class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span></p>
                            <?php

                            if ($rdv->etat == "0") {
                            ?>
                                <p><span class="text-color">Motif d'annulation :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->reponse ?? "--" ?></span></p>
                            <?php
                            }
                            ?>
                        </div>

                    </div>
                </div>
                <!-- <?php if ($rdv->etat == "2" || $rdv->etat == "3"): ?>
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Transmission du Rendez-vous effectif</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        </div>
                        <div class="row pd-20">
                            <div class="col-md-6">
                                <p><span class="text-color">Transmis le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($rdv->transmisLe) ? "" : date("d/m/Y à H:i:s", strtotime($rdv->transmisLe)) ?></span></p>
                                <p><span class="text-color">Transmis à :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomgestionnaire . " ( " . $rdv->codeagentgestionnaire . " )"  ?? "--" ?></span></p>
                                <p><span class="text-color">Villes : </span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeEffective ?? "" ?></span></p>
                                <p><span class="text-color">Transmis par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomAdmin . " " . $rdv->prenomAdmin ?></span></p>
                                <p><span class="text-color">Sms envoyé ? :</span> <span style="text-transform:uppercase; font-weight:bold;" class="<?php echo $color_etatSms; ?>"><?php echo $lib_etatSms ?></span></p>

                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Traiter le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= date("d/m/Y à H:i:s", strtotime($rdv->datetraitement)) ?? "--" ?></span></p>
                                <p><span class="text-color">Est Permit ? :</span> <span style="text-transform:uppercase; font-weight:bold;" class="<?php echo $color_estPermit; ?>"><?php echo $lib_estPermit ?></span></p>

                                <?php if ($rdv->estPermit == 1): ?>
                                    <p><span class="text-color">Issue apres Rdv : </span> <span class="text-infos"><span class="btn btn-success ">Accordé pour <?= $rdv->motifrdv  ?></span></span></p>

                                <?php else: ?>
                                    <p><span class="text-color">Issue apres Rdv : </span> <span class="text-infos"><span class="btn btn-danger ">Non Accordé pour <?= $rdv->motifrdv  ?></span></span></p>
                                    <?php if ($rdv->etatTraitement == 5): ?>
                                        <button class="btn btn-warning btn-sm modifier" id="modifier-<?= $rdv->idrdv ?> " style="background-color:#F9B233; color:white"><i class="fa fa-edit"></i> Modifier rdv</button>

                                    <?php endif; ?>
                                <?php endif; ?>
                                <p><span class="text-color">Reponse Apres entretien :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->libelleTraitement ?? "--" ?></span></p>
                                <?php if (!empty($rdv->reponseGest)): ?>
                                    <p><span class="text-color">Observation :</span><span class="text-infos" style="font-weight:bold;"><?= $rdv->reponseGest ?? "" ?></span></p>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?> -->

                <?php
                // print_r($rdv);
                if ($rdv->etat == "2" || $rdv->etat == "3") {
                ?>
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Transmission du Rendez-vous effectif</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        </div>
                        <div class="row pd-20">
                            <div class="col-md-6">
                                <p><span class="text-color">Transmis le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($rdv->transmisLe) ? "" : date("d/m/Y à H:i:s", strtotime($rdv->transmisLe)) ?></span></p>
                                <p><span class="text-color">Transmis à :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomgestionnaire ?? "--" ?></span></p>
                                <p><span class="text-color">Villes : </span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeEffective ?? "" ?></span></p>

                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Transmis par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomAdmin . " " . $rdv->prenomAdmin ?></span></p>
                                <p><span class="text-color">Sms envoyé ? :</span> <span style="text-transform:uppercase; font-weight:bold;" class="<?php echo $color_etatSms; ?>"><?php echo $lib_etatSms ?></span></p>
                                <p>
                                    <span class="text-color">Issue apres Rdv :</span>
                                    <?php if ($rdv->estPermit == 1 && $rdv->etatTraitement == 1): ?>
                                        <span class="text-infos"><span class="btn btn-success btn-sm">Accordé pour <?= $rdv->motifrdv  ?> </span></span>
                                    <?php elseif ($rdv->estPermit == 1 && $rdv->etatTraitement != 1): ?>
                                        <span class="text-infos"><span class="btn btn-danger ">Non Accordé pour <?= $rdv->motifrdv  ?></span></span>
                                        
                                    <?php endif; ?>
                
                                    <?php if ($rdv->etatTraitement == 5): ?>
                                        <button class="btn btn-warning btn-sm modifier" id="modifier-<?= $rdv->idrdv ?> " style="background-color:#F9B233; color:white"><i class="fa fa-edit"></i> Modifier rdv</button>

                                    <?php endif; ?>
                                </p>
                                <p><span class="text-color">Reponse Apres entretien :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->libelleTraitement ?? "--" ?></span></p>

                            </div>
                        </div>

                        <?php

                        if (!empty($rdv->reponseGest)) {
                        ?>
                            <div class="row pd-20">
                                <div class="col-md-12">
                                    <p><span class="text-color">Observation :</span><span class="text-infos" style="font-weight:bold;"><?= $rdv->reponseGest ?? "" ?></span></p>
                                </div>

                            </div>
                    </div>
                    <hr>

                <?php
                        }
                ?>

                <?php
                    if ($rdv->etatCourrier != "") {
                ?>

                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Courrier</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        </div>
                        <div class="row pl-20">
                            <div class="col-md-6">
                                <p><span class="text-color">Etat du courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->etatCourrier == "-1" ? "Pas encore déposé" : ($rdv->etatCourrier == "0" ? "Réjété" : ($rdv->etatCourrier == "1" ? "En attente de traitement" : ($rdv->etatCourrier == "2" ? "Validé" : ""))) ?></span></p>
                                <p><span class="text-color">Reponse apres traitement :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->reponseCourrier  ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Date de depôt de courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->etatCourrier == "-1" ? ($rdv->createdCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->createdCourrier))) : ($rdv->deposeCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->deposeCourrier))) ?></span></p>
                                <p><span class="text-color">Date de traitement de courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->traiteCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->traiteCourrier)) ?></span></p>
                            </div>
                        </div>
                        <div class="row pd-20 d-flex justify-content-end">
                            <button class="btn btn-warning" onclick="retour()">
                                <i class="fa fa-arrow-left"></i> Retour
                            </button>
                        </div>
                    </div>
            <?php
                    }
                }

            ?>
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
                        Voulez vous rejeter la demande de prestation <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                    </h4>
                    <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                    <span style='color:seagreen'>le client sera notifier du rejet de la prestation</span>
                    </hr>
                    <input type="text" id="idprestation" name="idprestation" hidden>
                    <input type="text" id="motif" name="motif" hidden>
                    <input type="text" id="traiterpar" name="traiterpar" hidden>
                    <input type="text" id="observation" name="observation" hidden>

                    <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                        <div class="col-6">
                            <button type="button" id="annulerRejet" name="annulerRejet" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                            NON
                        </div>
                        <div class="col-6">
                            <button type="button" id="validerRejet" name="validerRejet" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
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

    <div class="modal fade" id="modifierRDV-modal2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-info font-weight-bold">
                        Modification de la demande de RDV
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <span id="enteteClientRDV"></span>
                    <hr>
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <div class="form-row">
                                <input type="text" class="form-control" id="idcontrat2" name="idcontrat2" hidden>
                                <input type="text" class="form-control" id="idrdv2" name="idrdv2" hidden>
                                <input type="text" class="form-control" id="villesR2" name="villesR2" hidden>
                                <input type="text" class="form-control" id="motifrdv2" name="motifrdv2" hidden>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label>
                                        Date RDV
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="daterdveff2"
                                        name="daterdveff2"
                                        onblur="checkDate('1')" min="<?= date('Y-m-d') ?>"
                                        required>
                                    <small id="errorDate2" class="text-danger"></small>
                                </div>
                                <div class="form-group col-md-7">
                                    <label>
                                        Motif de modification
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                        class="form-control"
                                        id="motifmodif"
                                        name="motifmodif"
                                        rows="3"
                                        required></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="idTblBureau2" name="idTblBureau2">
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Fermer
                    </button>
                    <button type="submit" id="modifierDateRDV" name="modifierDateRDV" class="btn btn-warning">
                        <i class="fa fa-save"></i> Modifier RDV
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ================= JS ================= -->
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
        $(document).on('click', '.modifier', function() {

            const idrdv = this.id.split('-')[1];
            if (idrdv == "undefined") return;
            $.ajax({
                url: "../config/routes.php",
                method: "POST",
                dataType: "json",
                data: {
                    idrdv: idrdv,
                    etat: "rechercherRDV"
                },
                success: function(response) {
                    console.log(response);
                    //ouvrirModalModifierRDV(response)

                    let afficheuse = `
                        <div class="row g-3">

                            <!-- DÉTAIL CLIENT -->
                            <div class="col-md-7">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 fw-bold" style="color:#033f1f;">
                                            Détail du client
                                        </h6>
                                        <div style="border-top:3px solid #033f1f;width:60px;"></div>
                                    </div>

                                    <div class="card-body py-2 px-3 small">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Titre :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.titre ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Nom & Prénom :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.nomclient ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Date naissance :</span><br>
                                                    <span class="fw-bold">${response.datenaissance ?? ''}</span>
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Résidence :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.lieuresidence ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Téléphone :</span><br>
                                                    <span class="fw-bold">${response.tel ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Email :</span><br>
                                                    <span class="fw-bold">${response.email ?? ''}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DÉTAIL RDV -->
                            <div class="col-md-5">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 fw-bold" style="color:#033f1f;">
                                            Détail du RDV
                                        </h6>
                                        <div style="border-top:3px solid #033f1f;width:60px;"></div>
                                    </div>

                                    <div class="card-body py-2 px-3 small">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">ID RDV :</span><br>
                                                    <span class="fw-bold">${response.idrdv ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Contrat / Police :</span><br>
                                                    <span class="fw-bold">${response.police ?? ''}</span>
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Ville :</span><br>
                                                    <span class="fw-bold">${response.villes ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Motif :</span><br>
                                                    <span class="fw-bold">${response.motifrdv ?? ''}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>`;

                    $("#enteteClientRDV").html(afficheuse);
                    $("#idrdv2").val(response.idrdv);
                    $("#idcontrat2").val(response.police);
                    $("#villesR2").val(response.villes);
                    $("#idTblBureau2").val(response.idTblBureau);
                    $("#motifrdv2").val(response.motifrdv);
                    $("#motifmodif").val(response.libelleTraitement);

                    $("#modifierRDV-modal2").modal('show');

                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });

        })


        $("#modifierDateRDV").click(function() {

            const idrdv = document.getElementById("idrdv2").value;
            const idcontrat = document.getElementById("idcontrat2").value;
            const idVilleEff = document.getElementById("idTblBureau2").value;
            const daterdveff = document.getElementById("daterdveff2").value;
            const motifmodif = document.getElementById("motifmodif").value;
            console.log(idrdv, idcontrat, idVilleEff, daterdveff, motifmodif);
            if (!daterdveff) {
                alert("Veuillez renseigner la date du RDV SVP !!");
                document.getElementById("daterdveff2").focus();
                return false;
            }
            if (!motifmodif) {
                alert("Veuillez renseigner le motif de modification SVP !!");
                document.getElementById("motifmodif").focus();
                return false;
            }

            $.ajax({
                url: "../config/routes.php",
                method: "POST",
                dataType: "json",
                data: {
                    idrdv: idrdv,
                    idcontrat: idcontrat,
                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    motifmodif: motifmodif,
                    etat: "modifierRDVByGestionnaire"
                },
                success: function(response) {
                    console.log(response);

                    if (response !== '-1' && response !== '0') {
                        const msg = `<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été modifiée !</h2></div>`;
                        $("#msgEchec").html(msg);
                        $('#notificationValidation').modal("show");
                    } else {
                        const msg = `<div class="alert alert-danger" role="alert"><h2>Le RDV <span class="text-danger">${idrdv}</span> n'a pas été modifiée !</h2></div>`;
                        $("#msgEchec").html(msg);
                        $('#notificationValidation').modal("show");
                    }
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });

        })

        $(document).on("click", "#valider", function(evt) {

            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = gestionnaire.split("|");

            ///console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);

            $.ajax({
                url: "config/routes.php",
                method: "post",
                dataType: "json",
                data: {
                    idrdv: "<?= $rdv->idrdv ?>",
                    idcontrat: "<?= $rdv->police ?>",
                    gestionnaire: gestionnaire,
                    objetRDV: objetRDV,
                    daterdveff: dateRDV,
                    etat: "transmettreRDV",
                },
                success: function(response) {

                    if (response != '-1') {
                        let a_afficher = `
                        <div class="alert alert-success" role="alert" style="text-align: center; font-size: 18px ; color: #033f1f; font-weight: bold">
                            Le rdv n° ${response} a bien été transmis au gestionnaire ${nomgestionnaire} pour reception le ${dateRDV} à ${villesGestionnaire}
                        </div>`;

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")
                    } else {
                        let a_afficher = `
                        <div class="alert alert-danger" role="alert">
                           Desole , le rdv n° ${response} n'a pas été transmis au gestionnaire ${nomgestionnaire} 
                        </div>`;


                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")
                    }

                },
                error: function(response) {
                    console.error("Erreur AJAX :", response);
                }
            })
        })

        $("#retourNotification").click(function() {

            $('#notificationValidation').modal('hide')
            location.href = "detail-rdv";

        })

        // Quand un gestionnaire est sélectionné
        $(document).on('change', '#ListeGest', function() {
            verifierActivationBouton();
        });

        function retour() {
            window.history.back();
        }

        function checkDate() {

            const villesR = document.getElementById("villesR2").value;
            const dateStr = document.getElementById("daterdveff2").value;
            var idVilleEff = document.getElementById("idTblBureau2").value;


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
                $("#modifierDateRDV").prop("disabled", true);
                return;
            }

            // Récupérer les jours autorisés depuis l'API
            getJoursReception(idVilleEff, function(joursAutorises) {

                //console.log("Jours autorisés :", joursAutorises);
                // Vérification : est-ce que dayNumber est dans les jours autorisés ?
                const autorise = joursAutorises.includes(dayNumber);

                if (autorise) {
                    //	activer le bouton modifierRDV
                    $("#modifierDateRDV").prop("disabled", false);
                    //alert("✅ Le jour " + jourNom + " est autorisé pour la réception !");
                    $("#errorDate2").html("✅ <span style='color:green;'> Le " + jourNom + " est autorisé pour la réception pour la ville de <b>" + villesR + "</b>!</span>");
                } else {

                    //alert("❌ Le jour " + jourNom + " n’est pas autorisé pour la réception.");
                    $("#errorDate2").html("❌ <span style='color:red;'> Le " + jourNom + " n’est pas autorisé pour la réception pour la ville de <b>" + villesR + "</b>.</span>");
                    //	desactiver le bouton modifierRDV
                    $("#modifierDateRDV").prop("disabled", true);
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