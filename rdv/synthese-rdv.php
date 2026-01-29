<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("../autoload.php");

$paramCompte = $_SESSION['paramCompte'];
list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

$libelle = "";
$description = "";
$filtreuse = null;

if (isset($_REQUEST["jour"])) {
    $jour = $_REQUEST["jour"];
    $libelle = 'Jour';
    $description = $jour;
    $filtreuse = "  ( (tblrdv.daterdv LIKE '%/%' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') = '$jour') OR (tblrdv.daterdv LIKE '%-%' AND STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y') = '$jour') )";
    $tableauSuivi = $fonction->recapTraitementEffectue($jour, $profil, $usersid);
} elseif (isset($_REQUEST["motif"])) {
    $motif = $_REQUEST["motif"];
    $libelle = 'Motif';
    $description = $motif;
    $filtreuse = " tblrdv.motifrdv = '$motif'  ";
} elseif (isset($_REQUEST["delai"])) {
    $delai = $_REQUEST["delai"];
    $date = date('Y-m-d');
    $libelle = 'Délai';
    if ($delai == "prochain") {
        $description = " A venir";
        $filtreuse = " ( (tblrdv.daterdv LIKE '%/%' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') > '$date') OR (tblrdv.daterdv LIKE '%-%' AND STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y') > '$date') )";
    } elseif ($delai == "expire") {
        $description = " Expiré";
        $filtreuse = " ( (tblrdv.daterdv LIKE '%/%' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') < '$date') OR (tblrdv.daterdv LIKE '%-%' AND STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y') < '$date') )";
    } else {
        $description = " Aujourd'hui";
        $filtreuse = " ( (tblrdv.daterdv LIKE '%/%' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') = '$date') OR (tblrdv.daterdv LIKE '%-%' AND STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y') = '$date') )";
    }
} elseif (isset($_REQUEST["ville"])) {
    $ville = $_REQUEST["ville"];
    $libelle = 'Ville';
    $description = $ville;
    $filtreuse = "  tblvillebureau.libelleVilleBureau = '$ville' ";
} elseif (isset($_REQUEST["agent"])) {
    $agent = $_REQUEST["agent"];
    $libelle = 'Gestionnaire';
    $description = $agent;
    $filtreuse = "  CONCAT(users.nom, ' ', users.prenom) = '$agent' ";
}

if (isset($filtreuse) && $filtreuse != null) {

    if ($profil == "gestionnaire" || $profil == "agent") {
        $critereParametre  = " WHERE tblrdv.gestionnaire = '$usersid'  AND " . $filtreuse;
    } else {
        $critereParametre  = " WHERE " . $filtreuse;
    }
}

$sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
                    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
                    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $critereParametre ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
$tableauSuivi = $fonction->_getSelectDatabases($sqlSelect);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "../include/entete.php"; ?>
</head>

