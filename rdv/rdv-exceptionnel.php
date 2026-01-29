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
                                <h4 class="text-primary font-weight-bold">Rendez vous Exceptionnel</h4>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0">
                                    <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Prise de RDV Exceptionnel</li>
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
                        <h3 style="color:white">Prise de RDV Exceptionnel </h3>
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
                                        <label>ID contrat / N° de police(s) :</label>
                                        <input type="text" class="form-control" id="police" name="police" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Nom & Prénom(s) :</label>
                                        <input type="text" class="form-control" id="nomclient" name="nomclient" disabled>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Date de naissance :</label>
                                        <input type="text" class="form-control" id="datenaissance" name="datenaissance" disabled>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="form-group col-md-3">
                                        <label>Téléphone (<bold style="color: red;"> *</bold>):</label>
                                        <input type="text" class="form-control" id="telephone" name="telephone">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Email (<bold style="color: red;"> *</bold>):</label>
                                        <input type="text" class="form-control" email id="email" name="email">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Lieu de résidence (<bold style="color: red;"> *</bold>) :</label>
                                        <?= $fonction->getLieuResidence("", "") ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Statut du demandeur (<bold style="color: red;"> *</bold>):</label>
                                        <select name="statutDemandeur" id="statutDemandeur" class="form-control" data-rule="required">
                                            <option value="" disabled selected>[selectionnez svp le statut du demandeur]</option>
                                            <option value="souscripteur">Souscripteur</option>
                                            <option value="assure">Assuré</option>
                                            <option value="beneficiaire">Beneficiaire</option>
                                            <option value="autres">Autres</option>
                                        </select>

                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Motif du RDV (<bold style="color: red;"> *</bold>):</label>
                                        <?php echo $fonction->getSelectTypePrestation(" AND impact = '1' "); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV souhaitée (<bold style="color: red;"> *</bold>) :</label>
                                        <input type="date" class="form-control" id="daterdveff" name="daterdveff" onblur="checkDate('1')" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                                        <input type="date" class="form-control" id="daterdv" name="daterdv" onblur="checkDate('1')" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" hidden>

                                        <span id="errorDate" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Ville choisie (<bold style="color: red;"> *</bold>) :</label>
                                        <?= $fonction->getVillesBureau("", "") ?>
                                    </div>

                                    <div class="form-group col-md-3 ">
                                        <label>Gestionnaire (<bold style="color: red;"> *</bold>) :</label>
                                        <select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>
                                    </div>

                                </div>
                            </div>
                            <hr>
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>Rendez-vous Exeptionnel du : <span id="dateheure" class="font-weight-bold"> <?= date('d/m/Y H:i:s') ?> </span></p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <p>Prit par : <span class="font-weight-bold"><?= $_SESSION['utilisateur'] ?></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white text-right">
                                <button type="button" id="retourRDV" name="retourRDV" class="btn btn-secondary" data-dismiss="modal">RETOUR</button>
                                <button type="submit" class="btn btn-success text-white" name="enregistrerRDV" id="enregistrerRDV" style="background: #033f1f">ENREGISTRER RDV</button>

                            </div>
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


    <div class="modal  hide fade in" data-backdrop="static" id="getIdContrat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-header" style="background:#033f1f; color: #fff; font-weight:bold;"> Identification Client pour prise de RDV Exeptionnel</div>
                <div class="modal-body ">
                    <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;"> Identification Client </h4>
                    <hr>
                    <h6 class="text-center p-2" style="color:red ; font-weight:bold;"> Veuillez renseigner l'id contrat ci-dessous : </h6>
                    <div class="row">

                        <div class="form-group col-sm-12 col-md-12">
                            <label for="nomRdv">Veuillez renseigner l'id contrat du client <bold style="color: #F9B233;"> *</label>
                            <input type="text" id="idContrat" name="idContrat" data-rule="required" required placeholder="Veuillez renseigner l'id contrat du client" class="form-control">
                        </div>
                        <small class="text-danger" id="notif_n_mdp"></small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="closeGetIdContrat" name="closeGetIdContrat" class="btn btn-secondary" data-dismiss="modal">RETOUR</button>
                        <button type="submit" class="btn btn-warning text-white" name="verifierIdContrat" id="verifierIdContrat" style="background: #F9B233">RECHERCHER</button>
                        <span id="lib2"></span>
                    </div>
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

            //alert("Connexion en cours ...")

            $('#getIdContrat').modal("show")

            const etape = "1";
            const idcontrat = "2259414";
            const idVilleEff = "2";


            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;
            console.log(dateRDVEffective + " " + objetRDV);

        });


        // Quand la ville change
        $('#villesRDV').change(function() {

            if ($(this).val() === "null") return;
            //const dateRDVEffective = $(this).val();
            const [idvillesRDV, villesRDV] = $(this).val().split(";");
            //console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  ");
            getListeSelectAgentTransformations(idvillesRDV, villesRDV);
        });

        // Quand la date change
        $('#daterdveff').change(function() {

            const dateRDVEffective = $(this).val();
            const objetRDV = $('#villesRDV').val();
            if (objetRDV === "null") return;

            const [idvillesRDV, villesRDV] = objetRDV.split(";");
            //console.log("Nouvelle date RDV effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  " + dateRDVEffective);
        });


        $("#closeGetIdContrat").click(function(evt) {
            window.history.back();
        })

        $("#verifierIdContrat").click(function(evt) {

            var idContrat = document.getElementById("idContrat").value;

            if (idContrat == "") {
                alert("Veuillez renseigner l'id contrat svp !!");
                document.getElementById("idContrat").focus();
                return false;
            }

            alert("Recherchez " + idContrat + ", en cours ...");
            let retour = getAPIVerificationProfil(idContrat);

            if (retour) {
                //console.log(retour.error);

                if (retour.error === true) {
                    alert("Contrat non trouvé !!");
                } else {

                    console.log(retour);
                    let details = retour["details"]
                    let confirmer = retour['regle']
                    let enc = retour['enc']
                    let nonRegle = enc["nonRegle"]
                    let contactsPersonne = retour["contactsPersonne"]

                    if (details.length > 0) {

                        $.each(details, function(indx, data) {

                            let prime = number_format(data.TotalPrime, 2, ',', ' ');
                            let capital = number_format(data.CapitalSouscrit, 2, ',', ' ');

                            $('#getIdContrat').modal("hide")
                            $("#nomclient").val(data.nomSous + " " + data.PrenomSous ?? "")
                            $("#datenaissance").val(data.DateNaissance ?? "")
                            $("#police").val(data.IdProposition ?? idContrat)
                        })
                    } else {
                        alert("Contrat non trouvé !!");
                    }
                }

            } else {
                alert("Contrat non trouvé !!");
            }

        })


        $("#enregistrerRDV").click(function(evt) {

            var idContrat = document.getElementById("idContrat").value;
            var nomclient = document.getElementById("nomclient").value;
            var datenaissance = document.getElementById("datenaissance").value;

            var telephone = document.getElementById("telephone").value;
            var email = document.getElementById("email").value;
            var statutDemandeur = document.getElementById("statutDemandeur").value;
            var typePrestation = document.getElementById("typePrestation").value;
            var daterdveff = document.getElementById("daterdveff").value;
            var villesRDV = document.getElementById("villesRDV").value;
            var ListeGest = document.getElementById("ListeGest").value;
            var lieuResidence = document.getElementById("lieuResidence").value;

            if (telephone == "" || telephone.length < '10') {
                alert("Veuillez renseigner le telephone du demandeur svp !!");
                document.getElementById("telephone").focus();
                return false;
            }

            if (email == "") {
                alert("Veuillez renseigner l'email du demandeur svp !!");
                document.getElementById("email").focus();
                return false;
            }

            if (statutDemandeur == "") {
                alert("Veuillez renseigner le statut du demandeur  svp !!");
                document.getElementById("statutDemandeur").focus();
                return false;
            }

            if (lieuResidence == "") {
                alert("Veuillez renseigner le lieu de residence du demandeur  svp !!");
                document.getElementById("lieuResidence").focus();
                return false;
            }

            if (typePrestation == "") {
                alert("Veuillez renseigner le motif du RDV  svp !!");
                document.getElementById("typePrestation").focus();
                return false;
            }

            if (daterdveff == "") {
                alert("Veuillez renseigner la date du RDV  svp !!");
                document.getElementById("daterdveff").focus();
                return false;
            }

            if (villesRDV == "") {
                alert("Veuillez renseigner la ville du RDV  svp !!");
                document.getElementById("villesRDV").focus();
                return false;
            }

            if (ListeGest == "") {
                alert("Veuillez renseigner le gestionnaire du RDV  svp !!");
                document.getElementById("ListeGest").focus();
                return false;
            }

            if (!checkEmail(email)) {
                alert("Veuillez renseigner une adresse email valide svp !!");
                document.getElementById("email").focus();
                return false;

            }

            alert("Enregistrement en cours ...");

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = ListeGest.split("|");

            $.ajax({
                url: "../config/routes.php",
                data: {
                    idContrat: idContrat,
                    telephone: telephone,
                    email: email,
                    lieuResidence: lieuResidence,
                    statutDemandeur: statutDemandeur,
                    typePrestation: typePrestation,
                    daterdveff: daterdveff,
                    villesRDV: villesRDV,
                    ListeGest: ListeGest,
                    nomclient: nomclient,
                    datenaissance: datenaissance,
                    etat: "priseRdvExceptionnel"

                },
                dataType: "json",
                method: "post",
                success: function(response, status) {

                    console.log(response);
                    let a_afficher = ''

                    if (response != "-1") {

                        a_afficher = `
                        <div class="alert alert-success" role="alert" style="text-align: center; font-size: 18px ; color: #033f1f; font-weight: bold">
                            Le rdv n° ${response} a bien été pris et transmis au gestionnaire ${nomgestionnaire} pour reception le ${daterdveff} à ${villesGestionnaire}
                        </div>`;

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>Desole un rdv est déjà en cours pour  <span class="text-danger">` + idContrat + `</span> !</h2> </div>`
                    }

                    $("#msgEchec").html(a_afficher)
                    $('#notificationValidation').modal("show")
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });

            //$('#enregistrementRDV').modal("hide")
            //location.href = "detail-rdv";

        })

        $("#afficheuse").on("change", "#ListeGest", function(evt) {

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

        function checkEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    </script>

</body>

</html>