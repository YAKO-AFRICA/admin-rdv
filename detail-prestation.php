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

if (isset($_COOKIE["id"])) {


    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idprestation = GetParameter::FromArray($_COOKIE, 'id');
    $action = GetParameter::FromArray($_COOKIE, 'action');
    $code = GetParameter::FromArray($_COOKIE, 'code');

    $prestation = $fonction->_getRetournePrestation(" WHERE id = '" . $idprestation . "' ");
    if ($prestation == null) {
        header('Location: liste-prestation.php');
    }

    $prestation = new tbl_prestations($prestation[0]);
    $retour_documents = $fonction->_getListeDocumentPrestation(1);

    $afficheuse = TRUE;

    // print_r($prestation);


} else {
    header("Location:deconnexion.php");
}


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
                    <div class="col-md-3">
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
                                        <p><span class="text-color">Residence :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lieuresidence; ?></span></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">Téléphone :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->cel; ?></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">E-mail :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->email; ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
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
                    <div class="col-md-3">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Liste des documents joints </h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:bisque;color:white">

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
                                                <p class="mb-0 text-secondary" style="font-size: 0.6em;"> <?= $datecreation_doc ?> </p>
                                            </div>
                                            <button type="button" class="btn btn-warning bx bx-show" data-doc-id="<?= $documents; ?>"
                                                data-path-doc="<?= $documents; ?>" style="background-color:#F9B233 !important;">
                                                <i class="dw dw-eye"></i>
                                            </button>


                                        </div>
                                        <span id="checking_<?= $ref_doc ?>"> </span>
                                <?php
                                    }
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">  Attention ! <strong>Aucun document joint</strong>. </div>';
                                }
                                ?>

                            </div>
                        </div>

                    </div>
                </div>

                <?php

                if ($prestation->etape != "1") {

                ?>
                    <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                        <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Détails traitement de la prestation </h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        <div class="row">

                            <div class="col-md-5">
                                <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke; color:#033f1f">
                                    <p><span class="text-color">traite le : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->traiterle; ?></span></span></p>
                                    <p><span class="text-color">traite par : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->traiterpar; ?></span></span></p>
                                    <p><span class="text-color">statut : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->lib_statut; ?></span></span></p>
                                    <?php
                                    if ($prestation->etape == "3") {
                                    ?>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke; color:#033f1f">
                                    <?php
                                    if ($prestation->etape == "2") {
                                    ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><span class="text-color">Migration NSIL : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->migrationNsil; ?></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><span class="text-color">Date Migration NSIL : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->migreele; ?></span></p>
                                            </div>
                                            <?php
                                            $detailPrestationNsil = $fonction->_GetDetailsTraitementPrestation($prestation->id);
                                            if ($detailPrestationNsil != null) {
                                            ?>

                                                <div class="col-md-6">
                                                    <p><span class="text-color">libelle Operation : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $detailPrestationNsil->libelleOperation; ?></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="text-color">delai Traitement : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $detailPrestationNsil->delaiTraitement; ?></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="text-color">id courrier NSIL : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $detailPrestationNsil->idTblCourrier; ?></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="text-color">code courrier NSIL : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $detailPrestationNsil->codeCourrier; ?></span></p>
                                                </div>

                                            <?php
                                            }
                                            ?>
                                        </div>


                                    <?php
                                    } elseif ($prestation->etape == "3") {
                                        $ListeMotifRejet = $fonction->_GetListeMotifRejetPrestation($prestation->code, null, true);

                                    ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><span class="text-color">Observations : </span><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $prestation->observationtraitement; ?></span></p>
                                            </div>
                                            <div class="col-md-12">
                                                <p><span class="text-color"> Liste des motifs de rejet de la prestation : </span><br><span class="text-infos" style="font-size:18px; font-weight:bold; color:<?= $prestation->color ?>"><?= $ListeMotifRejet; ?></span></p>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="footer-wrap pd-20 mb-20">
            <?php include "include/footer.php";    ?>
        </div>

        <div class="modal fade" id="notification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-body text-center">
                        <div class="form-group">
                            <h2><span id="a_afficher"></span></h2>
                        </div>
                        <div class="card-body radius-12 w-100">
                            <div class="alert alert-success" role="alert">
                                <h2><span id="a_afficher2"></span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="closeNotif">
                            <button type="button" id="closeNotif" class="btn btn-secondary"
                                data-dismiss="modal">FERMER</button>
                        </div>
                    </div>
                </div>
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

                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                    </div>
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
        $(document).ready(function() {

            $(".bx-show").click(function() {
                let path_document = $(this).data("path-doc");
                let val_doc = $(this).closest('.d-flex').find('.val_doc').val();

                let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;
                $("#document").val(path_document);
                $("#val_doc2").val(val_doc);
                $("#iframeAfficheDocument").html(html);
                $('#modaleAfficheDocument').modal("show");
            });

        });

        function retour() {
            window.history.back();
        }



        $("#ajourner").click(function(evt) {

            var search = $("#search").text()
            var tablo = search.split("|");

            let ref = tablo[0];
            var tabloRef = ref.split("-");
            var idDemande = $("#idDemande").text()

            //alert(idDemande)
            let msg = "Veuillez les raisons de l'ajournement svp !!"
            $("#a_afficherE2").text(msg)
            $("#demande2").text(idDemande)
            $('#echecNotification').modal("show")

        })

        $("#modifier").click(function(evt) {

            var search = $("#search").text()
            var tablo = search.split("|");

            let ref = tablo[0];
            var tabloRef = ref.split("-");
            var idDemande = $("#idDemande").text()

            //alert(idDemande)
            document.cookie = "id=" + idDemande;
            location.href = "demande";


        })


        $("#rechercher").click(function(evt) {

            var search = $("#search").text()
            var tablo = search.split("|");

            let ref = tablo[0];
            var tabloRef = ref.split("-");
            var idDemande = $("#idDemande").text()

            console.log(idDemande);
            console.log(search);
            //console.log(tablo);

            $.ajax({
                url: "config/routes.php",
                data: {
                    params: search,
                    etat: "searchContrat"
                },
                dataType: "json",
                method: "post",
                //async: false,
                success: function(response, status) {
                    console.log(response)
                    etat = response
                    let contact1;
                    console.log(response["data"])
                    resultat = response["data"];

                    console.log(resultat)

                    if (response["result"] == 'SUCCES') {

                        let totalLigne = response["total"];

                        $("#total").text(totalLigne)
                        remplirTablo(resultat, idDemande, search);

                    } else if (response["result"] == 'NULL') {
                        a_afficher = `<div class="alert alert-warning" role="alert">
								<h2>Desolé ! aucun contrat ne correspond a votre recherche </h2> </div>`

                        $("#a_afficher2").html(a_afficher)
                        $('#notification').modal("show")

                    } else {

                        let msg = "Desolé , erreur sur lors de la récupération des données"
                        $("#a_afficherE2").text(msg)
                        $("#demande2").text(idDemande)
                        $('#echecNotification').modal("show")
                    }
                },
                error: function(response, status, etat) {
                    console.log(etat, response)
                    //   etat =  '-1';
                }
            })
        })


        $('div #tabloContrat').on("click", ".generer", function(evt) {

            var data = evt.target.id.split('-');
            var ind = data[1]
            var idcontrat = $("#chk-" + ind).val()
            var idDemande = $("#dem-" + ind).text()
            var prime = $("#prime-" + ind).text()
            var produit = $("#produit-" + ind).text()
            console.log(idcontrat, idDemande, prime, produit);
            genrerCompteDemande(idDemande, idcontrat)
        })


        $("#closeNotif").click(function() {
            $('#notification').modal('hide')
            window.history.back();
        })

        $("#closeEchec").click(function() {
            $('#echecNotification').modal('hide')
            location.reload();
        })



        function remplirTablo(resultat, idDemande, search) {


            let tabloAd = search.split('|')
            console.log(tabloAd);

            let nomAdherant = tabloAd[1] + ' ' + tabloAd[2]
            let infos = ""
            let lignes = ""
            let contratEnCours = 0
            let contratAutre = 0
            let notif = ""
            let nbreCR
            if (resultat && resultat.lenght !== 0) {
                //nbreCR = resultat.lenght;
                $.each(resultat, function(indx, element) {
                    //console.log(indx);
                    if (element.Contact1 == null) contact1 = "";
                    else contact1 = element.Contact1;

                    /*if (element.Etat == "En cours" && element.Etat == "En veille") {*/
                    if (element.Adherent == nomAdherant) {
                        //if (element.Etat != "Arrêté") {


                        contratEnCours += 1;
                        lignes += `
						<tr id="ligne-${indx}">
							<td class="active" >
								<input type="checkbox" class="select-item checkbox" id="chk-${indx}" name="select-item" value="${element.id}" />
							</td>
							<td id="idcontrat">${element.id}</td>
							<td >${element.Adherent}</td>
							<td >${element.DateNaissance}</td>
							<td >${contact1}</td>
							<!--td >${element.Contact2}</td-->
							<td >${element.DateEffet}</td>
							<td id="prime-${indx}" >${parseInt(element.Prime)}</td>
							<td id="produit-${indx}" >${element.Produit}</td>
							<td >${element.Etat}</td>
							<td id="dem-${indx}" hidden >${idDemande}</td>
							<td class="table-plus">
								<button class="btn bx bx-file generer"  name="generer"  id="btn-${indx}" style=" color: aliceblue ; background-color:#F9B233 !important; ">Generer compte</button>
							</td>
						</tr>`
                    } else {
                        contratAutre += 1
                    }

                });
            }

            let entete = `<thead class="table-light">
							<tr>
								<th class="active" class="table-plus ">
									<input type="checkbox" class="select-all checkbox" name="select-all" />
								</th>
								<th>Id proposition</th>
								<th>Adherent</th>
								<th>Date naissance</th>
								<th>Contact1</th>
								<!--th>Contact2</th-->
								<th>Date Effet</th>
								<th>Prime (FCFA)</th>
								<th>Produit</th>
								<th>Etat</th>
								<th></th>
							</tr>
						</thead>`

            let tabloC = `<table id="listeReclams" class="table table-striped data-table-export" style="font-size:8pt;">
							${entete}
						<tbody>	${lignes} </tbody></table>`

            let nissa = `<hr>
										
			<div style="float:right">
										<button class="btn btn-secondary p-2 m-4" name="selected" id="selected" style="background: #033f1f !important;"> Generer /Affecter contrat</button>
									</div>
									<div class="card radius-12 w-100 " >
									${tabloC}
									</div>`



            //notif = ' Contrat en cours : <b>' + contratEnCours + '</b> -  Contrat Suspendu/Arreté : <b>' + contratAutre + '</b>'
            notif = `<div style="color: #033f1f !important;"> Contrat en cours (<span style="color: #F9B233 !important;">` +
                contratEnCours + `</span>)  <br>
			Contrat Suspendu/Arreté (<span style="color: #F9B233 !important;">` + contratAutre + `</span> )</div>`


            $("#noteResultat").html(notif)
            $("#content-tbaLigneDemande").html(lignes);

        }


        function genrerCompteDemande(idDemande, idcontrat) {

            //let etat

            console.log(idDemande + " - " + idcontrat)

            let a_afficher
            let message_error

            $.ajax({
                url: "config/routes.php",
                data: {
                    idDemande: idDemande,
                    idcontrat: idcontrat,
                    etat: "getAddCompte"
                },
                dataType: "json",
                method: "post",
                //async: false,
                success: function(response, status) {
                    console.log(response)

                    if (response != '-1') {
                        result = response["result"];
                        resultat = response["data"];
                        console.log(result)
                        if (result == "SUCCES") {
                            //console.log(resultat)
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>Bravo ! ` + resultat + `</h2> </div>`

                            $("#a_afficher2").html(a_afficher)
                            $('#notification').modal("show")

                        } else {
                            a_afficher = " Désolé , le compte n'a pas été généré !"
                            a_afficher2 = "message erreur : " + resultat
                            $("#a_afficherE").text(a_afficher)
                            $("#a_afficherE2").text(a_afficher2)
                            $('#echecNotification').modal("show")
                        }
                        //alert(a_afficher)
                    } else {
                        let msg = "Désolé , erreur survenue lors de la generation de compte !! "
                        $("#a_afficherE2").text(msg)
                        $('#echecNotification').modal("show")
                    }
                },
                error: function(response, status, etat) {
                    console.log(response)
                }
            })
        }


        function getTraitementGEN() {


            var motifTrait = document.getElementById("motifTrait").value;
            var statutTrait = document.getElementById("statutTrait").value;
            var Commentaire = document.getElementById("Commentaire").value;
            var idDemande = $("#demande2").text()


            if (statutTrait == "") {
                alert("Veuillez renseigner le statut de la generation svp !!");
                document.getElementById("statutTrait").focus();
                return false;
            }
            if (motifTrait == "") {
                alert("Veuillez renseigner le motif svp !!");
                document.getElementById("motifTrait").focus();
                return false;
            }
            //alert(Commentaire + ' ' + statutTrait + '  ' + idDemande)

            $('#echecNotification').modal('hide')

            $.ajax({
                url: "config/routes.php",
                data: {
                    idDemande: idDemande,
                    Commentaire: Commentaire,
                    statutTrait: statutTrait,
                    motifTrait: motifTrait,
                    etat: "getAjourne"
                },
                dataType: "json",
                method: "post",
                //async: false,
                success: function(response, status) {
                    console.log(response)
                    a_afficher = "La demande a bien été  ajournée ! "
                    $("#a_afficher2").text(a_afficher)
                    $('#notification').modal("show")
                },
                error: function(response, status, etat) {
                    console.log(response)
                }
            })

        }



        //column checkbox select all or cancel
        $("input.select-all").click(function() {
            var checked = this.checked;
            $("input.select-item").each(function(index, item) {
                item.checked = checked;
            });
        });

        //check selected items
        $("input.select-item").click(function() {
            var checked = this.checked;
            console.log(checked);
            checkSelected();
        });

        //check is all selected
        function checkSelected() {
            var all = $("input.select-all")[0];
            var total = $("input.select-item").length;
            var len = $("input.select-item:checked:checked").length;
            console.log("total:" + total);
            console.log("len:" + len);
            all.checked = len === total;
        }
        //button select all or cancel
        $("#select-all").click(function() {
            //$('div #tabloContrat').on("click", ".select-all", function(evt) {
            var all = $("input.select-all")[0];
            all.checked = !all.checked
            var checked = all.checked;
            $("input.select-item").each(function(index, item) {
                item.checked = checked;
            });
        });



        /*generer plusieurs contrat correspondant */
        $("#selected").click(function() {

            var items = [];
            $("input.select-item:checked:checked").each(function(index, item) {
                items[index] = item.value;
            });
            if (items.length < 1) {
                //alert("no selected items!!!");
                let msg = "Désolé ! veuillez sélectionner au minimum une ligne et valider ! "
                $("#a_afficherE2").text(msg)
                $('#echecNotification').modal("show")

            } else {
                var values = items.join(',');

                //alert("contrat selectionné " + values);

                var search = $("#search").text()
                var tablo = search.split("|");
                console.log(search);
                //console.log(tablo);

                let ref = tablo[0];
                //var tabloRef = ref.split("-");
                //var idDemande = tabloRef[2]

                var idDemande = ref
                console.log(idDemande);
                console.log(values);
                genrerCompteDemande(idDemande, values)
            }
        });



        function myFunction() {
            var x = document.getElementById("myDIV");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
    </script>


</body>

<!--script>
	var oTable = $('#listeDemande').DataTable({
		order: [
			[0, 'desc']
		],
		buttons: [
			'copy', 'excel', 'pdf'
		]
	});
</script-->

</html>