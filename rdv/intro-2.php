<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit;
}


include("../autoload.php");

$paramCompte = $_SESSION['paramCompte'];
list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

$mois = $fonction->retourneMoisCourant();
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
            <div class="card-body pb-20 radius-12 w-100 p-4">

                <div class="bg-white pd-20 card-box mb-30">
                    <h4 class="mb-20 p-2" style="color:#033f1f;font-weight:bold;">Mon calendrier </h4>
                    <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                    <?php if ($profil == "gestionnaire" || $profil == "agent") : ?>

                        <?php
                        // Gestion du mois via GET (navigation)
                        $moisCourant = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');
                        $timestampMois = strtotime($moisCourant . '-01');
                        $nbJoursMois = date('t', $timestampMois);

                        // Jours de la semaine
                        $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

                        // Mois précédent / suivant
                        $moisPrecedent = date('Y-m', strtotime('-1 month', $timestampMois));
                        $moisSuivant = date('Y-m', strtotime('+1 month', $timestampMois));
                        ?>

                        <div class="card-body">
                            <div style="text-align:center; margin-bottom:15px;">
                                <a href="?mois=<?= $moisPrecedent ?>" class="btn btn-secondary">&laquo; Mois précédent</a>
                                <strong style="margin:0 15px; font-size:18px;"><?= date('F Y', $timestampMois) ?></strong>
                                <a href="?mois=<?= $moisSuivant ?>" class="btn btn-secondary">Mois suivant &raquo;</a>
                            </div>

                            <div class="calendar-container" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:5px; max-width:1000px; margin:auto;">
                                <!-- Entête des jours -->
                                <?php foreach ($joursSemaine as $jourSemaine) : ?>
                                    <div style="font-weight:bold; text-align:center; padding:5px; border-bottom:2px solid #ccc;"><?= $jourSemaine ?></div>
                                <?php endforeach; ?>

                                <?php
                                // Décalage pour aligner le 1er jour
                                $decalage = date('N', $timestampMois) - 1;
                                for ($i = 0; $i < $decalage; $i++) echo '<div></div>';

                                // Boucle sur les jours
                                for ($jour = 1; $jour <= $nbJoursMois; $jour++) :
                                    $dateCourante = $moisCourant . '-' . str_pad($jour, 2, '0', STR_PAD_LEFT);
                                    $retour = $fonction->recapTraitementEffectue($dateCourante);


                                    $total = $retour['total'] ?? 0;
                                    $encours = $retour['en_attente'] ?? 0;
                                    $traite = $retour['traiter'] ?? 0;
                                    $transmis = $retour['transmis'] ?? 0;
                                    $rejeter = $retour['rejeter'] ?? 0;


                                    $jourSemaineNum = date('N', strtotime($dateCourante));
                                    $bgColor = $total > 0 ? '#d1f7d6' : ($jourSemaineNum >= 6 ? '#f0f0f0' : '#f7f7f7');
                                ?>
                                    <div style="border:1px solid #ccc; padding:8px; text-align:center; border-radius:6px; background-color:<?= $bgColor ?>;"
                                        title="Total: <?= $total ?>, En cours: <?= $encours ?>, Traité: <?= $traite ?>">
                                        <strong><?= $jour ?></strong><br>
                                        <?php if ($total > 0) : ?>
                                            <div style="font-size:12px; margin-top:3px;">
                                                <span style="color:#019875; font-weight:bold; font-size:10px;">Total : <?= $total ?></span><br>
                                                <span style="color:#f0ad4e; font-weight:bold; font-size:10px;">En cours: <?= $encours ?></span><br>
                                                <span style="color:#033f1f; font-weight:bold; font-size:10px;">Traité : <?= $traite ?></span><br>
                                                <span style="color:#5bc0de; font-weight:bold; font-size:10px;">Transmis : <?= $transmis ?></span><br>
                                                <span style="color:red; font-weight:bold; font-size:10px;">Rejeter : <?= $rejeter ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                </div>

            <?php endif; ?>

            <div class="bg-white pd-20 card-box mb-30">
                <h4 class="mb-20 p-2" style="color:#033f1f;font-weight:bold;">Mon calendrier</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;margin-bottom:15px;"></div>

                <?php if ($profil == "gestionnaire" || $profil == "agent") : ?>

                    <?php
                    $moisCourant = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');
                    $timestampMois = strtotime($moisCourant . '-01');
                    $nbJoursMois = date('t', $timestampMois);
                    $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    $moisPrecedent = date('Y-m', strtotime('-1 month', $timestampMois));
                    $moisSuivant = date('Y-m', strtotime('+1 month', $timestampMois));
                    ?>

                    <div style="text-align:center; margin-bottom:10px;">
                        <a href="?mois=<?= $moisPrecedent ?>" class="btn btn-sm btn-secondary">&laquo; Mois précédent</a>
                        <strong style="margin:0 15px; font-size:16px;"><?= date('F Y', $timestampMois) ?></strong>
                        <a href="?mois=<?= $moisSuivant ?>" class="btn btn-sm btn-secondary">Mois suivant &raquo;</a>
                    </div>

                    <div class="calendar-container" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:5px; max-width:700px; margin:auto;">
                        <!-- Entête des jours -->
                        <?php foreach ($joursSemaine as $jourSemaine) : ?>
                            <div style="font-weight:bold; text-align:center; padding:4px; border-bottom:2px solid #ccc; font-size:12px;"><?= $jourSemaine ?></div>
                        <?php endforeach; ?>

                        <?php
                        $decalage = date('N', $timestampMois) - 1;
                        for ($i = 0; $i < $decalage; $i++) echo '<div></div>';

                        for ($jour = 1; $jour <= $nbJoursMois; $jour++) :
                            $dateCourante = $moisCourant . '-' . str_pad($jour, 2, '0', STR_PAD_LEFT);
                            $retour = $fonction->recapTraitementEffectue($dateCourante);

                            $total = $retour['total'] ?? 0;
                            $encours = $retour['en_attente'] ?? 0;
                            $traite = $retour['traiter'] ?? 0;
                            $transmis = $retour['transmis'] ?? 0;
                            $rejeter = $retour['rejeter'] ?? 0;

                            $jourSemaineNum = date('N', strtotime($dateCourante));
                            $bgColor = $total > 0 ? '#d1f7d6' : ($jourSemaineNum >= 6 ? '#f0f0f0' : '#f7f7f7');
                        ?>
                            <div style="border:1px solid #ccc; padding:4px; text-align:center; border-radius:6px; background-color:<?= $bgColor ?>; font-size:11px;"
                                title="Total: <?= $total ?>, En cours: <?= $encours ?>, Traité: <?= $traite ?>, Transmis: <?= $transmis ?>, Rejeté: <?= $rejeter ?>">
                                <strong><?= $jour ?></strong>
                                <?php if ($total > 0) : ?>
                                    <div style="margin-top:2px; line-height:1.2;">
                                        <span style="color:#f0ad4e;">En attente :<?= $encours ?></span><br>
                                        <span style="color:#033f1f;">Traité(s) :<?= $traite ?></span><br>
                                        <span style="color:#5bc0de;">Transmis :<?= $transmis ?></span><br>
                                        <span style="color:red;">Rejeté(s) :<?= $rejeter ?></span> <br>
                                        <span style="color:black;">Total :<?= $total ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                <?php endif; ?>
            </div>

            <div class="bg-white pd-20 card-box mb-30">
                <h4 class="mb-20 p-2" style="color:#033f1f;font-weight:bold;">Mon calendrier</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;margin-bottom:15px;"></div>

                <?php if ($profil == "gestionnaire" || $profil == "agent") : ?>

                    <?php
                    $moisCourant = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');
                    $timestampMois = strtotime($moisCourant . '-01');
                    $nbJoursMois = date('t', $timestampMois);
                    $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    $moisPrecedent = date('Y-m', strtotime('-1 month', $timestampMois));
                    $moisSuivant = date('Y-m', strtotime('+1 month', $timestampMois));
                    ?>

                    <div style="text-align:center; margin-bottom:10px;">
                        <a href="?mois=<?= $moisPrecedent ?>" class="btn btn-sm btn-secondary">&laquo; Mois précédent</a>
                        <strong style="margin:0 15px; font-size:16px;"><?= date('F Y', $timestampMois) ?></strong>
                        <a href="?mois=<?= $moisSuivant ?>" class="btn btn-sm btn-secondary">Mois suivant &raquo;</a>
                    </div>

                    <table class="table table-bordered text-center" style="table-layout:fixed; max-width:1000px; margin:auto; font-size:12px;">
                        <thead>
                            <tr>
                                <?php foreach ($joursSemaine as $jourSemaine) : ?>
                                    <th style="background:#033f1f; color:white;"><?= $jourSemaine ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $decalage = date('N', $timestampMois) - 1;
                            $jourCourant = 1;

                            // Calcul du nombre de lignes nécessaires
                            $totalCases = $nbJoursMois + $decalage;
                            $lignes = ceil($totalCases / 7);

                            for ($i = 0; $i < $lignes; $i++) :
                                echo '<tr>';
                                for ($j = 0; $j < 7; $j++) :
                                    $caseNum = $i * 7 + $j;
                                    if ($caseNum < $decalage || $jourCourant > $nbJoursMois) {
                                        echo '<td></td>';
                                    } else {
                                        $dateCourante = $moisCourant . '-' . str_pad($jourCourant, 2, '0', STR_PAD_LEFT);
                                        $retour = $fonction->recapTraitementEffectue($dateCourante);

                                        $total = $retour['total'] ?? 0;
                                        $encours = $retour['en_attente'] ?? 0;
                                        $traite = $retour['traiter'] ?? 0;
                                        $transmis = $retour['transmis'] ?? 0;
                                        $rejeter = $retour['rejeter'] ?? 0;

                                        $bgColor = $total > 0 ? '#d1f7d6' : ($j >= 5 ? '#f0f0f0' : '#ffffff');

                                        echo '<td style="vertical-align:top; border:1px solid #ccc; background:' . $bgColor . '; padding:4px;">';
                                        echo '<strong>' . $jourCourant . '</strong><br>';
                                        if ($total > 0) {
                                            echo '<span style="background:#019875;color:white;border-radius:10px;padding:1px 3px;display:inline-block;margin-top:2px;font-size:10px;">T:' . $total . '</span><br>';
                                            echo '<span style="background:#f0ad4e;color:white;border-radius:10px;padding:1px 3px;display:inline-block;margin-top:1px;font-size:10px;">E:' . $encours . '</span><br>';
                                            echo '<span style="background:#033f1f;color:white;border-radius:10px;padding:1px 3px;display:inline-block;margin-top:1px;font-size:10px;">R:' . $traite . '</span><br>';
                                            echo '<span style="background:#5bc0de;color:white;border-radius:10px;padding:1px 3px;display:inline-block;margin-top:1px;font-size:10px;">S:' . $transmis . '</span><br>';
                                            echo '<span style="background:red;color:white;border-radius:10px;padding:1px 3px;display:inline-block;margin-top:1px;font-size:10px;">X:' . $rejeter . '</span>';
                                        }
                                        echo '</td>';
                                        $jourCourant++;
                                    }
                                endfor;
                                echo '</tr>';
                            endfor;
                            ?>
                        </tbody>
                    </table>

                <?php endif; ?>
            </div>
            <div class="bg-white pd-20 card-box mb-30">
                <h4 class="mb-20 p-2" style="color:#033f1f;font-weight:bold;">Mon calendrier</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;text-align: center;"></div>

                <?php if ($profil == "gestionnaire" || $profil == "agent") : ?>

                    <?php
                    // Mois courant via GET
                    $moisCourant = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');
                    $timestampMois = strtotime($moisCourant . '-01');
                    $nbJoursMois = date('t', $timestampMois);

                    // Jours de la semaine
                    $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

                    // Mois précédent / suivant
                    $moisPrecedent = date('Y-m', strtotime('-1 month', $timestampMois));
                    $moisSuivant = date('Y-m', strtotime('+1 month', $timestampMois));
                    ?>

                    <div class="card-body">
                        <div style="text-align:center; margin-bottom:15px;">
                            <a href="?mois=<?= $moisPrecedent ?>" class="btn btn-secondary">&laquo; Mois précédent</a>
                            <strong style="margin:0 15px; font-size:18px;"><?= date('F Y', $timestampMois) ?></strong>
                            <a href="?mois=<?= $moisSuivant ?>" class="btn btn-secondary">Mois suivant &raquo;</a>
                        </div>

                        <table style="width:100%; border-collapse:collapse; text-align:center;">
                            <thead>
                                <tr>
                                    <?php foreach ($joursSemaine as $jourSemaine) : ?>
                                        <th style="padding:5px; border:1px solid #ccc; background:#f0f0f0;"><?= $jourSemaine ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $decalage = date('N', $timestampMois) - 1; // Décalage pour aligner le 1er jour
                                $jourActuel = 1;
                                $totalCases = ceil(($nbJoursMois + $decalage) / 7) * 7;

                                for ($case = 0; $case < $totalCases; $case++) :
                                    if ($case % 7 == 0) echo '<tr>';

                                    if ($case < $decalage || $jourActuel > $nbJoursMois) {
                                        echo '<td style="border:1px solid #ccc; background:#f7f7f7; padding:8px;"></td>';
                                    } else {
                                        $dateCourante = $moisCourant . '-' . str_pad($jourActuel, 2, '0', STR_PAD_LEFT);
                                        $retour = $fonction->recapTraitementEffectue($dateCourante);

                                        $total = $retour['total'] ?? 0;
                                        $encours = $retour['en_attente'] ?? 0;
                                        $traite = $retour['traiter'] ?? 0;
                                        $transmis = $retour['transmis'] ?? 0;
                                        $rejeter = $retour['rejeter'] ?? 0;

                                        $jourSemaineNum = date('N', strtotime($dateCourante));
                                        $bgColor = $total > 0 ? '#d1f7d6' : ($jourSemaineNum >= 6 ? '#f0f0f0' : '#f7f7f7');

                                        echo '<td style="vertical-align:top; border:1px solid #ccc; background:' . $bgColor . '; padding:4px;">';
                                        echo '<strong>' . $jourActuel . '</strong>';

                                        if ($total > 0) {
                                            echo '<div style="display:flex; flex-direction:column; align-items:center; margin-top:3px; gap:2px; font-size:10px;">';
                                            echo '<div style="background:#019875;color:white;border-radius:4px;padding:2px 4px; width:100%;">T:' . $total . '</div>';
                                            echo '<div style="background:#f0ad4e;color:white;border-radius:4px;padding:2px 4px; width:100%;">E:' . $encours . '</div>';
                                            echo '<div style="background:#033f1f;color:white;border-radius:4px;padding:2px 4px; width:100%;">R:' . $traite . '</div>';
                                            echo '<div style="background:#5bc0de;color:white;border-radius:4px;padding:2px 4px; width:100%;">S:' . $transmis . '</div>';
                                            echo '<div style="background:red;color:white;border-radius:4px;padding:2px 4px; width:100%;">X:' . $rejeter . '</div>';
                                            echo '</div>';
                                        }

                                        echo '</td>';
                                        $jourActuel++;
                                    }

                                    if ($case % 7 == 6) echo '</tr>';
                                endfor;
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>


            <div class="bg-white pd-20 card-box mb-30">
                <h4 class="mb-20 p-2" style="color:#033f1f;font-weight:bold;">Mon calendrier</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;text-align: center;"></div>

                <?php if ($profil == "gestionnaire" || $profil == "agent") : ?>

                    <?php
                    $moisCourant = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');
                    $timestampMois = strtotime($moisCourant . '-01');
                    $nbJoursMois = date('t', $timestampMois);

                    $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    $moisPrecedent = date('Y-m', strtotime('-1 month', $timestampMois));
                    $moisSuivant = date('Y-m', strtotime('+1 month', $timestampMois));

                    $today = date('Y-m-d');
                    ?>

                    <div class="card-body">
                        <div style="text-align:center; margin-bottom:15px;">
                            <a href="?mois=<?= $moisPrecedent ?>" class="btn btn-secondary">&laquo; Mois précédent</a>
                            <strong style="margin:0 15px; font-size:18px;"><?= date('F Y', $timestampMois) ?></strong>
                            <a href="?mois=<?= $moisSuivant ?>" class="btn btn-secondary">Mois suivant &raquo;</a>
                        </div>

                        <table class="table table-bordered text-center" style="border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <?php foreach ($joursSemaine as $jourSemaine) : ?>
                                        <th style="padding:5px;"><?= $jourSemaine ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $decalage = date('N', $timestampMois) - 1;
                                $jourActuel = 1;
                                $totalCases = ceil(($nbJoursMois + $decalage) / 7) * 7;

                                for ($case = 0; $case < $totalCases; $case++) :
                                    if ($case % 7 == 0) echo '<tr>';

                                    if ($case < $decalage || $jourActuel > $nbJoursMois) {
                                        echo '<td style="background:#f7f7f7;"></td>';
                                    } else {
                                        $dateCourante = $moisCourant . '-' . str_pad($jourActuel, 2, '0', STR_PAD_LEFT);
                                        $retour = $fonction->recapTraitementEffectue($dateCourante);

                                        $total = $retour['total'] ?? 0;
                                        $encours = $retour['en_attente'] ?? 0;
                                        $traite = $retour['traiter'] ?? 0;
                                        $transmis = $retour['transmis'] ?? 0;
                                        $rejeter = $retour['rejeter'] ?? 0;

                                        // Couleur selon date
                                        if ($dateCourante < $today) {
                                            $bgColor = '#6c757d'; // secondary
                                            $textColor = 'white';
                                        } elseif ($dateCourante == $today) {
                                            $bgColor = '#007bff'; // primary
                                            $textColor = 'white';
                                        } else {
                                            $bgColor = '#dc3545'; // danger
                                            $textColor = 'white';
                                        }

                                        echo '<td style="vertical-align:top; padding:4px; background:' . $bgColor . '; color:' . $textColor . ';">';
                                        echo '<strong>' . $jourActuel . '</strong>';

                                        if ($total > 0) {
                                            echo '<div style="display:flex; flex-direction:column; align-items:center; margin-top:3px; gap:2px; font-size:10px;">';
                                            echo '<div style="background:#019875;color:white;border-radius:4px;padding:2px 4px; width:100%;">T:' . $total . '</div>';
                                            echo '<div style="background:#f0ad4e;color:white;border-radius:4px;padding:2px 4px; width:100%;">E:' . $encours . '</div>';
                                            echo '<div style="background:#033f1f;color:white;border-radius:4px;padding:2px 4px; width:100%;">R:' . $traite . '</div>';
                                            echo '<div style="background:#5bc0de;color:white;border-radius:4px;padding:2px 4px; width:100%;">S:' . $transmis . '</div>';
                                            echo '<div style="background:red;color:white;border-radius:4px;padding:2px 4px; width:100%;">X:' . $rejeter . '</div>';
                                            echo '</div>';
                                        }

                                        echo '</td>';
                                        $jourActuel++;
                                    }

                                    if ($case % 7 == 6) echo '</tr>';
                                endfor;
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>



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
            console.log(paramCompte);
            //introRDV()
        })





        function introRDV(service = "rdv", filtreuse = "") {



            alert("Connexion routes ..." + service + " " + filtreuse);

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