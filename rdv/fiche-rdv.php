<?php

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


    $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);

    if ($retour_rdv == null) {
        //header('Location: liste-rdv-attente');
        // exit;
    }

    $rdv = $retour_rdv[0];

    // if ($rdv->etat != "1") {
    //     header('Location: detail-rdv');
    //     exit;
    // }

    $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';

    $reply = $fonction->getRetourneVillesBureau($rdv->idTblBureau);

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
                    <div class="col-md-12">
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
                                        <input type="text" class="form-control" value="<?= $rdv->motifrdv ?? '' ?>" disabled>
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
                                        <input type="text" class="form-control" value="<?= $rdv->villes ?? '' ?>" disabled>
                                        <input type="hidden" id="idVilleBureau" name="idVilleBureau" value="<?= $rdv->idVilleBureau ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV souhaitée :</label>
                                        <input type="text" class="form-control" id="daterdv" name="daterdv" value="<?= $rdv->daterdv ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Ville effective :</label>
                                        <?= $fonction->getVillesBureau($rdv->idTblBureau, "") ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV Effective <span class="text-danger">*</span> :</label>
                                        <input type="date" class="form-control" id="daterdveff" name="daterdveff" onblur="checkDate('1')" min="<?= date('Y-m-d') ?>" value="<?= $daterdv ?>" required>
                                        <span id="errorDate" class="text-danger"></span>
                                    </div>
                                    <input type="hidden" value="<?= $rdv->idrdv ?? '' ?>">
                                </div>

                                <div id="infos-compteurRDV"></div>
                                <div id="infos-jourReception"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contrat NSIL -->
                    <!-- <div class="col-md-4">
                        <div class="card mb-4 bg-light">
                            <div class="card-header bg-white">
                                <h4 class="text-center font-weight-bold" style="color:#033f1f!important;">Information sur le contrat via NSIL</h4>
                            </div>
                            <div class="card-body" style="color:#033f1f!important;">
                                <div class="row" id="infos-contrat"></div>
                            </div>
                        </div>
                    </div> -->
                </div>

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
                                    <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Etat de la demande : </label>
                                </div>
                                <div class="form-group row">
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                        <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">Rejété le rdv</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                        <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">Affecté le rdv à un gestionnaire</label>
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
                        Voulez vous rejeter la demande de rdv <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                    </h4>
                    <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                    <span style='color:seagreen'>le client sera notifier du rejet de la demande de rdv</span>
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
        $(document).ready(function() {

            const etape = <?= $rdv->etat ?>;
            const idcontrat = "<?= $rdv->police ?>";
            const idVilleEff = "<?= $rdv->idTblBureau ?>";


            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;


            // if (idcontrat !== "") {
            //     remplirModalEtatComtrat(idcontrat);
            // }


            checkDate('0');

            $('input[name="customRadio"]').change(function() {
                const valeur = $(this).val();

                if (valeur === '3') {
                    getMenuRejeter();
                } else if (valeur === '2') {

                    getMenuValider();

                } else {
                    alert('La prestation est en attente.');
                    $("#afficheuse").empty();
                    $("#color_button").text("#F9B233");
                    $("#optionTraitement").empty();
                }
            });



            // Quand la ville change
            $('#villesRDV').change(function() {

                if ($(this).val() === "null") return;

                //const dateRDVEffective = $(this).val();
                const [idvillesRDV, villesRDV] = $(this).val().split(";");

                //console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  ");

                const valeur = $('input[name="customRadio"]:checked').val();
                if (valeur === '2') {
                    getMenuValider();
                }
                //verifierActivationBouton();
                checkDate('0');
            });

            // Quand la date change
            $('#daterdveff').change(function() {

                const dateRDVEffective = $(this).val();
                const objetRDV = $('#villesRDV').val();
                if (objetRDV === "null") return;

                const [idvillesRDV, villesRDV] = objetRDV.split(";");

                //console.log("Nouvelle date RDV effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  " + dateRDVEffective);
                const valeur = $('input[name="customRadio"]:checked').val();
                if (valeur === '2') {
                    getMenuValider();
                }
            });
        });

        $("#afficheuse").on("change", "#ListeGest", function(evt) {

        })

        $(document).on("click", "#valider", function(evt) {

            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();


            // comparer dateRDV a aujourd'hui , si superieur ok , sinon non

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = gestionnaire.split("|");

            ///console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);

            $.ajax({
                url: "../config/routes.php",
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
                            Le rdv n° ${response} a bien été transmis au gestionnaire ${nomgestionnaire} pour reception le ${formatDateJJMMAA(dateRDV)} à ${villesGestionnaire}
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
                alert("Veuillez renseigner le motif de rejet svp !!");
                return;
            }
        })

        $("#validerRejet").click(function(evt) {
            //alert("validerRejet ")

            var idrdv = document.getElementById("idprestation").value;
            var motif = document.getElementById("motif").value;
            var traiterpar = document.getElementById("traiterpar").value;
            var observation = document.getElementById("observations").value;

            let valideur = <?= $_SESSION['id'] ?>

            //console.log(idrdv, motif, valideur, traiterpar, observation);
            $.ajax({
                url: "../config/routes.php",
                data: {
                    idrdv: idrdv,
                    motif: motif,
                    traiterpar: valideur,
                    observation: observation,
                    etat: "rejeterRDV"
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
								<h2>La demande de rdv <span class="text-success">` + code + `</span> a bien été rejetée  !</h2></div>`

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du rejet de la demande de rdv <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

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

        // Quand un gestionnaire est sélectionné
        $(document).on('change', '#ListeGest', function() {
            verifierActivationBouton();
        });

        function retour() {
            window.history.back();
        }


        // Génère le formulaire de validation
        function getMenuValider() {

            const objetRDV = $("#villesRDV").val();
            const dateRDVEffective = $("#daterdveff").val();

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            const notif = `
                <div id="afficheuseCompteurUsers"></div>
                <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Affecter le RDV n° <?php echo $rdv->idrdv; ?> à un gestionnaire pour la Transformation</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;text-align: center;"></div>
                <div class="row">
                    <div class="form-group col-md-2 col-sm-12">
                        <label class="col-form-label">id contrat / police :</label>
                        <input type="text" class="form-control" name="actionType" value="valider" hidden>
                        <input type="text" class="form-control" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                    </div>
                    <div class="form-group col-md-3 col-sm-12">
                        <label class="col-form-label">Date RDV effective :</label>
                        <input type="date" class="form-control" id="dateRDVEffective" value="${dateRDVEffective}" disabled>
                    </div>
                    <div class="form-group col-md-3 col-sm-12">
                        <label class="col-form-label">Ville RDV Effective :</label>
                        <input type="text" class="form-control" id="villesRDVEffective" value="${villesRDV}" disabled>
                    </div>
                    <div class="form-group col-md-4 col-sm-12">
                        <label class="form-label" style="font-size:16px; font-weight:bold;">Liste des gestionnaires ${villesRDV} (<span style="color:red;">*</span>)</label>
                        <select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>
                    </div>
                </div>`;

            const bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white" disabled>Transmettre le RDV </button>`;

            $("#color_button").text(`#033f1f`);
            $("#nom_button").text(`Enregistrer le traitement`);
            $("#afficheuse").html(notif);
            $("#optionTraitement").html(bouton_valider);

            getListCompteurGestionnaire();
            getListeSelectAgentTransformations(idvillesRDV, villesRDV);
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
                    //console.log(response)
                    resultat = response

                    if (resultat.error != false) {
                        //console.log(response)
                    }
                },
                error: function(response, status, etat) {
                    resultat = '-1';
                }
            })
            return resultat
        }



        function getListeSelectAgentTransformations(idVilleEff, villesRDV) {

            console.log("selection de gestionnaire de transformation", idVilleEff, villesRDV)
            $.ajax({
                url: "../config/routes.php",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "afficherGestionnaire"
                },
                dataType: "json",
                method: "post",
                success: function(response, status) {

                    console.log(response);

                    if (response != "-1") {

                        let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;
                        $.each(response, function(indx, data) {
                            let agent = data.gestionnairenom;
                            html += `<option value="${data.id}|${agent}|${idVilleEff}|${villesRDV}" id="ob-${indx}">${agent}</option>`;
                        });

                        $("#ListeGest").html(html);
                    } else {
                        if (villesRDV != undefined) {
                            villesRDV = villesRDV.toUpperCase();
                            let html = `<option value="">[aucun agent de Transformations pour ${villesRDV}]</option>`;
                            $("#ListeGest").html(html);
                        }

                    }

                    //verifierActivationBouton(); // Vérifie après chargement
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });
        }


        function getListCompteurGestionnaire(idusers = "") {



            var daterdveff = document.getElementById("daterdveff").value;
            var objetRDV = document.getElementById("villesRDV").value;
            let tablo = objetRDV.split(";");
            var idVilleEff = tablo[0];
            var villesRDV = tablo[1];

            //console.log(idVilleEff, daterdveff, villesRDV, idusers)


            $.ajax({
                url: "../config/routes.php",
                data: {

                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    idusers: idusers,
                    etat: "ListCompteurGestionnaireByNISSA"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let afficheusers = ``;
                    //console.log(response)
                    if (response != "-1") {
                        if (response.length > 0) {

                            $.each(response, function(indx, data) {

                                //console.log(data)
                                let agent = data.gestionnairenom
                                let totalrdv = data.totalrdv
                                let agentid = data.id
                                let codeagent = data.codeagent

                                afficheusers += ` <div class="form-group col-md-4 col-sm-12">
                                    <label for="tel" class="col-form-label" style="font-size:14px; font-weight:bold;">${agent} :</label>
                                    <span class="badge badge-pill" style="background-color:#F9B233;color:white">${totalrdv}</span>
                                </div>`
                                let afficheusersAll = `
                                <div class="card-body p-2" style="background-color:whitesmoke">
                                     <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> Total(s) des RDV(s) par gestionnaire du <b>${formatDateJJMMAA(daterdveff)}</b> </h4>
                                    <div class="row"> ` + afficheusers + `</div>
                                </div>  `


                                $("#afficheuseCompteurUsers").html(afficheusersAll);
                            })
                        }
                    } else {

                        afficheusers += ` <div class="card-body p-2" style="background-color:#D3D3D3">
                            <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> pas d'informations disponible sur les gestionnaires de <b>${villesRDV}</b></h4></div>`
                        $("#afficheuseCompteurUsers").html(afficheusers);
                    }
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat)
                }
            })
        }


        function checkDate(parms) {


            var objetVillesRDV = document.getElementById("villesRDV").value;
            var daterdv = document.getElementById("daterdv").value;
            var daterdveff = document.getElementById("daterdveff").value;
            const optionAffectationGestionnaire = document.getElementById("customRadio3");
            const customRadio2Checked = document.getElementById("customRadio2");
            customRadio2Checked.checked = true;
            optionAffectationGestionnaire.disabled = true;

            let tablo = objetVillesRDV.split(";");
            var idVilleEff = tablo[0];
            var villesRDV = tablo[1];

            //console.log("checkDate : " + daterdveff + " : " + idVilleEff + " - " + villesRDV)
            const dateStr = daterdveff; // format YYYY-MM-DD
            const parts = dateStr.split("-"); // ["2025", "11", "18"]

            // Création de l'objet Date
            const dateObj = new Date(parts[0], parts[1] - 1, parts[2]); // Année, mois (0-indexé), jour

            // Récupération du numéro du jour
            const dayNumber = dateObj.getDay(); // 0 = Dimanche, 6 = Samedi

            // Récupération du nom du jour
            const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            const jourNom = jours[dayNumber];

            console.log("Date :", dateStr);
            console.log("Numéro du jour :", dayNumber);
            console.log("Jour de la semaine :", jourNom);


            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const rdvDate = new Date(daterdveff);
            rdvDate.setHours(0, 0, 0, 0);

            if (rdvDate >= today) {
                console.log("✅ Date RDV valide");

                // Vérification si la date est un samedi (6) ou un dimanche (0)
                if (dayNumber === 0 || dayNumber === 6) {

                    optionAffectationGestionnaire.disabled = true;
                    customRadio2Checked.checked = true;
                    $("#afficheuse").html('');
                    $("#errorDate").html("");
                    alert("Les rendez-vous ne peuvent pas être pris le week-end ou les jours fériés. Veuillez sélectionner un jour en semaine.");
                    //$('input[name="daterdveff"]').val('');
                    $("#infos-compteurRDV").text("Les rendez-vous ne peuvent pas être pris le week-end ou les jours fériés. Veuillez sélectionner un jour en semaine.").show();
                    return; // Arrête l'exécution
                } else {
                    //$("#infos-compteurRDV").text("").hide();

                    //getRetourneReceptionJour(idVilleEff, dayNumber, function(existe) {
                    getRetourneReceptionJour2(idVilleEff, dayNumber, function(result) {

                        // if (result.existe) {
                        //     console.log("✅ Le jour existe :", result.day);
                        // } else {
                        //     console.log("❌ Le jour n'existe pas :", result.day);
                        // }
                        // console.log("Données complètes :", result.data);


                        if (result.existe) {
                            //alert("✅ Le jour " + dayNumber + " est autorisé pour la réception !");
                            //$("#errorDate").html("✅ Le jour <span style='color: red; font-weight: bold;'>" + jourNom + "</span> est autorisé pour la reception </span>");

                            let a_afficher = ""

                            $.ajax({
                                url: "../config/routes.php",
                                data: {
                                    parms: parms,
                                    idVilleEff: idVilleEff,
                                    daterdveff: daterdveff,
                                    daterdv: daterdv,
                                    etat: "compteurRdv"
                                },
                                dataType: "json",
                                method: "post",

                                success: function(response, status) {

                                    //console.log(response)
                                    let daterdvR = response.daterdv;
                                    let idVilleEffR = response.idVilleEff;
                                    let total = response.total;
                                    let dataR = response.data;
                                    let retourJourReception = response.retourJourReception;



                                    let a_afficher_jour_reception = ' LES JOURS DE RECEPTION POUR LA VILLE : <span style="color:#033f1f ; font-weight: bold;">' + villesRDV + '</span> <br>';
                                    let optionR = "";
                                    let maxRDV = 0;
                                    if (retourJourReception.length > 0) {
                                        $.each(retourJourReception, function(key, value) {
                                            optionR = optionR + value.jour + " - "

                                            maxRDV = value.nbmax
                                        })
                                        optionR = optionR.substring(0, optionR.length - 2);

                                    }
                                    let rdvRestant = 0
                                    if (total != "0") {
                                        total = parseInt(dataR["totalrdv"]);
                                        rdvRestant = maxRDV - total
                                        if (rdvRestant > 0) {
                                            optionAffectationGestionnaire.disabled = false;
                                            a_afficher = "Il y a <span style='color: red; font-weight: bold;'>" + number_format(total) + "</span> RDV(s) programmé(s) a cette date <span style='color:#033f1f ; font-weight: bold;'>" + daterdvR + "</span> pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>"

                                        } else {
                                            customRadio2Checked.checked = true;
                                            $("#afficheuse").html('');
                                            optionAffectationGestionnaire.disabled = true;
                                            a_afficher = "Plus de place disponible à cette date <span style='color:#033f1f ; font-weight: bold;'>" + daterdvR + "</span> pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>"
                                        }
                                    } else {
                                        optionAffectationGestionnaire.disabled = false;
                                        rdvRestant = maxRDV
                                        a_afficher = "Pas de RDV programmé a cette date <span style='color:#033f1f ; font-weight: bold;'>" + daterdvR + "</span> pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>"
                                    }

                                    $("#errorDate").html("Il reste <span style='color: red; font-weight: bold;'>" + rdvRestant + "</span> RDV(s) pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>");
                                    $("#infos-jourReception").html(`<div class="alert alert-warning" role="alert"> <h4>` + a_afficher_jour_reception + `<br> </h4>
                        <span style="color:#033f1f ; font-weight: bold;">` + optionR + `</span></div>`);
                                    $("#infos-compteurRDV").html(`<div class="alert alert-info" role="alert"> ` + a_afficher + `</div>`);

                                },
                                error: function(response, status, etat) {
                                    console.log(response, status, etat)
                                }
                            })

                        } else {
                            let retourJourReception = result.data

                            let a_afficher_jour_reception = ' LES JOURS DE RECEPTION POUR LA VILLE : <span style="color:#033f1f ; font-weight: bold;">' + villesRDV + '</span> <br>';
                            let optionR = "";
                            customRadio2Checked.checked = true;
                            $("#afficheuse").html('');
                            optionAffectationGestionnaire.disabled = true;

                            if (retourJourReception.length > 0) {
                                $.each(retourJourReception, function(key, value) {
                                    optionR = optionR + jours[value] + " - "
                                    maxRDV = value.nbmax
                                })
                                optionR = optionR.substring(0, optionR.length - 2);

                            }
                            alert("❌ Le jour " + jourNom + " n'est pas autorisé pour cette ville.");
                            $("#errorDate").html("❌ Le jour <span style='color: red; font-weight: bold;'>" + jourNom + "</span> n'est pas autorisé pour la reception  </span>");
                            $("#infos-jourReception").html(`<div class="alert alert-warning" role="alert"> <h4>` + a_afficher_jour_reception + `<br> </h4>
                        <span style="color:#033f1f ; font-weight: bold;">` + optionR + `</span></div>`);
                            $("#infos-compteurRDV").html(``);
                        }

                    });
                }

            } else {
                //console.log("❌ Date RDV invalide (date passée)");
                optionAffectationGestionnaire.disabled = true;
                customRadio2Checked.checked = true;
                $("#afficheuse").html('');
                $("#errorDate").html("");
                $("#infos-compteurRDV").text("❌ La date du rendez-vous ne peut pas être antérieure à aujourd’hui.").show();
                // alert("La date du rendez-vous ne peut pas être antérieure à aujourd’hui.");
                //document.getElementById("daterdveff").focus();
                return;
            }

        }


        function getRetourneReceptionJour(idVilleEff, dayNumber, callback) {
            $.ajax({
                url: "../config/routes.php",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "receptionJourRdv"
                },
                dataType: "json",
                method: "POST",
                success: function(response) {
                    // console.log("Réponse reçue :", response);

                    // Vérifie si dayNumber eXiste dans le tableau
                    // let existe = response.includes(dayNumber);
                    let existe = response.map(Number).includes(Number(dayNumber));

                    //console.log("DayNumber :", dayNumber, "→ existe ?", existe);

                    // Appelle le callback avec true ou false
                    callback(existe);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", error);
                    callback(false);
                }
            });
        }

        function getRetourneReceptionJour2(idVilleEff, dayNumber, callback) {
            $.ajax({
                url: "../config/routes.php",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "receptionJourRdv"
                },
                dataType: "json",
                method: "POST",
                success: function(response) {
                    //console.log("Réponse reçue :", response);

                    // Vérifie si dayNumber existe dans la réponse
                    let existe = response.map(Number).includes(Number(dayNumber));

                    // Objet de retour standardisé
                    let retour = {
                        existe: existe,
                        data: response,
                        day: dayNumber
                    };

                    callback(retour);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", error);

                    // Même structure même en cas d’erreur
                    callback({
                        existe: false,
                        data: null,
                        day: dayNumber,
                        error: error
                    });
                }
            });
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

                //console.log(details)
                $("#infos-contrat").html(infos);
            } else {
                $("#infos-contrat").html(" pas d'informations disponible");
            }
        }

        function verifierActivationBouton2() {
            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();

            const toutRempli = objetRDV && objetRDV !== "null" && dateRDV && gestionnaire && etat === "2";

            console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);
            if (gestionnaire != null) {
                // afficher le bouton valider
                $("#valider").prop("disabled", !toutRempli);
            } else {
                // afficher le bouton rejeter
                $("#rejeter").prop("disabled", !toutRempli);
            }
            //$("#valider").prop("disabled", !toutRempli);

        }

        function verifierActivationBouton() {
            const etat = $('input[name="customRadio"]:checked').val();

            const champsOK =
                $('#villesRDV').val() && $('#villesRDV').val() !== "null" &&
                $('#daterdveff').val() &&
                $('#ListeGest').val();

            $("#valider").prop("disabled", !(etat === "2" && champsOK));
            $("#rejeter").prop("disabled", !(etat !== "2" && champsOK));
        }


        function getMenuRejeter() {


            let notif = `
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-12 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner le motif du rejet du RDV <span style="color:red;">*</span> :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"></textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

            let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    Rejeter la prestation
                </button>`;

            $("#afficheuse").html(notif);
            $("#color_button").text("red");
            $("#optionTraitement").html(bouton_rejet);
        }
    </script>

</body>

</html>