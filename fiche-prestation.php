<?php

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");


$plus = "";
$resultat = "";
$afficheuse = FALSE;

$tablo_doc_attendu = array();

if (isset($_COOKIE["id"])) {


    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idprestation = GetParameter::FromArray($_COOKIE, 'id');
    $action = GetParameter::FromArray($_COOKIE, 'action');
    $code = GetParameter::FromArray($_COOKIE, 'code');

    $prestation = $fonction->_getRetournePrestation(" WHERE id='" . $idprestation . "'  ");

    if ($prestation == null) {
        header('Location: liste-prestation.php');
    }

    $prestation = new tbl_prestations($prestation[0]);
    $retour_documents = $fonction->_getListeDocumentPrestation($idprestation);

    $afficheuse = TRUE;
} else {
    header("Location:deconnexion.php");
}

//exit;

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "include/entete.php"; ?>
</head>

<body>

    <?php include "include/header.php";  ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>TRAITEMENT DES DEMANDES</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
                                    <li class="breadcrumb-item " aria-current="page">Liste des demandes</li>
                                    <li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="card-body height-100-p pd-20">
                    <div class="card-box mb-30">
                        <div style="float:left" class="p-2">
                            <button class="btn btn-warning p-2 m-2" onclick="retour()"><i class='fa fa-arrow-left'>Retour</i></button>
                        </div>
                    </div>
                    <div class="card-body radius-12 w-100 p-4" style="border:1px solid gray;background:#033f1f">
                        <h3 class="text-center" style="color:white">Prestation n° <span style="color:#F9B233;"><?= strtoupper($code) ?></span></h3>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-5">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Information sur le demandeur</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid #D3D3D3;background:#D3D3D3;color:#033f1f">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <p><span class="text-color">Nom & Prenoms: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->souscripteur2; ?></span></span></p>
                                        <p><span class="text-color">Date de naissance :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->datenaissance; ?></span></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lieuresidence; ?></span></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">Numero de téléphone :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->cel; ?></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">E-mail :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->email; ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Information sur le contrat via NSIL</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke;color:#033f1f">
                                <div class="row p-2" id="infos-contrat">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Liste des documents joints </h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:bisque;color:white">
                                <div class="text-center mb-2">
                                    <span id="compteur_valides" class="badge bg-success">Validés : 0</span>
                                    <span id="compteur_restants" class="badge bg-danger">Restants : <?= count($retour_documents); ?></span>
                                </div>
                                <?php
                                $i = 0;
                                if ($retour_documents != null) {
                                    for ($i = 0; $i <= count($retour_documents) - 1; $i++) {
                                        $tablo = $retour_documents[$i];

                                        $id_prestation = $tablo["idPrestation"];
                                        $path_doc = trim($tablo["path"]);
                                        $type_doc = trim($tablo["type"]);
                                        $doc_name = trim($tablo["libelle"]);
                                        $ref_doc = trim($tablo["id"]);
                                        $datecreation_doc = trim($tablo["created_at"]);
                                        $documents = Config::URL_PRESTATION_RACINE . $path_doc;

                                        array_push($tablo_doc_attendu,  $ref_doc);

                                        switch ($type_doc) {
                                            case 'RIB':
                                                $nom_document = "RIB";
                                                break;
                                            case 'Police':
                                                $nom_document = "Police du contrat d'assurance";
                                                break;
                                            case 'bulletin':
                                                $nom_document = "Bulletin de souscription";
                                                break;
                                            case 'AttestationPerteContrat':
                                                $nom_document = "Attestation de déclaration de perte";
                                                break;
                                            case 'CNI':
                                                $nom_document = "CNI";
                                                break;
                                            case 'etatPrestation':
                                                $nom_document = "Fiche de demande de prestation";
                                                break;
                                            default:
                                                $nom_document = "Fiche d'identification du numéro de paiement";
                                                break;
                                        }

                                        $values = $id_prestation . "-" . $ref_doc . "-" . $nom_document . "-" . $doc_name;
                                ?>
                                        <div class="d-flex align-items-center mt-3 document-ligne" id="line_<?= $ref_doc ?>">
                                            <input type="text" class="val_doc" name="val_doc" value="<?php echo $values; ?>" hidden>
                                            <input type="text" class="path_doc" name="path_doc" value="<?php echo $documents; ?>" hidden>

                                            <div class="fm-file-box text-success p-2">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0" style="font-size: 12px;">
                                                    <a href="<?= $documents ?>" target="_blank"> <?= $nom_document ?> </a>
                                                </h6>
                                                <p class="mb-0 text-secondary" style="font-size: 0.8em;"> <?= $datecreation_doc ?> </p>
                                            </div>
                                            <button type="button" class="btn btn-warning bx bx-show" data-doc-id="<?= $documents; ?>"
                                                data-path-doc="<?= $documents; ?>" style="background-color:#F9B233 !important;">
                                                <i class="dw dw-eye"> voir</i>
                                            </button>


                                        </div>
                                        <span id="checking_<?= $ref_doc ?>"> </span>
                                <?php
                                    }
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">  Attention ! <strong>Aucun document joint</strong>. </div>';
                                }
                                ?>
                                <input type="text" id="tablo_doc_attendu" name="tablo_doc_attendu" value="<?= json_encode($tablo_doc_attendu)  ?>" hidden>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-7">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Information sur le demandeur </h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke;color:white">
                                <div class="row" style="color:#033f1f!important">
                                    <div class="col-md-6">
                                        <p><span class="text-color">Date demande: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_datedemande; ?></span></span></p>
                                        <p><span class="text-color">Type de prestation: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->typeprestation; ?></span></span></p>
                                        <p><span class="text-color">Code prestation :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->code; ?></span></p>
                                        <p><span class="text-color">Id du contrat :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->idcontrat; ?></span></p>
                                        <p><span class="text-color">Commentaire :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->msgClient; ?></span></p>

                                    </div>
                                    <div class="col-md-6">
                                        <p><span class="text-color">Montant souhaité :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->montantSouhaite ?> FCFA</span></p>
                                        <p><span class="text-color">Moyen de paiement :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_moyenPaiement; ?></p>
                                        <?php
                                        if ($prestation->moyenPaiement == "Virement_Bancaire") {
                                        ?>
                                            <p><span class="text-color">IBAN du compte :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->IBAN; ?></p>

                                        <?php
                                        } else {
                                        ?>
                                            <p><span class="text-color">Operateur :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_Operateur; ?></p>
                                            <p><span class="text-color">Telephone de Paiement :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->telPaiement; ?></p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pd-20">
                    <div class="card-box height-100-p pd-20">
                        <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> traitement de la prestation </h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        <div class="row card-body" style="background: #D3D3D3;" id="infos_perso">
                            <div class="col-md-6">
                                <p style="float:left">Traité le : <span class="text-infos" id="dateheure" style="font-size:18px; font-weight:bold;"><?= $prestation->created_at; ?></span></p>
                                <span class="text-infos" style="color:#033f1f ; color:white !important"></span>
                            </div>
                            <div class="col-md-6">
                                <p style="float:right">Traité par : <span class="text-infos" style="font-size:18px; font-weight:bold;"><?php echo $_SESSION['utilisateur'];  ?></span></p>
                            </div>
                            <input type="text" id="id_prestation" name="id_prestation" value="<?= $prestation->id; ?>" hidden>
                            <input type="text" class="form-control" id="codeprest" name="codeprest" value="<?php echo $prestation->code; ?>" hidden>

                            <input type="text" id="valideur" name="valideur" value="<?php echo $_SESSION['utilisateur'];  ?>" hidden>
                        </div>
                        <div class="row">
                            <div class="row card-body" style="color: #033f1f;">
                                <div class=" offset-md-6 col-md-6">
                                    <div class="form-group">
                                        <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Etat de la demande de prestation :</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                            <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">Rejété la demande</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                            <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">Accepter la demande</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="afficheuse"></div>
                        <div class="row" id="idNumId"></div>

                        <div class="modal-footer" id="footer">
                            <label class="" id="lib"></label>
                            <div id="optionTraitement">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="footer-wrap pd-20 mb-20">
                <?php include "include/footer.php";    ?>
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
                        <div class="card-body">
                            <p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
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
        <script src="vendors/scripts/core.js"></script>
        <script src="vendors/scripts/script.min.js"></script>
        <script src="vendors/scripts/process.js"></script>
        <script src="vendors/scripts/layout-settings.js"></script>
        <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
        <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
        <!-- buttons for Export datatable -->
        <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
        <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
        <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
        <!-- Datatable Setting js -->
        <script src="vendors/scripts/datatable-setting.js"></script>


        <script>
            let document_rejeter = [];
            let document_valider = [];
            let tablo_document = [];

            let etape = <?php echo $prestation->etape ?>;
            let idcontrat = "<?php echo $prestation->idcontrat; ?>";
            var tablo_documents = <?= json_encode($tablo_doc_attendu)  ?>;

            //alert(etape + " " + idcontrat);

            console.log(tablo_documents);

            $(document).ready(function() {

                if (idcontrat != "") {
                    remplirModalEtatComtrat(idcontrat)
                }

                $('input[name="customRadio"][value="' + <?php echo $prestation->etape; ?> + '"]').attr('checked', 'checked');

                /*if (etape == '2') {
                    getMenuValider()
                } else if (etape == '3') {
                    getMenuRejeter()
                }*/


                $('input[name="customRadio"]').change(function() {
                    // Lorsqu'on change de valeur dans la liste
                    var valeur = $(this).val();

                    // On supprime le commentaire et vérifie la valeur
                    if (valeur == '3') {
                        // La prestation est rejetée
                        getMenuRejeter();

                    } else if (valeur == '2') {

                        // La prestation est validée
                        let tablo_attendu = <?php echo json_encode($tablo_doc_attendu); ?>;
                        console.log(tablo_attendu);
                        console.log(document_valider);

                        if (tablo_documents.length == 0) {
                            $("#afficheuse").empty();
                            $('input[name="customRadio"][value="1"]').prop('checked', true); // Remplace attr('checked', 'checked') par prop
                            alert("Désolé, aucun document joint , veuillez rejeter la prestation.");

                        } else {
                            // On vérifie si le nombre de documents validés correspond au nombre attendu
                            if (document_valider.length === tablo_attendu.length) {
                                $("#color_button").text("green");
                                $("#nom_button").text("Valider la prestation");
                                getMenuValider();

                            } else {
                                $("#afficheuse").empty();
                                $('input[name="customRadio"][value="1"]').prop('checked', true); // Remplace attr('checked', 'checked') par prop
                                alert("Désolé, veuillez vérifier que tous les documents joints soient corrects, puis valider la prestation ou cliquer sur rejeter la prestation.");

                            }
                        }

                    } else {
                        // La prestation est en attente
                        alert('La prestation est en attente.');
                        $("#afficheuse").empty();
                        $("#color_button").text("#F9B233");
                        $("#optionTraitement").empty();
                        // Si tu veux désactiver un bouton, tu peux décommenter cette ligne :
                        // $('#envoyer').attr('disabled', 'disabled');
                    }
                });
            })


            $(".bx-show").click(function() {
                let path_document = $(this).data("path-doc");
                let val_doc = $(this).closest('.d-flex').find('.val_doc').val();

                let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;
                $("#document").val(path_document);
                $("#val_doc2").val(val_doc);
                $("#iframeAfficheDocument").html(html);
                $('#modaleAfficheDocument').modal("show");
            });


            $("#modaleAfficheDocument").on("click", "#valid_download", function() {

                let doc = $("#document").val();
                let val_doc = $("#val_doc2").val();

                let tab = val_doc.split('-');
                let id_prestation = tab[0];
                let ref_doc = tab[1];
                let doc_name = tab[2];
                let type_doc = tab[3];

                if (document_valider.includes(ref_doc)) {
                    alert(doc_name + " a déjà été validé");
                    $("#checking_" + ref_doc).html(`<i class="fa fa-check text-warning"> ${doc_name} a déjà été validé</i>`);
                } else {
                    document_valider.push(ref_doc);
                    alert(doc_name + " validé avec succès");

                    $("#checking_" + ref_doc).html(`<i class="fa fa-check text-success"> ${doc_name} a bien été validé</i>`);
                    $("#line_" + ref_doc).closest(".d-flex")
                        .addClass("border border-success bg-light")
                        .fadeOut(2000);
                    //$("#line_" + ref_doc).fadeOut();

                    updateCompteurs(document_valider);
                }
                $('#modaleAfficheDocument').modal("hide");
            });

            $("#modaleAfficheDocument").on("click", "#annuler_download", function() {

                alert("Annulation de la validation");

                let doc = $("#document").val();
                let val_doc = $("#val_doc2").val();

                let tab = val_doc.split('-');
                let id_prestation = tab[0];
                let ref_doc = tab[1];
                let doc_name = tab[2];
                let type_doc = tab[3];

                console.log(val_doc);

                if (!document_valider.includes(ref_doc)) {
                    alert(doc_name + " n'a pas encore été validé");
                    $("#checking_" + ref_doc).html(`<i class="fa fa-times text-danger"> ${doc_name} n'est pas validé</i>`);
                } else {
                    // Retire ref_doc du tableau
                    document_valider = document_valider.filter(item => item !== ref_doc);
                    alert(doc_name + " a été annulé");

                    // Met à jour l'affichage
                    $("#checking_" + ref_doc).html(`<i class="fa fa-undo text-secondary"> ${doc_name} a été annulé</i>`);
                    $("#line_" + ref_doc).closest(".d-flex")
                        .removeClass("border border-success bg-light")
                        .fadeIn();

                    updateCompteurs(document_valider, tablo_documents.length);
                }

                $('#modaleAfficheDocument').modal("hide");

            });


            $("#optionTraitement").on("click", "#valider", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;

                var idcontrat = document.getElementById("idcontrat").value;
                var typeOpe = document.getElementById("typeOpe").value;
                var ListeOpe = document.getElementById("ListeOpe").value;
                var delaiTrait = document.getElementById("delaiTrait").value;


                console.log(id_prestation, codeprest, valideur);
                console.log(idcontrat, typeOpe, ListeOpe, delaiTrait);

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        idprestation: id_prestation,
                        code: codeprest,
                        traiterpar: valideur,
                        idcontrat: idcontrat,
                        typeOpe: typeOpe,
                        ListeOpe: ListeOpe,
                        delaiTrait: delaiTrait,
                        etat: "validerprestation"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,
                    success: function(response, status) {
                        console.log(response)

                        let result = response["result"];
                        let total = response["total"];
                        let resultat = response["data"];
                        //console.log(result)

                        if (result == "SUCCESS") {
                            //console.log(resultat)
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La prestation <span class="text-success">` + codeprest + `</span> a bien été accepté !</h2> </div>`
                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>Desole une erreur est survenue lors de l'acceptation de la prestation <span class="text-danger">` + codeprest + `</span>  !</h2> </div>`
                        }

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                        //   etat =  '-1';
                    }
                })
            })


            $("#optionTraitement").on("click", "#confirmerRejet", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;

                let observation = $('#obervation').val();
                let motifs = $('#motifRejet').val();

                if (!motifs || motifs.length === 0) {
                    alert("Veuillez sélectionner au moins un motif de rejet.");
                    return;
                }
                //console.log(motifs);

                if (motifs.length > 0) {

                    let libMotif = ""

                    motifs.forEach(function(motif) {
                        // Sépare chaque motif sur le symbole "|"
                        // console.log(motif);
                        let parts = motif.split('|');
                        let id = parts[0];
                        let libelle = parts[1];
                        //console.log("ID:", id, "Libellé:", libelle);
                    });
                    $("#a_afficher_1").text(codeprest)
                    $("#a_afficher_2").text(libMotif)


                    $("#idprestation").val(id_prestation)
                    $("#traiterpar").val(valideur)
                    $("#motif").val(motifs)
                    $("#observation").val(observation)

                    $('#confirmation-modal').modal('show')

                } else {
                    alert("Veuillez renseigner le motif de rejet svp !!");
                    document.getElementById("motifRejet").focus();
                    return false;
                }
            })

            $("#validerRejet").click(function(evt) {
                //alert("validerRejet ")

                var idprestation = document.getElementById("idprestation").value;
                var motif = document.getElementById("motif").value;
                var traiterpar = document.getElementById("traiterpar").value;
                var observation = document.getElementById("observation").value;

                //console.log(idprestation, motif, traiterpar, observation);

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        idprestation: idprestation,
                        motif: motif,
                        traiterpar: traiterpar,
                        observation: observation,
                        etat: "confirmerRejet"
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
								<h2>La prestation <span class="text-success">` + code + `</span> a bien été rejetée  !</h2></div>`

                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du rejet de la prestation <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                        }
                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                    }
                })

            })

            $("#annulerRejet").click(function(evt) {
                var idprestation = document.getElementById("idprestation").value;
                var motif = document.getElementById("motif").value;
                var traiterpar = document.getElementById("traiterpar").value;
                $('#confirmation-modal').modal('hide')
            })


            $("#optionTraitement").on("click", "#valider", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;

                var idcontrat = document.getElementById("idcontrat").value;
                var typeOpe = document.getElementById("typeOpe").value;
                var ListeOpe = document.getElementById("ListeOpe").value;
                var delaiTrait = document.getElementById("delaiTrait").value;


                console.log(id_prestation, codeprest, valideur);
                console.log(idcontrat, typeOpe, ListeOpe, delaiTrait);

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        idprestation: id_prestation,
                        code: codeprest,
                        traiterpar: valideur,
                        idcontrat: idcontrat,
                        typeOpe: typeOpe,
                        ListeOpe: ListeOpe,
                        delaiTrait: delaiTrait,
                        etat: "validerprestation"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,
                    success: function(response, status) {
                        console.log(response)

                        let result = response["result"];
                        let total = response["total"];
                        let resultat = response["data"];
                        //console.log(result)

                        if (result == "SUCCESS") {
                            //console.log(resultat)
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La prestation <span class="text-success">` + codeprest + `</span> a bien été accepté !</h2> </div>`
                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>Desole une erreur est survenue lors de l'acceptation de la prestation <span class="text-danger">` + codeprest + `</span>  !</h2> </div>`
                        }

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                        //   etat =  '-1';
                    }
                })
            })

            $("#afficheuse").on("change", "#typeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]
                console.log(val)

                $("#opeType").val(lib)

                $.ajax({
                    url: "https://api.laloyalevie.com/enov/op-type-operation-list",
                    data: {
                        type: key
                    },
                    dataType: "json",
                    method: "post",
                    success: function(response, statut) {
                        console.log(response)
                        var apt = "<option> Choix de l\'operation " + lib + "</option>"

                        $.each(response, function(key, value) {
                            let valueOpe = value.CodeTypeAvenant + '-' + value.DelaiTraitement + ' jours' + '-' + value.MonLibelle
                            apt += '<option  value="' + valueOpe + '">' + value.MonLibelle + '-(' + value.DelaiTraitement + ' jours)' + '</option>'
                        })
                        $("#ListeOpe").html(apt)
                    },
                    error: function(response, statut, err) {
                        console.log(err)
                        console.log(response)
                    }
                })
            })


            $("#afficheuse").on("change", "#ListeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]

                $("#delaiTrait").val(lib)
            })

            $("#closeAfficheDocument").click(function() {

                $('#modaleAfficheDocument').modal('hide')
            })

            $("#retourNotification").click(function() {

                $('#notificationValidation').modal('hide')
                //location.reload();
                location.href = "detail-prestation";

            })


            function retour() {
                window.history.back();
            }

            function getMenuValider() {

                let notif = `
                            <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Verification pour migration NSIL </h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $prestation->idcontrat; ?>" disabled>
                                    </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;">Type d\'opération (<span style="color:red;">*</span>)</label>
                                        <select name="typeOpe" id="typeOpe" class="form-control "  data-rule="required">
                                            <option value="">...</option>
                                            <option value="TECH-Technique">Technique</option>
                                            <option value="AVT-Administratif">Administratif</option>
                                        </select>
                                </div>
                                <div class="form-group col-md-5 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;" >Liste d\'opération <span id="opeType"></span> (<span style="color:red;">*</span>)</label>
                                        <select name="ListeOpe" id="ListeOpe" class="form-control " data-rule="required">
                                            
                                        </select>
                                </div>
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">Délai de traitement :</label>
                                    <input type="text" class="form-control" id="delaiTrait" name="delaiTrait" value="" disabled>
                                </div>
                            </div>
                        `

                let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Accepter la prestation</button>`


                $("#color_button").text(`#033f1f`)
                $("#nom_button").text(`Enregistrer le traitement`)
                $("#afficheuse").html(notif)
                $("#optionTraitement").html(bouton_valider)
            }

            function getMenuRejeter() {


                let notif = `
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-7 col-sm-12" id="afficheuseMotifRejet"></div>
                    <div class="form-group col-md-5 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner vos observations sur le rejet de la demande de prestation :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"> <?php echo $prestation->observationtraitement; ?> </textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

                let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    Rejeter la prestation
                </button>`;

                $("#afficheuse").html(notif);
                $("#color_button").text("red");
                $("#optionTraitement").html(bouton_rejet);

                getListeMotifRejet();
            }


            function getListeMotifRejet() {
                $.ajax({
                    url: "config/routes.php",
                    method: "post",
                    dataType: "json",
                    data: {
                        etat: "listeMotifRejet"
                    },
                    success: function(response) {
                        if (response != '-1') {
                            let html = `<label for="motifRejet" class="col-form-label">Motif de rejet :</label>
                            <select name="motifRejet[]" id="motifRejet" class="form-control" multiple data-msg="Objet" data-rule="required">
                                <option value="">...</option>`;

                            $.each(response, function(indx, data) {
                                let valus = data.code + '|' + data.libelle;
                                html += `<option value="${valus}" id="ob-${indx}">${data.libelle}</option>`;
                            });

                            html += `<option value="99|autres">AUTRE MOTIF</option></select>`;

                            $("#afficheuseMotifRejet").html(html);

                            // Initialisation de Select2
                            $('#motifRejet').select2({
                                placeholder: "Sélectionnez un ou plusieurs motifs",
                                width: '100%'
                            });
                        }
                    },
                    error: function(response) {
                        console.error("Erreur AJAX :", response);
                    }
                });
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
                            numero += `
                            Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span><br>
                            `
                        }
                    })

                    $.each(details, function(indx, data) {

                        infos += `
                        <div class="row w-100">
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nom et prenom  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.nomSous+" "+data.PrenomSous}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Date de naissance : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateNaissance}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;">  ${numero}	</div>
                        </div>	
                        <div class="row  w-100">
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Produit  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.produit}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Capital : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.CapitalSouscrit)}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Prime  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalPrime)}</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Effet Reel  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateEffetReel}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Fin Adhesion  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.FinAdhesion}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Duree du contrat : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DureeCotisationAns}</span></div>
                        </div>						
                        <div class="row  w-100">
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre d'emission  : <span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEmission}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Emission  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEmission)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Encaissment  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEncaissment}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Encaissement  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEncaissement)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreImpayes} </span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalImpayes)} FCFA</span></div>
                        </div>`
                    })

                    console.log(details)
                    $("#infos-contrat").html(infos);
                } else {
                    $("#infos-contrat").html(" pas d'informations disponible");
                }
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

                        if(resultat.error != false){
                            console.log(response)
                        }
                    },
                    error: function(response, status, etat) {
                        resultat = '-1';
                    }
                })
                return resultat
            }

            function updateCompteurs(document_valider, total_documents = 5) {
                let nb_valides = document_valider.length;
                let nb_restants = total_documents - nb_valides;
                $("#compteur_valides").text("Validés : " + nb_valides);
                $("#compteur_restants").text("Restants : " + nb_restants);
            }
        </script>


</body>

</html>