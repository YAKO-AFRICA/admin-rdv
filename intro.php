<?php

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}


include("autoload.php");


//


?>

<!DOCTYPE html>
<html>

<head>
    <?php include "include/entete.php"; ?>
</head>

<body>

    <?php
    include "include/header.php";
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

    <!--div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">

                    <div class="pd-10 height-50-p mb-30">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <img src="vendors/images/banner-img.png" alt="">
                            </div>

                            <div class="col-md-8">
                                <h4 class="font-20 weight-500 mb-10 ">
                                    Bienvenue <?php echo $_SESSION['utilisateur'];  ?> sur la plateforme de gestion des <?= strtolower($_SESSION['typeCompte']) ?>s ,</h4><br>
                                <p class="font-18 max-width-600">Vous trouverez ci-dessous un récapitulatif des différentes demandes de <?= strtolower($_SESSION['typeCompte']) ?>s.</p>
                            </div>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_PRESTATION) {

                $data = $fonction->_recapGlobalePrestations();
                echo "<script>let prestationsData = " . json_encode($data) . ";</script>";
            ?>

                <?php
                $retourStatut = $fonction->_recapGlobalePrestations();
                if (isset($retourStatut) && $retourStatut != null) {
                ?>
                    <div class="row">

                        <div class="col-md-4 col-sm-12 mb-30">
                            <a href="liste-prestations">
                                <div class="pd-20 card-box" style="background-color:whitesmoke; font-weight:bold; ">
                                    <h4 class="mb-30 h4 text-center" style="color:#033f1f!important;font-weight:bold; ">TOTALS PRESTATIONS</h4>
                                    <h1 class="mb-30 text-center" style="color:#033f1f;font-weight:bold; "><?= trim($retourStatut[1]['nb_ligne_total']) ?></h1>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-8 col-sm-12 mb-30">
                            <div class="row">
                                <?php

                                foreach ($retourStatut as $etat => $statut) {
                                ?>
                                    <div class="col-md-4 col-sm-12 mb-30">
                                        <a href="<?= trim($statut["url"]) ?>">
                                            <div class="pd-20 card-box" style="background-color:<?= trim($statut["color"]) ?>; font-weight:bold; ">
                                                <h4 class="mb-30 h4 text-center" style="color:white;font-weight:bold; ">PRESTATIONS <?= trim(strtoupper($statut["keyword"])) ?></h4>
                                                <h2 class="mb-30 text-center" style="color:white;font-weight:bold; "><?= trim($statut["nb_ligne_element"]) ?></h2>

                                            </div>
                                        </a>
                                    </div>
                                <?php

                                }
                                ?>
                            </div>

                        </div>
                    </div>

                <?php
                }
                ?>
                <div class="row p-2">
                    <div class="col-md-5">
                        <div class="card-box height-100-p pd-20">
                            <h2 class="h4 mb-20 p-2 card-body" style="background-color:#033f1f; font-weight:bold;color:white"> proportion par statut de traitement des demandes de prestation</h2>
                            <div id="container" style="width: 100%; height: 300px;"></div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card-box height-100-p pd-20">
                            <h2 class="h4 mb-20 p-2 card-body" style="background-color:#033f1f; font-weight:bold;color:white"> proportion par type de demande de prestation</h2>

                            <div class="card-body">
                                <canvas id="myChartType" class="chartjs-render-monitor " style="width:100%;max-width:750px; height:250px; ">
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>


        </div>
    </div-->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">

                    <div class="pd-10 height-50-p mb-30">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <img src="vendors/images/banner-img.png" alt="">
                            </div>

                            <div class="col-md-8">
                                <h4 class="font-20 weight-500 mb-10 ">
                                    Bienvenue <?php echo $_SESSION['utilisateur'];  ?> sur la plateforme de gestion des validations des demandes de services <?= strtoupper(Config::Societe) ?> ,</h4><br>
                                <p class="font-18 max-width-600">Vous trouverez ci-dessous un récapitulatif des différentes demandes de <?= strtolower($_SESSION['typeCompte']) ?>s.</p>
                            </div>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="container px-0">
                    <h4 class="mb-30 text-blue h4">PRESTATIONS</h4>
                    <hr>
                    <div class="row">
                        <?php $affiche_2 =    '';
                        $affiche_3 =    '';
                        $retourStatut = $fonction->_recapGlobalePrestations();
                        if (isset($retourStatut) && $retourStatut != null) {

                            foreach ($retourStatut as $etat => $statut) {

                                if ($statut["etat"] == "-1") {
                                    continue;
                                } else {
                                    $values = $statut["etat"] . ";" . $statut["libelle"];
                        ?>
                                    <div class="col-md-4 mb-30">
                                        <div class="card-box pricing-card mt-30 mb-30" style="background-color:<?= trim($statut["color"]) ?>; font-weight:bold; ">

                                            <!-- <div class="price-title">
                                                Beginner
                                            </div> -->
                                            <div class="pricing-title" style="color:white; font-weight:bold; ">
                                                <?= trim(strtoupper($statut["nb_ligne_element"])) ?>
                                            </div>
                                            <div class="text-white">
                                                <?= trim(strtoupper($statut["libelle"])) ?>
                                            </div>
                                            <div class="cta">
                                                <a href="liste-rdv-nissa?etat=<?= strtolower($statut["etat"]) ?>" class="btn btn-info btn-rounded btn-lg">-></a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                                }
                            }
                        }
                        ?>
                    </div>

                    <h4 class="mb-30 text-success h4">RDVS</h4>
                    <hr>
                    <div class="row">
                        <?php $affiche_2 =    '';
                        $affiche_3 =    '';
                        $retourStatut = $fonction->pourcentageRDV();
                        if (isset($retourStatut) && $retourStatut != null) {

                            foreach ($retourStatut as $etat => $statut) {
                                if ($statut["statut"] == "-1") {
                                    continue;
                                } else {
                                    $values = $statut["statut"] . ";" . $statut["libelle"];
                        ?>
                                    <div class="col-md-3 mb-30">
                                        <div class="card-box pricing-card mt-30 mb-30" style="background-color:<?= trim($statut["color"]) ?>; font-weight:bold; ">

                                            <!-- <div class="price-title">
                                                Beginner
                                            </div> -->
                                            <div class="pricing-title" style="color:white; font-weight:bold; ">
                                                <?= trim(strtoupper($statut["nb_ligne_element"])) ?>
                                            </div>
                                            <div class="text-white">
                                                <?= trim(strtoupper($statut["libelle"])) ?>
                                            </div>
                                            <div class="cta">
                                                <a href="liste-rdv-nissa?etat=<?= strtolower($statut["statut"]) ?>" class="btn btn-info btn-rounded btn-lg">-></a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                                }
                            }
                        }
                        ?>
                    </div>

                    <!-- <h4 class="mb-30 mt-30 text-blue h4">Pricing Table Style 2</h4>
                    <div class="row">
                        <div class="col-md-4 mb-30">
                            <div class="card-box pricing-card-style2">
                                <div class="pricing-card-header">
                                    <div class="left">
                                        <h5>Standard</h5>
                                        <p>For small businesses</p>
                                    </div>
                                    <div class="right">
                                        <div class="pricing-price">
                                            €10<span>/month</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-card-body">
                                    <div class="pricing-points">
                                        <ul>
                                            <li>2 TB of space</li>
                                            <li>120 days of file recovery</li>
                                            <li>Smart Sync</li>
                                            <li>Dropbox Paper admin tools</li>
                                            <li>Granular sharing permissions</li>
                                            <li>User and company-managed groups</li>
                                            <li>Live chat support</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="cta">
                                    <a href="#" class="btn btn-primary btn-rounded btn-lg">Get Started</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-30">
                            <div class="card-box pricing-card-style2">
                                <div class="pricing-card-header">
                                    <div class="left">
                                        <h5>Advanced</h5>
                                        <p>For big businesses</p>
                                    </div>
                                    <div class="right">
                                        <div class="pricing-price">
                                            €15<span>/month</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-card-body">
                                    <div class="pricing-points">
                                        <ul>
                                            <li>Everything in Standard</li>
                                            <li>As much space as needed</li>
                                            <li>Advanced admin controls</li>
                                            <li>Dropbox Showcase</li>
                                            <li>Tiered admin roles</li>
                                            <li>Advanced user management tools</li>
                                            <li>Domain verification</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="cta">
                                    <a href="#" class="btn btn-primary btn-rounded btn-lg">Get Started</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-30">
                            <div class="card-box pricing-card-style2">
                                <div class="pricing-card-header">
                                    <div class="left">
                                        <h5>Enterprise</h5>
                                        <p>For enterprises</p>
                                    </div>
                                    <div class="right">
                                        <div class="pricing-price">
                                            €25<span>/month</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pricing-card-body">
                                    <div class="pricing-points">
                                        <ul>
                                            <li>Everything in Advanced</li>
                                            <li>Account Capture</li>
                                            <li>Network control</li>
                                            <li>Enterprise management support</li>
                                            <li>Domain Insights</li>
                                            <li>Advanced training for end users</li>
                                            <li>24/7 phone support</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="cta">
                                    <a href="#" class="btn btn-primary btn-rounded btn-lg">Get Started</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="footer-wrap pd-20 mb-20">
                    <?php include "include/footer.php";    ?>
                </div>
            </div>
        </div>
    </div>





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

    <script src="vendors/scripts/datatable-setting.js"></script>
    <!-- Datatable Setting js -->
    <script src="vendors/scripts/datatable-setting.js"></script>
    <script src="src/plugins/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <!-- Inclure Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        $(document).ready(function() {

            let typeCompte = "<?php echo $_SESSION['typeCompte'] ?>";

            if (typeCompte == "prestation") {
                introPretations()
            }


        })


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
                    etat: "intro"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let retourStatut = response['retourStatut']
                    let retourType = response['global']
                    //console.log(retourStatut);


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

        function templateDiagrammeBar(arg1, arg2, arg3 = null, textlegend = "Ma production recouvrement") {
            var xValues = arg1;
            var yValues = arg2;
            var barColors = "";



            if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
            barColors = arg3;
            //var barColors = arg3;

            //console.log(barColors)

            new Chart("myChart3", {
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
            new Chart("myChartType", {
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