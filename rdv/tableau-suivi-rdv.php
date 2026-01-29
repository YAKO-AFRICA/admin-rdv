<?php

use PSpell\Config;

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("../autoload.php");

$mois = $fonction->retourneMoisCourant();
$tabSemaine = $fonction->retourneSemaineCourante();
// print_r($tabSemaine);
// //exit;
// print_r($mois);

$tabloService = array("rdv" => "rdv", "prestation" => "prestation", "sinistre" => "sinistre");

$plus = "";
$afficheuse = false;

if (isset($_REQUEST['filtreliste'])) {

    $retourPlus = $fonction->getFiltreuseRDV();
    $filtre = $retourPlus["filtre"];
    $libelle = $retourPlus["libelle"];
    //echo $filtre; exit;
    if ($filtre) {
        list(, $conditions) = explode('AND', $filtre, 2);
        $plus = " WHERE $conditions ";
    }
} else {
    // je veux les donnees du mois en cours
    $plus = " WHERE YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE()) AND MONTH(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = MONTH(CURDATE()) ";
    $libelle = "RDV du mois en cours";
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "../include/entete.php"; ?>

    <style>
        .stat-box strong {
            font-size: 18px;
        }

        .badge-auto {
            padding: 4px 8px;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
        }

        .table td {
            font-size: 13px;
        }

        .header-title {
            background-color: #033f1f;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 15px;
        }
    </style>
</head>

<body>

    <?php include "../include/header.php"; ?>

    <!-- ================= PRELOADER ================= -->
    <!-- <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Chargement...</div>
        </div>
    </div> -->
    <!-- ============================================== -->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>TABLEAU SUIVI</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tableau suivi</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <input type="hidden" id="afficheuse" name="afficheuse" value="<?php echo $afficheuse; ?>" hidden />
            <input type="hidden" id="service" name="service" value="rdv" hidden />
            <input type="hidden" id="filtreuse" name="filtreuse" value="<?php echo $plus; ?>" hidden />
            <input type="hidden" id="libelleFiltre" name="libelleFiltre" value="<?php echo $libelle; ?>" hidden />

            <i class="icon-copy ion-navicon-round" type="submit" onclick="myFunction()" title="FILTRE">FILTRE</i>
            <div class="card-box mb-10" id="myDIV">
                <div class="card-body">
                    <!-- <form method="POST" id="filtreForm"> -->
                    <div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;" id="filtreForm">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <fieldset class="border rounded p-3">
                                    <legend class="w-auto px-2 font-weight-bold">
                                        Filtrer sur la date RDV
                                    </legend>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="rdvLe" class="form-label">Date début ( <span style="color:red;">*</span> )</label>
                                                    <input type="date" class="form-control" id="rdvLe" name="rdvLe" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="rdvAu" class="form-label">Date fin ( <span style="color:red;">*</span> )</label>
                                                    <input type="date" class="form-control" id="rdvAu" name="rdvAu" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="traiterLe" class="form-label">Date début traitement </label>
                                                    <input type="date" class="form-control" id="traiterLe" name="traiterLe">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="traiterAu" class="form-label">Date fin traitement</label>
                                                    <input type="date" class="form-control" id="traiterAu" name="traiterAu">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <fieldset class="border rounded p-3">
                                    <legend class="w-auto px-2 font-weight-bold">
                                        Filtrer sur statut du RDV / motif du RDV
                                    </legend>

                                    <div class="row g-3">
                                        <div class="col-md-6 form-group">
                                            <h6 style="color: #033f1f !important;">Statut RDV</h6>
                                            <?php echo $fonction->getSelectTypeEtapeRDV(); ?>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <h6 style="color: #033f1f !important;">Motif RDV</h6>
                                            <?php echo $fonction->getSelectTypeRDVFiltre(); ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <fieldset class="border rounded p-3">
                                    <legend class="w-auto px-2 font-weight-bold">
                                        Filtrer sur Ville Reception / Gestionnaire Transformation
                                    </legend>

                                    <div class="row g-3">
                                        <div class="col-md-6 form-group">
                                            <h6 style="color: #033f1f !important;">Ville RDV</h6>
                                            <?= $fonction->getVillesBureau("", "") ?>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <h6 style="color: #033f1f !important;"> Gestionnaire </h6>
                                            <select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" id="footer">
                        <button type="submit" name="filtreliste" id="filtreliste" class="btn btn-secondary" style="background:#033f1f; color: white" onclick="getRechercheTABLEAU()">RECHERCHER</button>
                    </div>
                    <!-- </form> -->
                </div>

            </div>
            <hr>

            <div class="card-box mb-30" id="resultatTableauSuivi">
                <div class="pd-20 text-center">
                    <h4 style="color:#033f1f;">Statistique des RDV </h4>

                    <p style="color:#033f1f; font-weight: bold">
                    <h6><span id="libelleFiltreAffiche"></span> (<span style="color:#F9B233;" id="totalResultat"></span>) </h6>
                    </p>
                </div>
                <div class="card-body pb-20 radius-12 w-100 p-4">

                    <div class="card-body pb-20 radius-12 w-100 p-4">
                        <div class="mt-2">
                            <button class="btn btn-sm" style="background:#033f1f; color:white; text-decoration:none;" id="telechargerExcel">
                                Telecharger le rapport Excel
                            </button>
                        </div>
                    </div>
                    <div class="bg-white pd-20 card-box mb-30">
                        <div id="afficheuseEtat">
                        </div>
                    </div>
                    <hr>
                    <div class="bg-white pd-20 card-box mb-30">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique par Delai de Rendez-vous </h4>
                        <div class="row mb-4">
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseDelai"></div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                <div id="chart7"></div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="bg-white pd-20 card-box mb-30">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Rendez-vous par Motif</h4>
                        <div class="row mb-4">
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseMotif">
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6 col-sm-12 mb-3">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="chartMotif"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="bg-white pd-20 card-box mb-30">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Rendez-vous par Ville</h4>
                        <div class="row mb-4">
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="chartVilles"></div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseVilles">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="bg-white pd-20 card-box mb-30">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Rendez-vous par Gestionnaire</h4>
                        <div class="row mb-4">
                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="chartRDVGestionnaire"></div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseRDVGestionnaire">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>



    <?php include "../include/footer.php"; ?>


    <div class="modal fade" id="confirmationSuivi-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center font-18">
                    <h4 class="padding-top-30 mb-30 weight-500">
                        Voulez vous generer le Tableau de Suivi ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                    </h4>
                    <p class="text-wrap text-muted">
                    <div class="card-body p-1 rounded " style="background:#f8f9fa;" id="messageConfirm">
                    </div>
                    <div class="p-1 rounded" style="background:#f8f9fa;">
                        <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                        <span id="a_afficher_2" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                        <span id="a_afficher_3" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                        <span id="a_afficher_4" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                        <span id="a_afficher_5" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                        <span id="a_afficher_6" style="color:#033f1f!important; font-weight:bold;"> </span><br>
                    </div>
                    </p>
                    <input type="text" id="rdvLeC" name="rdvLeC" hidden>
                    <input type="text" id="rdvAuC" name="rdvAuC" hidden>
                    <input type="text" id="traiterLeC" name="traiterLeC" hidden>
                    <input type="text" id="traiterAuC" name="traiterAuC" hidden>
                    <input type="text" id="etaperdvC" name="etaperdvC" hidden>
                    <input type="text" id="typerdvC" name="typerdvC" hidden>
                    <input type="text" id="villesRDVC" name="villesRDVC" hidden>
                    <input type="text" id="ListeGestC" name="ListeGestC" hidden>



                    <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                        <div class="col-6">
                            <button type="button" id="annulerRejet" name="annulerRejet" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                            NON
                        </div>
                        <div class="col-6">
                            <button type="button" id="confirmerTableauSuivi" name="confirmerTableauSuivi" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
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
    <script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>
    <script src="../src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
    <script src="../src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="../src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
    <script src="../src/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
    <script src="../src/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../vendors/scripts/dashboard2.js"></script>

    <script src="../src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="../src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
    <script src="../vendors/scripts/highchart-setting.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>



    <!-- <script src="include/fonction.js"></script> -->

    <script>
        const colors = [
            "#3b82f6", "#ef4444", "#22c55e", "#eab308", "#a855f7", "#14b8a6", "#f97316",
            "#10b981", "#6366f1", "#84cc16", "#f43f5e", "#0ea5e9", "#475569", "#d946ef",
            "#059669", "#941010ff", "#7c3aed", "#be123c", "#38bdf8", "#4ade80", "#facc15",
            "#fb923c", "#1e40af", "#6b7280"
        ];

        let resultatRecherche = [];

        // ====================== INIT ======================
        $(document).ready(function() {
            $("#resultatTableauSuivi").hide();

            const objetRDV = $("#villesRDV").val();
            if (objetRDV && objetRDV !== "null") {
                const [idvillesRDV, villesRDV] = objetRDV.split(";");
                getListeSelectAgentTransformations(idvillesRDV, villesRDV);
            }
        });

        // ====================== CHANGEMENT VILLE ======================
        $("#villesRDV").change(function() {
            if ($(this).val() === "null") return;
            const [idvillesRDV, villesRDV] = $(this).val().split(";");
            getListeSelectAgentTransformations(idvillesRDV, villesRDV);
        });

        // ====================== EXPORT EXCEL ======================
        $('#telechargerExcel').click(function() {
            if (resultatRecherche.length === 0 || resultatRecherche == "-1") {
                alert("Aucune donnée à exporter !");
                return;
            }

            const date = new Date();
            const nomFichier = `tableau-suivi-rdv-${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}-${date.getHours()}${date.getMinutes()}${date.getSeconds()}.xlsx`;
            exportExcelFormat(resultatRecherche, nomFichier);
        });




        $("#confirmerTableauSuivi").click(function(evt) {
            console.log("confirmerTableauSuivi ")

            var rdvLe = document.getElementById("rdvLeC").value;
            var rdvAu = document.getElementById("rdvAuC").value;
            var traiterLe = document.getElementById("traiterLeC").value;
            var traiterAu = document.getElementById("traiterAuC").value;
            var etaperdv = document.getElementById("etaperdvC").value;
            var typerdv = document.getElementById("typerdvC").value;
            var villesRDV = document.getElementById("villesRDVC").value;
            var ListeGest = document.getElementById("ListeGestC").value;

            console.log(rdvLe, rdvAu, traiterLe, traiterAu, etaperdv, typerdv, villesRDV, ListeGest);

            $.ajax({
                url: "../config/routes.php",
                data: {
                    rdvLe: rdvLe,
                    rdvAu: rdvAu,
                    traiterLe: traiterLe,
                    traiterAu: traiterAu,
                    etaperdv: etaperdv,
                    typerdv: typerdv,
                    villesRDV: villesRDV,
                    ListeGest: ListeGest,
                    etat: "tableauSuiviConfirmation"
                },
                dataType: "json",
                method: "post",
                success: function(response, status) {

                    console.log(response);
                    if (response != "-1" || response.length > 0) {

                        $("#myDIV").hide();
                        $("#totalResultat").html(response.length);

                        const colonnes = ['etat', 'motifrdv', 'nomgestionnaire', 'villeEffective', 'villes', 'idCourrier'];
                        const stats = getStatsGenerales(response, colonnes);
                        const statsDelai = getStatsDelaiRDV(response, "daterdveff");

                        const tabloEtat = stats['etat'];
                        const tabloMotif = stats['motifrdv'];
                        const tabloNomGestionnaire = stats['nomgestionnaire'];
                        const tabloVilleEffective = stats['villeEffective'];
                        const tabloVilles = stats['villes'];
                        const tabloCourrier = stats['idCourrier'];

                        console.log(tabloNomGestionnaire);
                        console.log(tabloCourrier);
                        afficheuseEtat(tabloEtat);
                        afficheuseDelaiRDV(statsDelai);
                        // afficheuseMotif(tabloMotif, colors);
                        // afficheuseVilles(tabloVilles, colors);
                        // afficheuseRDVGestionnaire(tabloNomGestionnaire, colors);
                    } else {

                        alert("dddddddddddddddd")
                        let a_afficher = `
                        <div class="alert alert-danger" role="alert">
                           desolé 
                        </div>`;


                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")
                    }

                    //verifierActivationBouton(); // Vérifie après chargement
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });

        })


        // ====================== MODAL CONFIRMATION ======================
        function getRechercheTABLEAU() {
            const rdvLe = $("#rdvLe").val();
            const rdvAu = $("#rdvAu").val();
            const traiterLe = $("#traiterLe").val();
            const traiterAu = $("#traiterAu").val();
            const etaperdv = $("#etaperdv").val();
            const typerdv = $("#typerdv").val();
            const villesRDV = $("#villesRDV").val();
            const ListeGest = $("#ListeGest").val();

            if (!rdvLe && !rdvAu) {
                alert("Veuillez renseigner la date du RDV !");
                $("#rdvLe").focus();
                return false;
            }

            let obs = {
                observations1: rdvLe && rdvAu ? `Date RDV : ${rdvLe} - ${rdvAu}` : `Date RDV : ${rdvLe || rdvAu}`,
                observations2: traiterLe && traiterAu ? `Date Traitement RDV : ${traiterLe} - ${traiterAu}` : `Date Traitement RDV : ${traiterLe || traiterAu}`,
                observations3: etaperdv ? `Etat RDV : ${etaperdv.split(";")[1]}` : "",
                observations4: typerdv ? `Motif RDV : ${typerdv.split(";")[1]}` : "",
                observations5: villesRDV ? `Ville RDV : ${villesRDV.split(";")[1]}` : "",
                observations6: ListeGest ? `Gestionnaire RDV : ${ListeGest.split("|")[1]}` : ""
            };

            // Remplir modal
            $("#rdvLeC").val(rdvLe);
            $("#rdvAuC").val(rdvAu);
            $("#traiterLeC").val(traiterLe);
            $("#traiterAuC").val(traiterAu);
            $("#etaperdvC").val(etaperdv);
            $("#typerdvC").val(typerdv);
            $("#villesRDVC").val(villesRDV);
            $("#ListeGestC").val(ListeGest);

            $("#a_afficher_1").text(obs.observations1);
            $("#a_afficher_2").text(obs.observations2);
            $("#a_afficher_3").text(obs.observations3);
            $("#a_afficher_4").text(obs.observations4);
            $("#a_afficher_5").text(obs.observations5);
            $("#a_afficher_6").text(obs.observations6);

            $('#confirmationSuivi-modal').modal('show');
        }


        // ====================== AJAX AGENTS ======================
        function getListeSelectAgentTransformations(idVilleEff, villesRDV) {
            $.ajax({
                url: "../config/routes.php",
                method: "post",
                dataType: "json",
                data: {
                    idVilleEff,
                    etat: "afficherGestionnaire"
                },
                success: function(response) {
                    let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;
                    if (response != "-1") {
                        response.forEach((data, idx) => {
                            html += `<option value="${data.id}|${data.gestionnairenom}|${idVilleEff}|${villesRDV}" id="ob-${idx}">${data.gestionnairenom}</option>`;
                        });
                    } else {
                        html = `<option value="">[Aucun agent pour ${villesRDV}]</option>`;
                    }
                    $("#ListeGest").html(html);
                },
                error: function(err) {
                    console.error(err);
                }
            });
        }

        function myFunction() {
            var x = document.getElementById("myDIV");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }


        function retour() {
            window.history.back();
        }




        function exportExcelFormat(tablo, fileName = "export.xlsx") {

            const ws = XLSX.utils.json_to_sheet(tablo);

            // Largeurs automatiques
            ws['!cols'] = Object.keys(tablo[0]).map(col => ({
                wch: 20
            }));

            // Style en-têtes
            Object.keys(tablo[0]).forEach((k, i) => {
                let cell = ws[XLSX.utils.encode_cell({
                    r: 0,
                    c: i
                })];
                cell.s = {
                    font: {
                        bold: true,
                        color: {
                            rgb: "FFFFFF"
                        }
                    },
                    fill: {
                        fgColor: {
                            rgb: "4F81BD"
                        }
                    },
                    alignment: {
                        horizontal: "center"
                    }
                };
            });

            // Bordures
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r; ++R) {
                for (let C = range.s.c; C <= range.e.c; ++C) {

                    let cellAddr = XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    });
                    let cell = ws[cellAddr];
                    if (!cell) continue;

                    cell.s = cell.s || {};
                    cell.s.border = {
                        top: {
                            style: "thin",
                            color: {
                                rgb: "000000"
                            }
                        },
                        bottom: {
                            style: "thin",
                            color: {
                                rgb: "000000"
                            }
                        },
                        left: {
                            style: "thin",
                            color: {
                                rgb: "000000"
                            }
                        },
                        right: {
                            style: "thin",
                            color: {
                                rgb: "000000"
                            }
                        }
                    };
                }
            }

            // Workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Feuille1");
            XLSX.writeFile(wb, fileName);
        }

        function getStatsGenerales(rows, colonnes) {
            const stats = {};
            stats.total = rows.length;

            // Initialiser les clés pour chaque colonne
            colonnes.forEach(col => {
                stats[col] = {};
            });

            // Parcourir les lignes
            rows.forEach(row => {
                colonnes.forEach(col => {
                    let val = row[col] ?? null;
                    if (val === null) val = "NON RENSEIGNÉ";

                    if (!stats[col][val]) {
                        stats[col][val] = 0;
                    }
                    stats[col][val]++;
                });
            });
            return stats;
        }

        function formGraphEtat(valueEtat) {
            $(".dial2").knob();
            $({
                animatedVal: 0
            }).animate({
                animatedVal: valueEtat
            }, {
                duration: 3000,
                easing: "swing",
                step: function() {
                    $(".dial2").val(Math.ceil(this.animatedVal)).trigger("change");
                }
            });
        }

        function afficheuseVilles(tabloVilles, colors) {
            //console.log(tabloVilles);
            let optionVilles = ``;
            let tablo_graph = [];
            let tablo_color = [];
            let idcolor = 0;

            $.each(tabloVilles, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                tablo_color.push(colors[idcolor]);
                optionVilles += `<tr>
                                    <td>${indx}</td>
                                    <td><span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px">${data}</span></td>
                                </tr>`;
                idcolor++;
            });

            //console.log(tablo_graph);
            let htmlVilles = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par ville :</h5>
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionVilles + `
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseVilles").html(htmlVilles);
            // chart 5
            Highcharts.chart('chartVilles', {
                colors: tablo_color,
                title: {
                    text: 'Statistiques par ville'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });

        }

        function afficheuseMotif(tabloMotif, colors) {
            //console.log(tabloMotif);
            let optionMotif = ``;
            let tablo_graph = [];
            let tablo_color = [];
            let idcolor = 0;
            //let colors = ["red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow", "red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow"];
            $.each(tabloMotif, function(indx, data) {

                //console.log(indx);
                tablo_graph.push([indx, data, false], );
                tablo_color.push(colors[idcolor]);
                optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                idcolor++;
            });

            let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Motif :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionMotif + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseMotif").html(htmlMotif);

            if (tablo_graph.length == 0) {
                tablo_graph.push(["Aucun", 1, false], );
            } else {
                Highcharts.chart('chartMotif', {
                    colors: tablo_color,
                    title: {
                        text: 'Statistiques par Motif'
                    },
                    xAxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    series: [{
                        type: 'pie',
                        allowPointSelect: true,
                        keys: ['name', 'y', 'selected', 'sliced'],
                        data: tablo_graph,
                        showInLegend: true
                    }]
                });
            }


        }

        function afficheuseEtat(tabloEtat) {

            const tablo_statut_rdv = {
                "1": {
                    lib_statut: "En attente",
                    libelle: "En attente",
                    statut_traitement: "1",
                    color_statut: "badge badge-secondary",
                    color: "gray",
                    url: "liste-rdv-attente",
                    icone: "micon dw dw-edit"
                },

                "2": {
                    lib_statut: "Transmis",
                    libelle: "TRANSMIS",
                    statut_traitement: "2",
                    color_statut: "badge badge-secondary",
                    color: "blue",
                    url: "liste-rdv-transmis",
                    icone: "micon fa fa-forward fa-2x"
                },

                "0": {
                    lib_statut: "Rejete",
                    libelle: "REJETE",
                    statut_traitement: "0",
                    color_statut: "badge badge-danger",
                    color: "red",
                    url: "",
                    icone: "micon fa fa-close"
                },

                "3": {
                    lib_statut: "Traiter",
                    libelle: "TRAITER",
                    statut_traitement: "3",
                    color_statut: "badge badge-success",
                    color: "#033f1f",
                    url: "liste-rdv-traite",
                    icone: "micon fa fa-check"
                },

                "-1": {
                    lib_statut: "Saisie inachevée",
                    libelle: "SAISIE INACHEVEE",
                    statut_traitement: "-1",
                    color_statut: "badge badge-dark",
                    color: "black",
                    url: "liste-rdv-rejet",
                    icone: "micon fa fa-close"
                }
            };

            let optionEtat = ``;

            // ---- CALCUL TOTAL ----
            let total = 0;
            $.each(tabloEtat, function(indx, data) {
                total += parseInt(data);
            });

            // ---- CARTES INDIVIDUELLES ----
            $.each(tabloEtat, function(indx, data) {
                let valeurDDD = tablo_statut_rdv[indx] ?? 0;
                //console.log(valeurDDD);
                optionEtat += `
                    <div class="col-xl-3 mb-30">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:${valeurDDD.color}; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">	</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white"> ${data}</div>
										<div class="weight-600 font-14">RDV ${valeurDDD.libelle}</div>
									</div>
								</div>
							</div>
						</div>
                    `;
            });
            // ---- INJECTION HTML ----
            $("#afficheuseEtat").html(`<div class="row mb-4"> ${optionEtat} </div>`);
        }

        // ====================== AFFICHAGE ======================
        function afficheuseEtat222(tabloEtat) {
            const statut = {
                "1": {
                    libelle: "En attente",
                    color: "gray"
                },
                "2": {
                    libelle: "Transmis",
                    color: "blue"
                },
                "0": {
                    libelle: "Rejeté",
                    color: "red"
                },
                "3": {
                    libelle: "Traité",
                    color: "#033f1f"
                },
                "-1": {
                    libelle: "Saisie inachevée",
                    color: "black"
                }
            };
            let html = "";
            for (const key in tabloEtat) {
                const data = tabloEtat[key];
                const info = statut[key] || {
                    libelle: key,
                    color: "gray"
                };
                html += `<div class="col-xl-3 mb-30"><div class="card-box widget-style1 text-white" style="background-color:${info.color}; font-weight:bold;">
                            <div class="widget-data">
                                <div class="h4 mb-0 text-white">${data}</div>
                                <div class="weight-600 font-14">RDV ${info.libelle}</div>
                            </div>
                        </div></div>`;
            }
            $("#afficheuseEtat").html(`<div class="row mb-4">${html}</div>`);
        }

        function getStatsDelaiRDV(rows, colonneDate) {
            const stats = {};

            rows.forEach(row => {
                const delai = getDelaiRDV(row[colonneDate]);
                const etat = delai.etat;

                // Si l'état n'existe pas encore, on le crée
                if (!stats[etat]) {
                    stats[etat] = {
                        total: 0,
                        couleur: delai.couleur,
                        badge: delai.badge,
                        libelle: delai.libelle,
                        jours: [], // liste des jours pour cet état
                        lignes: [] // si tu veux lister les lignes associées
                    };
                }

                stats[etat].total++;
                stats[etat].jours.push(delai.jours);
                stats[etat].lignes.push({
                    ...row,
                    delai: delai // on ajoute toutes les infos du délai
                });
            });

            return stats;
        }

        function getDelaiRDV(dateRDV) {
            // Convertir d/m/Y → Y-m-d
            if (dateRDV && dateRDV.includes("/")) {
                const [jour, mois, annee] = dateRDV.split("/");
                dateRDV = `${annee}-${mois}-${jour}`;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const rdv = new Date(dateRDV);
            if (isNaN(rdv.getTime())) {
                return {
                    etat: "indisponible",
                    couleur: "gray",
                    badge: "",
                    libelle: "Date non disponible",
                    jours: null
                };
            }

            rdv.setHours(0, 0, 0, 0);

            const diffTime = rdv - today;
            const jours = Math.round(diffTime / (1000 * 60 * 60 * 24));

            // RDV EXPIRÉ
            if (jours < 0) {
                return {
                    etat: "expire",
                    couleur: "red",
                    badge: "badge badge-danger",
                    libelle: `Délai expiré depuis ${Math.abs(jours)} jour(s)`,
                    jours: Math.abs(jours)
                };
            }

            // RDV AUJOURD'HUI
            if (jours === 0) {
                return {
                    etat: "ok",
                    couleur: "#f39c12",
                    badge: "badge badge-warning",
                    libelle: "Aujourd’hui",
                    jours: 0
                };
            }

            // RDV À VENIR
            return {
                etat: "prochain",
                couleur: "#033f1f",
                badge: "badge badge-success",
                libelle: `${jours} jour(s) restant(s)`,
                jours: jours
            };
        }

        function afficheuseDelaiRDV(tabloMotif) {

            let optionMotif = ``;
            let tablo_graph = [];

            $.each(tabloMotif, function(indx, data) {

                // Renommage des clés
                if (indx == "ok") {
                    indx = "Aujourd’hui";
                } else if (indx == "expire") {
                    indx = "Délai expiré";
                } else if (indx == "prochain") {
                    indx = "À venir";
                }

                // --- TABLEAU HTML ---
                optionMotif += `
                    <tr>
                        <td>${indx}</td>
                        <td>
                            <span class="badge badge-pill" 
                                style="background:${data.couleur};color:white;font-size:12px">
                                ${data.total}
                            </span>
                        </td>
                    </tr>
                `;

                // --- GRAPHIQUE HIGHCHARTS (mode objet obligatoire) ---
                tablo_graph.push({
                    name: indx,
                    y: data.total,
                    color: data.couleur
                });
            });

            // --- TABLE HTML ---
            let htmlMotif = `
                <div class="card-box pd-20 shadow-sm border rounded">
                    <h5 class="mb-3">Statistiques par Delai de Rendez-vous :</h5>
                    
                    <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Motif</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${optionMotif}
                            </tbody>
                        </table>
                    </div>
                </div>`;

            $("#afficheuseDelai").html(htmlMotif);

            Highcharts.chart('chart7', {
                chart: {
                    type: 'bar', // ou 'column' ou 'pie' selon tes besoins
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: 'Statistiques par Delai de Rendez-vous'
                },
                xAxis: {
                    type: "category"
                },
                plotOptions: {
                    column: {
                        depth: 25
                    }
                },
                legend: {
                    enabled: true
                },
                series: [{
                    name: 'Delai RDV',
                    data: tablo_graph, // 👉 data: [{name,y,color}]
                    colorByPoint: false
                }]
            });



        }

        function afficheuseRDVGestionnaire(tabloGestionnaire, colors) {

            let optionGestionnaire = ``;
            let tablo_graph = [];
            let tablo_color = [];
            let idcolor = 0;
            $.each(tabloGestionnaire, function(indx, data) {
                //console.log(indx);
                tablo_graph.push([indx, data, false], );
                tablo_color.push(colors[idcolor]);
                optionGestionnaire += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                idcolor++;
            });

            let htmlGestionnaire = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par RDV Gestionnaire :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionGestionnaire + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseRDVGestionnaire").html(htmlGestionnaire);

            Highcharts.chart('chartRDVGestionnaire', {
                chart: {
                    type: 'bar', // ou 'column' ou 'pie' selon tes besoins
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: 'Statistiques par RDV Gestionnaire'
                },
                xAxis: {
                    type: "category"
                },
                plotOptions: {
                    column: {
                        depth: 25
                    }
                },
                legend: {
                    enabled: true
                },
                series: [{
                    name: 'RDV Gestionnaire',
                    data: tablo_graph, // 👉 data: [{name,y,color}]
                    colorByPoint: true,
                    colors: tablo_color
                }]
            });
        }
    </script>

</body>

</html>