<body>

    <?php include "../include/header.php"; ?>

    <!-- ================= PRELOADER ================= -->
    <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Chargement...</div>
        </div>
    </div>
    <!-- ============================================== -->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">

            <div class="page-header">
                <h4>Synthèse des rendez-vous</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                        <li class="breadcrumb-item active">Synthèse des rendez-vous</li>
                    </ol>
                </nav>
            </div>

            <!-- ================== INFOS VILLE ================== -->
            <div class="pd-20 mb-30">
                <div style="float:left" class="p-2">
                    <button class="btn btn-warning p-2 m-2" onclick="retour()"><i class='fa fa-arrow-left'>Retour</i></button>
                </div>
            </div>
            <br>
            <div class="card-box mb-30">

                <div class="pd-20">
                    <div class="row">

                        <div class="col-md-7 stat-box">
                            <p>Utilisateur : <strong><?= $userConnect ?></strong></p>
                            <p>Profil : <strong><?= $service ?> / <?= $profil ?></strong></p>
                        </div>

                        <div class="col-md-5 stat-box">
                            <p>Code agent : <strong><?= $codeagent ?></strong> </p>
                            <p><span class="badge badge-secondary"><?= strtoupper($libelle) ?> : <?= strtoupper($description) ?> </span></p>
                        </div>

                    </div>
                </div>
            </div>
            <?php if ($tableauSuivi != null) : ?>

                <div class="card-box mb-30">

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-success font-weight-bold mb-0">
                                Répartition Rendez-vous - <?= $description ?>
                            </h5>
                            <span class="badge badge-success badge-pill">
                                Total : <?= count($tableauSuivi) ?>
                            </span>
                        </div>
                        <hr>
                        <table id="tableUser" class="table hover data-table-export nowrap" style="width:100%; font-size:10px;">
                            <thead class="text-white" style="background-color: #033f1f;">
                                <tr>
                                    <th class="table-plus datatable-nosort">#Ref</th>
                                    <th>Id RDV</th>
                                    <th>Date prise RDV</th>
                                    <th>Nom & prénom(s)</th>
                                    <th>Id contrat</th>
                                    <th>Motif</th>
                                    <th>Date RDV</th>
                                    <th>Lieu RDV</th>
                                    <th>Détail</th>
                                    <th>État</th>
                                    <th class="table-plus datatable-nosort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($tableauSuivi); $i++) :
                                    $rdv  = $tableauSuivi[$i];


                                    // if (isset($rdv->etat) && empty($rdv->etat) && is_numeric($rdv->etat) && !isset(Config::tablo_statut_rdv[$rdv->etat]) && $rdv->etat == "undefined"  ) {
                                    //     continue;
                                    // }
                                    if (isset($rdv->etat) && !empty(trim($rdv->etat)) && trim($rdv->etat) != "" && isset(Config::tablo_statut_rdv[trim($rdv->etat)])) {
                                        $etat = $rdv->etat;
                                    } else {
                                        //print intval($rdv->etat). PHP_EOL;       //print_r($rdv);      //if(trim($rdv->etat) == "0") 
                                        $etat = intval($rdv->etat);
                                    }

                                    $retourEtat = Config::tablo_statut_rdv[$etat];
                                    $dateCompare = null;
                                    $lib_delai = null;
                                    $couleur_fond = null;
                                    $badge_delai = null;
                                    // Détermination de la date RDV affichée
                                    $dateRdv = $rdv->daterdv;
                                    if ($rdv->etat == "2" || $rdv->etat == "3") {
                                        if (isset($rdv->daterdveff) && $rdv->daterdveff != "") $dateRdv = date("d/m/Y", strtotime($rdv->daterdveff));

                                        if ($rdv->etat == "2") $dateCompare = $rdv->transmisLe;
                                        if ($rdv->etat == "3") $dateCompare = $rdv->traiterLe;
                                    }

                                    $delai = $fonction->getDelaiRDV($dateRdv, $dateCompare);
                                    if ($rdv->etat == "1") {
                                        $lib_delai = $delai['libelle'];
                                        $couleur_fond = $delai['couleur'] ?? 'transparent';
                                        $badge_delai = $delai['badge'] ?? 'badge badge-secondary';
                                    }

                                ?>
                                    <tr class="text-wrap">
                                        <td><?= $rdv->codedmd; ?></td>
                                        <td id="id-<?= $i ?>"><?php echo $rdv->idrdv; ?></td>
                                        <td><?= $rdv->daterdv; ?></td>
                                        <td><?= $rdv->nomclient; ?></td>
                                        <td class="text-wrap" id="idcontrat-<?= $i ?>"><?php echo $rdv->police; ?></td>
                                        <td><?= $rdv->motifrdv; ?></td>
                                        <td><?= $rdv->daterdv; ?></td>
                                        <td><?= $rdv->villes; ?></td>
                                        <td class="text-wrap text-muted">
                                            <div class="p-1 rounded" style="background:#f8f9fa;">


                                                <?php if ($rdv->etat == "1"): ?>
                                                    <span class="<?= $badge_delai ?? 'badge badge-secondary' ?>">
                                                        <?= $lib_delai ?? '—' ?>
                                                    </span>
                                                <?php elseif ($rdv->etat == "2"): ?>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        Gestionnaire :
                                                        <span style="font-weight:bold;"><?= htmlspecialchars($rdv->nomgestionnaire ?? "N/A") ?></span>
                                                    </p>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        Date Transmission :
                                                        <span style="font-weight:bold;"><?= !empty($rdv->transmisLe) ? date('d/m/Y', strtotime($rdv->transmisLe)) : "" ?></span>
                                                    </p>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        <?php if ($rdv->etat == "2" && ($dateRdv < date('Y-m-d'))): ?>
                                                            <span style="font-weight:bold; color:red;">Date RDV Expiré </span>
                                                        <?php endif; ?>
                                                    </p>
                                                <?php elseif ($rdv->etat == "3"): ?>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        Gestionnaire :
                                                        <span style="font-weight:bold;"><?= htmlspecialchars($rdv->nomgestionnaire ?? "N/A") ?></span>
                                                    </p>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        Date Traitement :
                                                        <span style="font-weight:bold;"><?= !empty($rdv->traiterLe) ? date('d/m/Y H:i', strtotime($rdv->traiterLe)) : "" ?></span>

                                                    </p>
                                                    <p class="mb-0 text-dark" style="font-size:0.7em;">
                                                        Traitement :
                                                        <span style="font-weight:bold;">
                                                            <?php if (isset($rdv->etatTraitement) && $rdv->etatTraitement != null && $rdv->etatTraitement != "0"): ?>
                                                                <?= $rdv->libelleTraitement ?>
                                                            <?php else: ?>
                                                                traitement non mentionné
                                                            <?php endif; ?>
                                                        </span>
                                                    </p>
                                                <?php else: ?>
                                                    <?= $rdv->reponse ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><span class="<?= htmlspecialchars($retourEtat["color_statut"]) ?>"> <?= htmlspecialchars($retourEtat["libelle"]) ?> </span></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm view"
                                                id="view-<?= $i ?>"
                                                style="background-color:#F9B233;color:white">
                                                <i class="fa fa-eye"></i> Détail
                                            </button>

                                            <?php if ($rdv->etat == "1"): ?>
                                                <button class="btn btn-success btn-sm traiter"
                                                    id="traiter-<?= $i ?>"
                                                    style="background-color:#033f1f; color:white">
                                                    <i class="fa fa-mouse-pointer"></i> Traiter
                                                </button>
                                            <?php elseif ($rdv->etat == "2" && ($dateRdv < date('Y-m-d'))): ?>

                                                <button class="btn btn-info btn-sm traiter"
                                                    id="traiter-<?= $i ?>"
                                                    style="background-color:info; color:white">
                                                    <i class="fa fa-mouse-pointer"></i> retraiter le rdv
                                                </button>

                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else:  ?>
                <div class="card-box mb-30">
                    <!-- <button class="btn btn-warning p-2 m-2" onclick="retour()"><i class='fa fa-arrow-left'>Retour</i></button> -->
                    <div class="pd-20">
                        <h4 class="text-danger mb-30 text-center h4">Aucun rendez-vous trouvé </h4>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php include "../include/footer.php"; ?>
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
        let profil = ""
        let paramCompte = "<?php echo $_SESSION['paramCompte'] ?>";
        if (paramCompte != null) {

            let tabloCompte = paramCompte.split("|");
            let usersid = tabloCompte[0];
            let service = tabloCompte[1];
            let typeCompte = tabloCompte[2];
            profil = tabloCompte[3];
            let cible = tabloCompte[4];
            let codeagent = tabloCompte[5];
            let userConnect = tabloCompte[6];
        }

        $(document).ready(function() {

            // Voir detail
            $(document).on('click', '.view', function() {
                const index = this.id.split('-')[1];
                const idrdv = $("#id-" + index).html();
                const idcontrat = $("#idcontrat-" + index).html();
                document.cookie = "idrdv=" + idrdv;
                document.cookie = "idcontrat=" + idcontrat;
                document.cookie = "action=detail";
                location.href = "detail-rdv";
            });

            // Traiter
            $(document).on('click', '.traiter', function() {

                const index = this.id.split('-')[1];
                const idrdv = $("#id-" + index).html();
                const idcontrat = $("#idcontrat-" + index).html();
                document.cookie = "idrdv=" + idrdv;
                document.cookie = "idcontrat=" + idcontrat;
                document.cookie = "action=traiter";
                if (profil == "gestionnaire" || profil == "agent") {
                    location.href = "traitement-rdv-gestionnaire";
                } else {
                    location.href = "fiche-rdv";
                }

            });
        });


        function retour() {
            window.history.back();
        }
    </script>

</body>

</html>