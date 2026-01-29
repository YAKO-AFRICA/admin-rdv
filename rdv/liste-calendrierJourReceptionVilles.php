<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

require_once "../autoload.php";

setlocale(LC_TIME, 'fr_FR.UTF-8');

// Date courante
$currentDate  = date('Y-m-d');
$currentMonth = date('m');
$currentYear  = date('Y');

// Infos utilisateur
$paramCompte = $_SESSION['paramCompte'];
list($usersid, $service, $typeCompte, $profil, $cible, $codeagent, $userConnect) = explode("|", $paramCompte);

$villesRDV = null;
$idVilleBureau = null;

// Chargement ville du gestionnaire / agent
if ($profil === "gestionnaire" || $profil === "agent") {

	$sqlQuery = "
        SELECT 
            users.id,
            users.email,
            users.codeagent,
            TRIM(CONCAT(users.nom, ' ', users.prenom)) AS gestionnairenom,
            tblvillebureau.libelleVilleBureau AS villeEffect,
            tblvillebureau.idVilleBureau
        FROM users
        INNER JOIN tblvillebureau 
            ON tblvillebureau.idVilleBureau = users.ville
        WHERE users.etat = '1'
          AND users.id = '$usersid'
        ORDER BY users.id ASC
    ";

	$resultat = $fonction->_getSelectDatabases($sqlQuery);

	if (!empty($resultat)) {
		$villesRDV = $resultat[0]->villeEffect;
		$idVilleBureau = $resultat[0]->idVilleBureau;
	}
}

if (isset($_REQUEST["mois"])) {
	$mois = $_REQUEST["mois"];
	$lib_mois = date('m', strtotime($mois));
} else {
	$mois = null;
	$lib_mois = date('m');
}

			

$mois = $fonction->retourneMoisCourant($mois);
$tabSemaine = $fonction->retourneSemaineCourante();
$debutSemaine = date('d/m/Y', strtotime($tabSemaine[0]));
$finSemaine  = date('d/m/Y', strtotime($tabSemaine[6]));
$retourJourReception = $fonction->getRetourJourReception($idVilleBureau);

$tabloMois = [1=>"Janvier", 2=>"Février", 3=>"Mars", 4=>"Avril", 5=>"Mai", 6=>"Juin", 7=>"Juillet", 8=>"Aout", 9=>"Septembre", 10=>"Octobre", 11=>"Novembre", 12=>"Décembre"];


?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<title>Jour de réception</title>

	<?php include "../include/entete.php"; ?>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

	<?php include "../include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">

				<!-- Header -->
				<div class="page-header">
					<div class="row">
						<div class="col-md-12">
							<div class="title">
								<h4>Jour de réception</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="intro"><?= Config::lib_pageAccueil ?></a>
									</li>
									<li class="breadcrumb-item active">Jour de réception</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<hr>

				<!-- Infos utilisateur -->
				<div class="card-box mb-30">
					<div class="pd-20">
						<div class="row">
							<div class="col-md-7">
								<p>Utilisateur : <strong><?= htmlspecialchars($userConnect) ?></strong></p>
								<p>Profil : <strong><?= htmlspecialchars($service) ?> / <?= htmlspecialchars($profil) ?></strong></p>
							</div>
							<div class="col-md-5">
								<p>Code agent : <strong><?= htmlspecialchars($codeagent) ?></strong></p>
								<?php if ($idVilleBureau !== null): ?>
									<p>
										Ville :
										<strong><?= htmlspecialchars($villesRDV) ?></strong>
										(<?= $idVilleBureau ?>)
									</p>
									<p>
										<span class="badge badge-info">
											Jour de réception :
											<?php
											$jourReception = "";
											foreach ($retourJourReception as $key => $value) {
												$jourReception .= $value->jour . " - ";
											}
											echo substr($jourReception, 0, -3);
											?>
										</span>
									</p>

								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<hr>

				<!-- Calendrier -->

				<div class="card-box mb-30">
					<div class="card-body">
						<h2 class="text-center">Calendrier des rendez-vous : <?= $tabloMois[intval($lib_mois)] ?></h2>
						<hr>

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
							<strong style="margin:0 15px; font-size:16px;"><?= $tabloMois[intval($lib_mois)] ?></strong>
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
								$retour = $fonction->recapTraitementEffectue($dateCourante, $profil, $usersid);

								$total = $retour['total'] ?? 0;
								$encours = $retour['en_attente'] ?? 0;
								$traite = $retour['traiter'] ?? 0;
								$transmis = $retour['transmis'] ?? 0;
								$rejeter = $retour['rejeter'] ?? 0;

								$jourSemaineNum = date('N', strtotime($dateCourante));
								$bgColor = $total > 0 ? '#d1f7d6' : ($jourSemaineNum >= 6 ? '#f0f0f0' : '#f7f7f7');
							?>
								<a href="synthese-rdv.php?jour=<?= $dateCourante ?>">
									<div style="border:1px solid #ccc; padding:4px; text-align:center; border-radius:6px; background-color:<?= $bgColor ?>; font-size:11px;"
										title="Total: <?= $total ?>, En cours: <?= $encours ?>, Traité: <?= $traite ?>, Transmis: <?= $transmis ?>, Rejeté: <?= $rejeter ?>">
										<strong><?= date("d/m", strtotime($dateCourante)) ?></strong>
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
								</a>
							<?php endfor; ?>
						</div>
					</div>
				</div>

			</div>

			<div class="footer-wrap pd-20 mb-20">
				<?php include "../include/footer.php"; ?>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="notificationValidation" tabindex="-1">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-body text-center" style="background-color: whitesmoke;">
					<div class="card-body" id="msgEchec"></div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" data-dismiss="modal">OK</button>
					<button class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>

	<!-- JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/locale/fr.js"></script>

	<script>
		const idVilleBureau = "<?= $idVilleBureau ?>";

		$(document).ready(function() {

			if (!idVilleBureau) {
				console.warn("Aucune ville associée à l'utilisateur.");
				return;
			}

			$('#calendar').fullCalendar({
				locale: 'fr',
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				events: function(start, end, timezone, callback) {
					$.ajax({
						url: "../config/routes.php",
						method: "POST",
						dataType: "json",
						data: {
							idVilleEff: idVilleBureau,
							etat: "receptionJourRdv"
						},
						success: function(response) {
							if (!response.success) return;

							const events = response.events.map(ev => ({
								title: ev.location,
								start: moment().day(ev.jour).format('YYYY-MM-DD'),
								allDay: true,
								idVille: ev.idvilles
							}));

							callback(events);
						},
						error: function(err) {
							console.error("Erreur chargement calendrier", err);
						}
					});
				},
				eventClick: function(event) {
					checkDate(event.idVille, event.start.format('YYYY-MM-DD'), event.title);
				}
			});
		});

		function checkDate(idVille, dateRDV, location) {

			$.ajax({
				url: "../config/routes.php",
				method: "POST",
				dataType: "json",
				data: {
					idVilleEff: idVille,
					daterdveff: dateRDV,
					daterdv: dateRDV,
					etat: "compteurRdv"
				},
				success: function(response) {

					const total = parseInt(response?.data?.totalrdv || 0);
					const message = total > 0 ?
						`Il y a <strong style="color:red">${total}</strong> RDV(s) le <strong>${response.daterdv}</strong> à <strong>${location}</strong>` :
						`Aucun RDV prévu le <strong>${response.daterdv}</strong> à <strong>${location}</strong>`;

					$('#msgEchec').html(message);
					$('#notificationValidation').modal('show');
				}
			});
		}
	</script>

</body>

</html>