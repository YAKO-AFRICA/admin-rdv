<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}
include("../autoload.php");

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "../include/entete.php"; ?>
</head>

<body>

    <?php
    include "../include/header.php";
    ?>

    <div class="pre-loader">
        <div class="pre-loader-box">
            <!--div class="loader-logo"><img src="vendors/images/logo-icon.png" alt="" style="width:30%;height:40%;"></div-->
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">15%</div>
            <div class="loading-text">
                chargement...
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-4"><img src="../vendors/images/banner-img.png" alt=""></div>
                    <div class="col-md-8">
                        <h4 class="font-20 weight-500 mb-10">
                            Bienvenue <?= $_SESSION['utilisateur']; ?> sur la plateforme de gestion des <?= strtolower($_SESSION['typeCompte']) == "gestionnaire" || strtolower($_SESSION['typeCompte']) == "rdv" ? "rendez-vous" : strtolower($_SESSION['typeCompte'] . 's') ?>,
                        </h4>
                        <p class="font-18 max-width-600">Récapitulatif des demandes de <?= strtolower($_SESSION['typeCompte']) == "gestionnaire" || strtolower($_SESSION['typeCompte']) == "rdv" ? "rendez-vous" : strtolower($_SESSION['typeCompte'] . 's') ?>.</p>
                    </div>
                </div>
            </div>

            <?php
            if ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_PRESTATION) {
                $data = $fonction->_recapGlobalePrestations();
                echo "<script>let prestationsData = " . json_encode($data) . ";</script>";
            ?>
                <div class="row">
                    <?= $fonction->getParametreGlobalPrestations(); ?>
                </div>

                <div class="row p-2">
                    <div class="col-md-5">
                        <div class="card-box pd-20">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par statut de traitement</h4>
                            <canvas id="chartStat" style="width:100%; height:300px;"></canvas>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card-box pd-20">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par type de prestation</h4>
                            <canvas id="chartType" style="width:100%; height:300px;"></canvas>
                        </div>
                    </div>
                </div>

            <?php
            } elseif ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_RDV) {
            ?>
                <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion Rendez-vous par ville</h4>

                <div class="row p-2">
                    <div class="col-md-6 table-responsive" style="height:500px;">
                        <!-- <div>Total général: <span id="totalVille">0</span></div> -->
                        <table id="tableRDV_Ville" class="table table-striped table-bordered" style="width:100% ; font-size:10px;">
                            <thead>
                                <tr>
                                    <th>Ville</th>
                                    <th>Nombre de RDV</th>
                                    <th>Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                    <div class="col-md-6 table-responsive" style="height:500px;">
                        <canvas id="chartRDV_Ville" height="150"></canvas>
                    </div>
                </div>
                <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par motif de Rendez-vous </h4>
                <div class="row p-2">
                    <div class="col-md-6 table-responsive" style="height:500px;">

                        <!-- <div>Total général: <span id="totalService">0</span></div> -->
                        <table id="tableRDV_Type" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Nombre de RDV</th>
                                    <th>Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>

                    </div>
                    <div class="col-md-6 table-responsive" style="height:500px;">
                        <canvas id="chartRDV_Type" height="120"></canvas>
                    </div>
                </div>
            <?php } elseif ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_GESTIONNAIRE) {
            ?>
                <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par motif de Rendez-vous </h4>
                <div class="row p-2">
                    <div class="col-md-6 table-responsive" style="height:500px;">

                        <!-- <div>Total général: <span id="totalService">0</span></div> -->
                        <table id="tableRDV_Type" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Nombre de RDV</th>
                                    <th>Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>

                    </div>
                    <div class="col-md-6 table-responsive" style="height:500px;">
                        <canvas id="chartRDV_Type" height="120"></canvas>
                    </div>
                </div>
            <?php
            } ?>


        </div>

        <div class="footer-wrap pd-20 mb-20">
            <?php include "../include/footer.php"; ?>
        </div>
    </div>





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

    <script src="../vendors/scripts/datatable-setting.js"></script>
    <!-- Datatable Setting js -->
    <script src="../vendors/scripts/datatable-setting.js"></script>
    <script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <!-- Inclure Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function() {

            let typeCompte = "<?php echo $_SESSION['typeCompte'] ?>";


            if (typeCompte == "prestation") {

                introPretations()

                document.addEventListener('DOMContentLoaded', function() {
                    const categories = [];
                    const data = [];

                    for (let key in prestationsData) {
                        if (prestationsData.hasOwnProperty(key)) {
                            let prestation = prestationsData[key];
                            categories.push(prestation.libelle + " ( " + prestation.nb_ligne_element + " ) ");
                            data.push({
                                y: parseFloat(prestation.pourcentage),
                                color: prestation.color
                            });
                        }
                    }

                    Highcharts.chart('container', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Répartition par étape de prestation (%)'
                        },
                        xAxis: {
                            categories,
                            title: {
                                text: 'Étape de prestation'
                            }
                        },
                        yAxis: {
                            min: 0,
                            max: 100,
                            title: {
                                text: 'Pourcentage (%)'
                            }
                        },
                        series: [{
                            name: 'Pourcentage',
                            data
                        }],
                        tooltip: {
                            pointFormat: '<b>{point.y:.1f}%</b>'
                        },
                        legend: {
                            enabled: false
                        }
                    });

                });

            }

            if (typeCompte == "rdv" || typeCompte == "gestionnaire") {
                introRDV()
            }

        })





        function introRDV() {

            let tabloRDV_Statut = [];
            let tabloStatRDV_Statut = [];
            let tabloValRDV_Statut = [];
            let barColorsRDV_Statut = [];

            let labelsRDV_Ville = [];
            let tabloRDV_Ville = [];
            let tabloValRDV_Ville = [];
            let barColorsRDV_Ville = [];

            let tabloRDV_user = [];
            let tabloStatRDV_user = [];
            let tabloValRDV_user = [];
            let barColorsRDV_user = [];

            let tabloRDV_Type = [];
            let labelsStat_Type = [];
            let tabloStatRDV_Type = [];
            let tabloValRDV_Type = [];
            let barColorsRDV_Type = [];


            $.ajax({
                url: "config/routes.php",
                data: {
                    type: "rdv",
                    etat: "intro"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let retourStatut = response['retourStatut']
                    let retourStatVille = response['retourStatVille']
                    let retourStatuser = response['retourStatuser']
                    let retourStatutType = response['retourStatutType']

                    console.log(response);

                    //retourStatVille.sort((a, b) => b.nb_ligne_element - a.nb_ligne_element);
                    $.each(retourStatVille, function(indxVille, elementVille) {

                        if (elementVille.etat == "1") aTraiter = elementVille.nb_ligne_element;
                        if (elementVille.libelle == "") elementVille.libelle = "Non renseigné";
                        labelsRDV_Ville.push(elementVille.libelle);
                        tabloValRDV_Ville.push(elementVille.nb_ligne_element);
                        barColorsRDV_Ville.push(elementVille.color);

                        tabloRDV_Ville += `<tr>
                            <td style="font-size:14px"><a href="synthese-rdv.php?idville=${elementVille.keyword}">${elementVille.libelle}</td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementVille.color};color:white">${elementVille.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge ${elementVille.bagde} badge-pill">${elementVille.pourcentage} %</span></td>
                            </tr>`
                    })

                    $("#tableRDV_Ville").html(tabloRDV_Ville);
                    // Affiche le graphe
                    afficherGraphVille(labelsRDV_Ville, tabloValRDV_Ville, barColorsRDV_Ville);

                    $.each(retourStatutType, function(indxType, elementType) {

                        if (elementType.libelle == "") elementType.libelle = "Non renseigné";
                        labelsStat_Type.push(elementType.libelle);
                        tabloValRDV_Type.push(elementType.nb_ligne_element);
                        barColorsRDV_Type.push(elementType.color);

                        tabloRDV_Type += `<tr>
                            <td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${elementType.libelle}</td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementType.color};color:white">${elementType.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge ${elementType.bagde} badge-pill">${elementType.pourcentage} %</span></td>
                            </tr>`
                    })

                    $("#tableRDV_Type").html(tabloRDV_Type);
                    // Affiche le graphe
                    afficherGraphType(labelsStat_Type, tabloValRDV_Type, barColorsRDV_Type);
                },
                error: function(response, status, etat) {
                    //var a_afficher = "traitement enregistrer avec succes !!"
                    // $("#a_afficher2").text(a_afficher)
                    // $('#notification').modal("show")
                }
            })
        }

        function introPretations() {
            let tablo = [];
            let tabloStat = [];
            let tabloVal = [];
            let barColors = [];

            let tabloType = [];
            let tabloStatType = [];
            let tabloValType = [];
            let barColorsType = [];

            let aTraiter = 0;


            $.ajax({
                url: "config/routes.php",
                data: {
                    type: "prestation",
                    etat: "intro"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let retourStatut = response['retourStatut']
                    let retourType = response['global']
                    console.log(response);


                    $.each(retourStatut, function(indx, element) {

                        if (element.etat == "1") aTraiter = element.nb_ligne_element;
                        tabloStat.push(element.keyword);
                        tabloVal.push(element.nb_ligne_element);
                        barColors.push(element.color);

                        tablo += `<tr>
                            <td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${element.keyword}</td>
                            <td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.pourcentage} %</span></td>
                            </tr>`
                    })

                    templateDiagrammeBar(tabloStat, tabloVal, barColors, "Production par statut de traitement")
                    $("#afficheuseStat").html(tablo);

                    $("#a_traiter").text(aTraiter + ' demandes non traitées');


                    $.each(retourType, function(indxType, elementType) {

                        tabloStatType.push(elementType.libelle + " (" + elementType.nb_ligne_element + ")");
                        tabloValType.push(elementType.nb_ligne_element);
                        barColors.push(elementType.color);


                        tabloType += `<tr>
                            <td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${elementType.libelle}</td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementType.color};color:white">${elementType.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementType.color};color:white" >${elementType.pourcentage} %</span></td>
                            </tr> `
                    })

                    templateDiagrammeBarType(tabloStatType, tabloValType, barColorsType, "Production par type de prestations")
                    $("#afficheuseStatType").html(tabloType);

                },
                error: function(response, status, etat) {
                    //var a_afficher = "traitement enregistrer avec succes !!"
                    // $("#a_afficher2").text(a_afficher)
                    // $('#notification').modal("show")
                }
            })
        }



        function afficherGraphVille(arg1, arg2, arg3 = null, textlegend = "graphique Rendez-vous par ville") {
            var xValues = arg1;
            var yValues = arg2;
            var barColors = "";



            if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
            barColors = arg3;
            //var barColors = arg3;

            //console.log(barColors)

            new Chart("chartRDV_Ville", {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,

                        text: textlegend
                    }
                }
            });
        }

        function afficherGraphType(arg1, arg2, arg3 = null, textlegend = "graphique par motif de Rendez-vous") {
            var xValues = arg1;
            var yValues = arg2;
            var barColors = "";



            if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
            barColors = arg3;
            //var barColors = arg3;

            //console.log(barColors)

            new Chart("chartRDV_Type", {
                type: "pie",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,

                        text: textlegend
                    }
                }
            });
        }

        function templateDiagrammeBar(arg1, arg2, arg3 = null, textlegend = "Ma production recouvrement") {
            var xValues = arg1;
            var yValues = arg2;
            var barColors = "";



            if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
            barColors = arg3;
            //var barColors = arg3;

            //console.log(barColors)

            new Chart("chartStat", {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,

                        text: textlegend
                    }
                }
            });
        }

        function templateDiagrammeBarType(arg1, arg2, arg3 = null, textlegend = "Production Agent") {
            var xValues = arg1;
            var yValues = arg2;
            var barColors;
            //var barColors = arg3;
            barColors = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
            //barColors = arg3;

            //console.log(barColors)
            new Chart("chartType", {
                type: "pie",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: textlegend
                    }
                }
            });
        }
    </script>

</body>

</html>