<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit;
}
setlocale(LC_TIME, 'fr_FR.UTF-8'); // Active la langue française

include("../autoload.php");

$paramCompte = $_SESSION['paramCompte'];
list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

if (isset($_REQUEST["mois"])) {
    $mois = $_REQUEST["mois"];
} else {
    $mois = null;
}

$mois = $fonction->retourneMoisCourant($mois);
$tabSemaine = $fonction->retourneSemaineCourante();
//print_r($tabSemaine);
//print_r($mois);


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

            <hr>
            <div class="border border-2 bg-light rounded p-4 mb-4">
                <h2>Statistique Globale de l'année <?php echo date('Y'); ?></h2><br> <br>
                <?php
                    $retourStatut = $fonction->afficheuseGlobalStatistiqueRDV();
                    echo $retourStatut;
                ?>
            </div>
            
            <!-- <div class="card-body pb-20 radius-12 w-100 p-4">
                <div class="mt-2">
                    <button class="btn btn-sm" style="background:#033f1f; color:white; text-decoration:none;" id="telechargerExcel">
                        Telecharger le rapport Excel
                    </button>
                </div>
            </div> -->
            <div class="border border-2 rounded bg-light p-4 mb-4">
                <h2>Statistique Globale</h2><br> <br>
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
    <script src="../vendors/scripts/rdv-expire-cron.js"></script>
    <!-- Datatable Setting js -->
    <script src="../vendors/scripts/datatable-setting.js"></script>
    <script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <!-- Inclure Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="../config/fonction.js"></script>


    <script>
        let colors = [
            "#3b82f6", // blue
            "#ef4444", // red
            "#22c55e", // green
            "#eab308", // yellow
            "#a855f7", // purple
            "#14b8a6", // teal
            "#f97316", // orange
            "#10b981", // emerald
            "#6366f1", // indigo
            "#84cc16", // lime
            "#f43f5e", // pink/red
            "#0ea5e9", // sky blue
            "#475569", // slate
            "#d946ef", // magenta
            "#059669", // dark green
            "#941010ff", // dark red
            "#7c3aed", // deep purple
            "#be123c", // crimson
            "#38bdf8", // light blue
            "#4ade80", // soft green
            "#facc15", // bright yellow
            "#fb923c", // light orange
            "#1e40af", // dark blue
            "#6b7280" // gray
        ];
        $(document).ready(function() {

            let typeCompte = "<?php echo $_SESSION['typeCompte'] ?>";

            let paramCompte = "<?php echo $_SESSION['paramCompte'] ?>";
            // if (paramCompte != null) {
            //     ////$this->paramCompte = trim($this->id . "|" . $this->service . "|" . $this->typeCompte . "|" . $this->profil . "|" . $this->cible . "|" . $this->codeagent."|" . $this->userConnect);

            //     let tabloCompte = paramCompte.split("|");
            //     let usersid = tabloCompte[0];
            //     let service = tabloCompte[1];
            //     let typeCompte = tabloCompte[2];
            //     let profil = tabloCompte[3];
            //     let cible = tabloCompte[4];
            //     let codeagent = tabloCompte[5];
            //     let userConnect = tabloCompte[6];

            //     console.log(usersid);
            //     console.log(service);
            //     console.log(typeCompte);
            //     console.log(profil);
            //     console.log(cible);
            //     console.log(codeagent);
            //     console.log(userConnect);

            //     if (service == "rdv") {

            //     }
            // }
            //console.log(paramCompte);
            introRDV()
        })





        function introRDV(service = "rdv", filtreuse = "") {



            //alert("Connexion routes ..." + service + " " + filtreuse);

            $.ajax({
                url: "../config/routes.php",
                data: {
                    service: service,
                    filtreuse: filtreuse,
                    etat: "tableauSuivi"
                },
                dataType: "json",
                method: "post",
                success: function(response, status) {
                    console.log(response);
                    if (response != "-1") {

                        //$("#totalResultat").html(response.length);

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
                        console.log(tabloEtat);
                        console.log(tabloMotif);
                        console.log(tabloVilleEffective);
                        console.log(tabloVilles);
                        console.log(tabloCourrier);
                        afficheuseEtat(tabloEtat);
                        afficheuseDelaiRDV(statsDelai);
                        afficheuseMotif(tabloMotif, colors);
                        afficheuseVilles(tabloVilles, colors);
                        afficheuseRDVGestionnaire(tabloNomGestionnaire, colors);
                    }


                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });
        }
    </script>

</body>

</html>