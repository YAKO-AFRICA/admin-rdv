<?php
session_start();


if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}/**/


include("../autoload.php");

if (isset($_REQUEST['filtreliste'])) {
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];
} else {
	$filtre = '';
}

$etat = GetParameter::FromArray($_REQUEST, 'i');
if (isset($etat) && $etat !== null && in_array($etat, array_keys(Config::tablo_statut_rdv))) {
	$etat = $etat;
	$retourEtat = Config::tablo_statut_rdv[$etat];
	$libelleTraitement = " - " . $retourEtat["libelle"];
	$couleur = $retourEtat["color"];
} else {
	$etat = null;
	$libelleTraitement = " - Total(s)";
	$couleur = "#000000";
}

$liste_rdvs = $fonction->getSelectRDVAfficher($etat);
if ($liste_rdvs != null) $effectue = count($liste_rdvs);
else $effectue = 0;

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

				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Liste des Rendez-vous <?= $libelleTraitement ?></h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a
											href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page">
										Liste des Rendez-vous <?= $libelleTraitement ?> </li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<?php
				$retourStatut = $fonction->afficheuseGlobalStatistiqueRDV();
				echo $retourStatut;
				?>

				<div class="card-box mb-30">
					<div class="pd-20">
						<h4 class="text-center" style="color:info; "> Liste des Rendez-vous <span style="color:<?= $couleur ?>;"><?= $libelleTraitement ?></span> </h4>
					</div>
					<div class="pb-20">
						<div class="col text-center">
							<h5><?= "Total Ligne  : " ?> <span style="color:<?= $couleur ?> !important;"><?= $effectue ?></span> </h5>
						</div>
					</div>

					<div class="pb-20">
						<table class="table hover data-table-export nowrap"
							id="liste-rdv-attente"
							style="width:100%; font-size:10px;">
							<thead>
								<tr>
									<th>#Ref</th>
									<th>Id RDV</th>
									<th>Date prise RDV</th>
									<th>Nom & prénom(s)</th>
									<th>Id contrat</th>
									<th>Motif</th>
									<th>Date RDV</th>
									<th>Lieu RDV</th>

									<?php if (!empty($etat) && in_array($etat, ["2", "3"])): ?>
										<th>Détail</th>
									<?php elseif ($etat === "0"): ?>
										<th>Motif rejet</th>
									<?php else: ?>
										<th>Délais</th>
									<?php endif; ?>

									<th>État</th>
									<th>Action</th>
								</tr>
							</thead>

							<tbody>
								<?php if (!empty($liste_rdvs)) : ?>
									<?php foreach ($liste_rdvs as $i => $rdv) :

										$retourEtat = Config::tablo_statut_rdv[$rdv->etat] ?? [
											'libelle' => 'Inconnu',
											'color_statut' => 'badge badge-secondary'
										];

										$dateRdvRaw = $rdv->daterdveff ?? null;
										$dateRdvObj = $dateRdvRaw ? new DateTime($dateRdvRaw) : null;
										$dateToday = new DateTime();

										$dateRdvAffiche = $dateRdvObj ? $dateRdvObj->format('d/m/Y') : '';

										$delai = $fonction->getDelaiRDV($dateRdvRaw, $rdv->traiterLe ?? null);
										$libDelai = $delai['libelle'] ?? '';
										$badgeDelai = $delai['badge'] ?? 'badge badge-secondary';
									?>
										<tr id="ligne-<?= $i ?>">
											<td><?= $i + 1 ?></td>
											<td id="id-<?= $i ?>"><?= htmlspecialchars($rdv->idrdv) ?></td>
											<td><?= htmlspecialchars($rdv->dateajou) ?></td>

											<td class="text-wrap">
												<?= htmlspecialchars($rdv->nomclient) ?>
												<p class="mb-0 text-dark" style="font-size:0.7em;">
													Téléphone :
													<strong><?= htmlspecialchars($rdv->tel) ?></strong>
												</p>
											</td>

											<td id="idcontrat-<?= $i ?>"><?= htmlspecialchars($rdv->police ?? '') ?></td>
											<td><?= htmlspecialchars($rdv->motifrdv ?? '') ?></td>

											<td id="daterdv-<?= $i ?>" style="font-weight:bold;">
												<?= $dateRdvAffiche ?>
											</td>

											<td style="color:#F9B233; font-weight:bold;">
												<?= htmlspecialchars(!empty($rdv->villes) ? strtoupper($rdv->villes) : 'Non mentionné') ?>
											</td>

											<td class="text-wrap">
												<?php if ($rdv->etat === "1"): ?>
													<span class="<?= htmlspecialchars($badgeDelai) ?>">
														<?= htmlspecialchars($libDelai) ?>
													</span>

												<?php elseif ($rdv->etat === "2"): ?>
													<p class="mb-0" style="font-size:0.7em;">
														Gestionnaire :
														<strong><?= htmlspecialchars($rdv->nomgestionnaire ?? 'N/A') ?></strong>
													</p>
													<p class="mb-0" style="font-size:0.7em;">
														Date transmission :
														<strong><?= !empty($rdv->transmisLe) ? date('d/m/Y', strtotime($rdv->transmisLe)) : '' ?></strong>
													</p>

													<?php if ($dateRdvObj && $dateRdvObj < $dateToday): ?>
														<p class="mb-0 text-danger" style="font-size:0.7em;">
															Date RDV expirée
														</p>
													<?php endif; ?>

												<?php elseif ($rdv->etat === "3"): ?>
													<p class="mb-0" style="font-size:0.7em;">
														Gestionnaire :
														<strong><?= htmlspecialchars($rdv->nomgestionnaire ?? 'N/A') ?></strong>
													</p>
													<p class="mb-0" style="font-size:0.7em;">
														Date traitement :
														<strong><?= !empty($rdv->traiterLe) ? date('d/m/Y H:i', strtotime($rdv->traiterLe)) : '' ?></strong>
													</p>
													<p class="mb-0" style="font-size:0.7em;">
														Traitement :
														<strong><?= htmlspecialchars($rdv->libelleTraitement ?? 'Non mentionné') ?></strong>
													</p>
												<?php endif; ?>
											</td>

											<td>
												<span class="<?= htmlspecialchars($retourEtat['color_statut']) ?>">
													<?= htmlspecialchars($retourEtat['libelle']) ?>
												</span>
											</td>

											<td class="text-wrap">
												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>">
													<i class="fa fa-eye"></i> Détail
												</button>

												<?php if (in_array($rdv->etat, ["1", "2"])): ?>
													<?php if ($dateRdvObj && $dateRdvObj < $dateToday): ?>
														<button class="btn btn-info btn-sm traiter" id="traiter-<?= $i ?>">
															<i class="fa fa-edit"></i> Modifier
														</button>
													<?php else: ?>
														<button class="btn btn-success btn-sm traiter"
															id="traiter-<?= $i ?>"
															style="background-color:#033f1f; color:white">
															<i class="fa fa-mouse-pointer"></i> Affecter
														</button>
														<button class="btn btn-success btn-sm reception" id="reception-<?= $i ?>">
															<i class="fa fa-check"></i> Traiter
														</button>
													<?php endif; ?>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>


				</div>

			</div>
		</div>
		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php";    ?>
		</div>
	</div>


	<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">
						Voulez vous rejeter la demande de rdv <span id="a_afficher_1" name="a_afficher_1"
							style="color:#033f1f!important; font-weight:bold;"> </span> ?
					</h4>
					<span style='color:red;'>Attention cette action est irreversible !!</span><br>
					<span style='color:seagreen'>le client sera notifier du rejet de la demande de rdv</span>
					</hr>
					<input type="text" id="idprestation" name="idprestation" hidden>
					<input type="text" id="observations" name="observations" hidden>

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="annulerRejet" name="annulerRejet"
								class="btn btn-secondary border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>
						<div class="col-6">
							<button type="button" id="validerRejet" name="validerRejet"
								class="btn btn-danger border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-check"></i></button>
							OUI
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="confirmationReception-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">
						Voulez vous traiter la demande de rdv <span id="a_afficher_2" name="a_afficher_2"
							style="color:#033f1f!important; font-weight:bold;"> </span> ?
					</h4>
					<span style='color:red;'>Attention cette action est irreversible !!</span><br>
					<span style='color:seagreen'>le rdv vous sera affecter automatiquement et le client sera notifier du traitement de la demande de rdv</span>
					</hr>
					<input type="text" id="idrdv" name="idrdv" hidden>
					<input type="text" id="observations" name="observations" hidden>

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="annulerRejet" name="annulerRejet"
								class="btn btn-secondary border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>
						<div class="col-6">
							<button type="button" id="confirmerReception" name="confirmerReception"
								class="btn btn-danger border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-check"></i></button>
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
					<button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success"
						style="background: #033f1f !important;">OK</button>
					<button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
				</div>
			</div>
		</div>
	</div>

	<!-- js -->
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
				location.href = "fiche-rdv";
			});


			//reception
			$(document).on('click', '.reception', function() {
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				const daterdv = $("#daterdv-" + index).html();
				// document.cookie = "idrdv=" + idrdv;
				// document.cookie = "idcontrat=" + idcontrat;
				// document.cookie = "action=traiter";
				// location.href = "fiche-rdv";
				//location.href = "traitement-rdv-gestionnaire";

				$("#idrdv").val(idrdv);
				$("#a_afficher_2").text(`n° ${idrdv} du ${daterdv}`);
				$('#confirmationReception-modal').modal('show');
				//alert(idrdv);
			});


			$("#confirmerReception").click(function() {

				const idrdv = $("#idrdv").val();
				const valideur = "<?= $_SESSION['id'] ?>";

				alert("affecter rdv " + idrdv + " à " + valideur);
				$.ajax({
					url: "../config/routes.php",
					method: "POST",
					dataType: "json",
					data: {
						idrdv: idrdv,
						traiterpar: valideur,
						observation: "Aucune observation",
						etat: "confirmerReceptionRDV"
					},
					success: function(response) {
						if (response != "-1") {
							document.cookie = "idrdv=" + idrdv;
							location.href = "traitement-rdv-gestionnaire";
						}
						// const msg = response !== '-1' && response !== '0' ?
						// `<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été confirmer !</h2></div>`:
						// `<div class="alert alert-danger" role="alert"><h2>Erreur lors de la confirmation du RDV <span class="text-danger">${idrdv}</span>.</h2></div>`;
						// $("#msgEchec").html(msg);
						// $('#notificationValidation').modal("show");


					},
					error: function(response, status, etat) {
						console.log(response, status, etat);
					}
				})
			})

			// $(".fa-trash").click(function(evt) {
			// 	const ind = extraireIndex(evt.target.id);
			// 	if (!ind) return;
			// 	const {
			// 		idrdv,
			// 		daterdv
			// 	} = extraireInfosRdv(ind);
			// 	$("#idprestation").val(idrdv);
			// 	$("#a_afficher_1").text(`n° ${idrdv} du ${daterdv}`);
			// 	$('#confirmation-modal').modal('show');
			// });


		})

		// $("#validerRejet").click(function() {
		// 	const idrdv = $("#idprestation").val();
		// 	const valideur = "<?= $_SESSION['id'] ?>";
		// 	$.ajax({
		// 		url: "config/routes.php",
		// 		method: "POST",
		// 		dataType: "json",
		// 		data: {
		// 			idrdv,
		// 			motif: "",
		// 			traiterpar: valideur,
		// 			observation: "Aucune observation",
		// 			etat: "confirmerRejetRDV"
		// 		},
		// 		success: function(response) {
		// 			const msg = response !== '-1' && response !== '0' ?
		// 				`<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été rejetée !</h2></div>` :
		// 				`<div class="alert alert-danger" role="alert"><h2>Erreur lors du rejet de la RDV <span class="text-danger">${idrdv}</span>.</h2></div>`;
		// 			$("#msgEchec").html(msg);
		// 			$('#notificationValidation').modal("show");
		// 		},
		// 		error: function(err) {
		// 			console.error("Erreur AJAX rejet RDV", err);
		// 		}
		// 	});
		// });


		$("#retourNotification").click(function() {
			$('#notificationValidation').modal('hide');
			location.reload(); // recharge la page au lieu de forcer vers detail-rdv
		});



		function extraireIndex(id) {
			let result = id.split('-');
			return result[1];
		}

		function extraireInfosRdv(index) {
			return {
				idrdv: $("#id-" + index).html(),
				idcontrat: $("#idcontrat-" + index).html(),
				daterdv: $("#daterdv-" + index).html()
			};
		}
	</script>



</body>


</html>