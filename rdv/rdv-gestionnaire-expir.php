<?php


session_start();


if (!isset($_SESSION['id'])) {
	header('Location: ../index.php');
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

$plus = "";

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

$liste_rdvs = $fonction->getSelectRDVAfficherGestionnaire(trim($_SESSION['id']), $etat);
// if ($liste_rdvs != null) $effectue = count($liste_rdvs);
// else $effectue = 0;


if ($liste_rdvs != null) {

    $liste_rdvs = array_filter($liste_rdvs, function ($rdv) use ($fonction) {

        // On ne filtre que les RDV etat = 2
        if ($rdv->etat != "2") {
            return true;
        }

        // Si pas de date effective → on garde
        if (empty($rdv->daterdveff)) {
            return true;
        }

        // Calcul du délai
        $delai = $fonction->getDelaiRDV($rdv->daterdveff);

        // On EXCLUT seulement si expiré depuis PLUS de 10 jours
        if ($delai['etat'] === 'expire' && $delai['jours'] > 10) {
            return false;
        }

        return true;
    });

    // Réindexation du tableau
    $liste_rdvs = array_values($liste_rdvs);

    $effectue = count($liste_rdvs);

} else {
    $effectue = 0;
}

?>




<!DOCTYPE html>
<html>

<head>
	<?php include "../include/entete.php"; ?>
</head>
<style>
    .card {
        border-radius: 8px;
    }

    .card-header {
        border-bottom: none;
    }

    .small p {
        font-size: 13px;
        line-height: 1.3;
    }
</style>
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
								<h4><?= strtoupper("Liste des Rendez-vous " . $libelleTraitement." Expirés") ?></h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a
											href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page">
										<?= strtolower("Liste des Rendez-vous " . $libelleTraitement." Expirés") ?></li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<?php
				$retourStatut = $fonction->afficheuseGlobalStatistiqueRDV(" and gestionnaire = '" . trim($_SESSION['id']) . "'", "rdv-gestionnaire-expir");
				echo $retourStatut;
				?>
				<div class="card-box mb-30">
					<div class="pd-20">
						<h4 class="text-center text-uppercase" style="color:#033f1f; ">
							<?= strtoupper("Mes Rendez-vous ") ?> <span style="color:<?= $couleur ?>;"><?= $libelleTraitement ?></span> Expirés </h4>
					</div>
					<div class="pb-20">
						<div class="col text-center">
							<h5><?= "Total Ligne  : " ?> <span style="color:<?= $couleur ?> !important;"><?= $effectue ?></span> </h5>
						</div>
					</div>
					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th>Id</th>
									<th>Date</th>
									<th>Nom & prénom(s)</th>
									<th>Id contrat</th>
									<th>Motif</th>
									<th>Date RDV</th>
									<th>lieu RDV</th>
									<th hidden></th>
									<?php if (!empty($liste_rdvs) && ($liste_rdvs[0]->etat == "3")): ?>
										<th>Détail</th>
									<?php else: ?>
										<th>Délais</th>
									<?php endif; ?>
									<th class="table-plus datatable-nosort">Etat</th>
									<th class="table-plus datatable-nosort">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($liste_rdvs != null) {


									$effectue = count($liste_rdvs);
									for ($i = 0; $i <= ($effectue - 1); $i++) {

										$rdv = $liste_rdvs[$i];
										$lib_delai = null;
										$couleur_fond = null;
										$badge_delai = null;

										$delai = $fonction->getDelaiRDV($rdv->daterdveff);
										if ($rdv->etat == "2") {
											$lib_delai = $delai['libelle'];
											$couleur_fond = $delai['couleur'] ?? 'transparent';
											$badge_delai = $delai['badge'] ?? 'badge badge-secondary';
										}

										if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_rdv[$etat];
									
									

								?>		
										<tr id="ligne-<?= $i ?>" style="color: <?= htmlspecialchars($couleur_fond) ?>;">
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>"><?php echo $rdv->idrdv; ?></td>
											<td><?php echo $rdv->dateajou; ?></td>
											<td class="text-wrap"><?php echo $rdv->nomclient; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span style="font-weight:bold;"><?php echo $rdv->tel; ?></span>
												</p>
											</td>

											<td class="text-wrap" id="idcontrat-<?= $i ?>"><?php echo $rdv->police; ?></td>

											<td id="motifrdv-<?= $i ?>" class="text-wrap"><?php echo $rdv->motifrdv; ?> </td>
											<td id="daterdv-<?= $i ?>"><?php echo $rdv->daterdv; ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#F9B233!important;"><?php echo $rdv->villes; ?></td>
											<td id="lieurdv-<?= $i ?>" hidden><?php echo $rdv->idTblBureau . ";" . $rdv->villes; ?></td>
											<td>
												<?php if ($rdv->etat == "2"): ?>
													<span class="<?= htmlspecialchars($badge_delai) ?>"><?= $lib_delai ?></span>
												<?php elseif ($rdv->etat == "3"): ?>
													<span class="badge badge-secondary text-wrap text-white text-center mt-2"><?php echo $rdv->libelleTraitement; ?> </span>
												<?php endif; ?>
											</td>
											<td class="text-wrap">
												<?php if (!empty($rdv->etatTraitement) && $rdv->etatTraitement == "5") : ?>
													<span class="badge badge-warning text-wrap text-white text-center mt-2"><?php echo $retourEtat["libelle"] ?></span><br>
												<?php else : ?>
													<span class="badge badge-success text-wrap text-white text-center mt-2"><?php echo $retourEtat["libelle"] ?></span><br>
												<?php endif; ?>

											</td>


											<td class="table-plus text-center">

												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>" style="background-color:#F9B233;color:white"><i class="fa fa-eye"></i> Détail</button>
												<?php if ($rdv->etat == "2" && ($rdv->daterdveff < date('Y-m-d'))): ?>
													<button class="btn btn-danger btn-sm modifierRDV" id="modifier-<?= $i ?> " style="background-color:red; color:white"><i class="fa fa-edit"></i> Modifier rdv</button>
												<?php endif; ?>
												<?php if ($rdv->etat == "2" && ($rdv->daterdveff >= date('Y-m-d'))): ?>

													<button class="btn btn-success btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:#033f1f; color:white"><i class="fa fa-mouse-pointer"></i> Traiter</button>
												<?php endif; ?>

												<!-- <?php if ($rdv->etatTraitement == "5"): ?>
													<button class="btn btn-primary btn-sm modifier" id="modifier-<?= $i ?> " style="background-color:blue; color:white"><i class="fa fa-edit"></i> Modifier rdv</button>
												<?php endif; ?> -->

											</td>

										</tr>
								<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-content">
				</div>
			</div>
			<hr>

		</div>
		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php";    ?>
		</div>
	</div>
	</div>


	<div class="modal fade" id="modifierRDV-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title text-info font-weight-bold">Information sur la demande de RDV</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-row">
						<div class="form-group col-md-4">
							<label>N° RDV :</label>
							<input type="text" class="form-control" id="idrdv" name="idrdv" readonly>
						</div>

						<div class="form-group col-md-8">
							<label>ID contrat / N° police(s) :</label>
							<input type="text" class="form-control" id="idcontrat" name="idcontrat" readonly>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-8">
							<label>Ville choisie :</label>
							<input type="text" class="form-control" id="villesR" name="villesR" readonly>
						</div>

						<div class="form-group col-md-4">
							<label>Motif :</label>
							<input type="text" class="form-control" id="motifrdv" name="motifrdv" readonly>
						</div>
					</div>

					<div class="form-group">
						<label>Date RDV <span class="text-danger">*</span> :</label>
						<input class="form-control" type="date" id="daterdveff" name="daterdveff" onchange="verifierReceptionRDV()" min="<?= date('Y-m-d') ?>" required>
						<span id="errorDate" class="text-danger"></span>
					</div>
					<input type="hidden" id="idTblBureau" name="idTblBureau">

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
					<button type="submit" name="modifierRDV" id="modifierRDV" class="btn btn-warning">Modifier RDV</button>
				</div>

			</div>
		</div>
	</div>



	<div class="modal fade" id="modifierRDV-modal2" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">

				<!-- HEADER -->
				<div class="modal-header bg-light">
					<h5 class="modal-title text-info font-weight-bold">
						Modification de la demande de RDV
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<!-- BODY -->
				<div class="modal-body">
					<span id="enteteClientRDV"></span>
					<hr>
					<div class="card-box mb-30">
						<div class="pd-20">
							<div class="form-row">
								<input type="text" class="form-control" id="idcontrat2" name="idcontrat2" hidden>
								<input type="text" class="form-control" id="idrdv2" name="idrdv2" hidden>
								<input type="text" class="form-control" id="villesR2" name="villesR2" hidden>
								<input type="text" class="form-control" id="motifrdv2" name="motifrdv2" hidden>

							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label>
										Date RDV
										<span class="text-danger">*</span>
									</label>
									<input
										type="date"
										class="form-control"
										id="daterdveff2"
										name="daterdveff2"
										onblur="checkDate('1')" min="<?= date('Y-m-d') ?>"
										required>
									<small id="errorDate2" class="text-danger"></small>
								</div>
								<div class="form-group col-md-7">
									<label>
										Motif de modification
										<span class="text-danger">*</span>
									</label>
									<textarea
										class="form-control"
										id="motifmodif"
										name="motifmodif"
										rows="3"
										required></textarea>
								</div>
							</div>
							<input type="hidden" id="idTblBureau2" name="idTblBureau2">
						</div>
					</div>
				</div>

				<!-- FOOTER -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						Fermer
					</button>
					<button type="submit" id="modifierDateRDV" name="modifierDateRDV" class="btn btn-warning">
						<i class="fa fa-save"></i> Modifier RDV
					</button>
				</div>

			</div>
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
					<span style=' color:red;'>Attention cette action est irreversible !!</span><br>
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
	<script src="../vendors/scripts/rdv-expire-cron.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

	<script>
		$(document).ready(function() {

			$("#modifierDateRDV").prop("disabled", true);

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
				location.href = "traitement-rdv-gestionnaire";
			});

			//modifier
			$(document).on('click', '.modifier', function() {

				//alert("modifier " + this.id)
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				const lieurdv = $("#lieurdv-" + index).html();
				const motifrdv = $("#motifrdv-" + index).html();


				const tablo = lieurdv.split(";");
				const idTblBureau = tablo[0];
				const villes = tablo[1];

				//alert(lieurdv + "  --  " + motifrdv)
				$("#idrdv").val(idrdv);
				$("#idcontrat").val(idcontrat);
				$("#villesR").val(villes);
				$("#idTblBureau").val(idTblBureau);
				$("#motifrdv").val(motifrdv);

				$("#modifierRDV-modal").modal('show');
			})

			//modifier
			$(document).on('click', '.modifierRDV', function() {

				//alert("modifierRDV " + this.id)
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				const lieurdv = $("#lieurdv-" + index).html();
				const motifrdv = $("#motifrdv-" + index).html();

				if (idrdv == "undefined") return;
				$.ajax({
					url: "../config/routes.php",
					method: "POST",
					dataType: "json",
					data: {
						idrdv: idrdv,
						etat: "rechercherRDV"
					},
					success: function(response) {
						console.log(response);
						//ouvrirModalModifierRDV(response)

						let afficheuse = `
                        <div class="row g-3">

                            <!-- DÉTAIL CLIENT -->
                            <div class="col-md-7">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 fw-bold" style="color:#033f1f;">
                                            Détail du client
                                        </h6>
                                        <div style="border-top:3px solid #033f1f;width:60px;"></div>
                                    </div>

                                    <div class="card-body py-2 px-3 small">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Titre :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.titre ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Nom & Prénom :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.nomclient ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Date naissance :</span><br>
                                                    <span class="fw-bold">${response.datenaissance ?? ''}</span>
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Résidence :</span><br>
                                                    <span class="fw-bold text-uppercase">${response.lieuresidence ?? ''}</span>
                                                </p>
                                                <p class="mb-1">
                                                    <span class="text-muted">Téléphone :</span><br>
                                                    <span class="fw-bold">${response.tel ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Email :</span><br>
                                                    <span class="fw-bold">${response.email ?? ''}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DÉTAIL RDV -->
                            <div class="col-md-5">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 fw-bold" style="color:#033f1f;">
                                            Détail du RDV
                                        </h6>
                                        <div style="border-top:3px solid #033f1f;width:60px;"></div>
                                    </div>

                                    <div class="card-body py-2 px-3 small">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">ID RDV :</span><br>
                                                    <span class="fw-bold">${response.idrdv ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Contrat / Police :</span><br>
                                                    <span class="fw-bold">${response.police ?? ''}</span>
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <p class="mb-1">
                                                    <span class="text-muted">Ville :</span><br>
                                                    <span class="fw-bold">${response.villes ?? ''}</span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="text-muted">Motif :</span><br>
                                                    <span class="fw-bold">${response.motifrdv ?? ''}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>`;

						$("#enteteClientRDV").html(afficheuse);
						$("#idrdv2").val(response.idrdv);
						$("#idcontrat2").val(response.police);
						$("#villesR2").val(response.villes);
						$("#idTblBureau2").val(response.idTblBureau);
						$("#motifrdv2").val(response.motifrdv);
						$("#modifierRDV-modal2").modal('show');

					},
					error: function(response, status, etat) {
						console.log(response, status, etat);
					}
				});
			})



			$(".fa-trash").click(function(evt) {
				const ind = extraireIndex(evt.target.id);
				if (!ind) return;
				const {
					idrdv,
					daterdv
				} = extraireInfosRdv(ind);
				$("#idprestation").val(idrdv);
				$("#a_afficher_1").text(`n° ${idrdv} du ${daterdv}`);
				$('#confirmation-modal').modal('show');
			});


		})


		$("#modifierDateRDV").click(function() {

			const idrdv = document.getElementById("idrdv2").value;
			const idcontrat = document.getElementById("idcontrat2").value;
			const idVilleEff = document.getElementById("idTblBureau2").value;
			const daterdveff = document.getElementById("daterdveff2").value;
			const motifmodif = document.getElementById("motifmodif").value;
			console.log(idrdv, idcontrat, idVilleEff, daterdveff, motifmodif);
			if (!daterdveff) {
				alert("Veuillez renseigner la date du RDV SVP !!");
				document.getElementById("daterdveff2").focus();
				return false;
			}
			if (!motifmodif) {
				alert("Veuillez renseigner le motif de modification SVP !!");
				document.getElementById("motifmodif").focus();
				return false;
			}

			$.ajax({
				url: "../config/routes.php",
				method: "POST",
				dataType: "json",
				data: {
					idrdv: idrdv,
					idcontrat: idcontrat,
					idVilleEff: idVilleEff,
					daterdveff: daterdveff,
					motifmodif: motifmodif,
					etat: "modifierRDVByGestionnaire"
				},
				success: function(response) {
					console.log(response);

					if (response !== '-1' && response !== '0') {
						const msg = `<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été modifiée !</h2></div>`;
						$("#msgEchec").html(msg);
						$('#notificationValidation').modal("show");
					} else {
						const msg = `<div class="alert alert-danger" role="alert"><h2>Le RDV <span class="text-danger">${idrdv}</span> n'a pas été modifiée !</h2></div>`;
						$("#msgEchec").html(msg);
						$('#notificationValidation').modal("show");
					}
				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});

		})


		$("#modifierRDV").click(function() {

			const idrdv = document.getElementById("idrdv").value;
			const idcontrat = document.getElementById("idcontrat").value;
			const idVilleEff = document.getElementById("idTblBureau").value;
			const daterdveff = document.getElementById("daterdveff").value;

			const dateStr = daterdveff; // format YYYY-MM-DD
			if (!dateStr) {
				alert("Veuillez renseigner la date du RDV SVP !!");
				document.getElementById("daterdveff").focus();
				return false;
			} else {
				console.log(dateStr);

				$.ajax({
					url: "../config/routes.php",
					method: "POST",
					dataType: "json",
					data: {
						idrdv: idrdv,
						idcontrat: idcontrat,
						idVilleEff: idVilleEff,
						daterdveff: dateStr,
						etat: "modifierRDVByGestionnaire"
					},
					success: function(response) {
						console.log(response);

						if (response !== '-1' && response !== '0') {
							const msg = `<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été modifiée !</h2></div>`;
							$("#msgEchec").html(msg);
							$('#notificationValidation').modal("show");
						} else {
							const msg = `<div class="alert alert-danger" role="alert"><h2>Le RDV <span class="text-danger">${idrdv}</span> n'a pas été modifiée !</h2></div>`;
							$("#msgEchec").html(msg);
							$('#notificationValidation').modal("show");
						}
					},
					error: function(response, status, etat) {
						console.log(response, status, etat);
					}
				});
			}
		});



		$("#validerRejet").click(function() {
			const idrdv = $("#idprestation").val();
			const valideur = "<?= $_SESSION['id'] ?>";
			$.ajax({
				url: "../config/routes.php",
				method: "POST",
				dataType: "json",
				data: {
					idrdv,
					motif: "",
					traiterpar: valideur,
					observation: "Aucune observation",
					etat: "confirmerRejetRDV"
				},
				success: function(response) {
					const msg = response !== '-1' && response !== '0' ?
						`<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été rejetée !</h2></div>` :
						`<div class="alert alert-danger" role="alert"><h2>Erreur lors du rejet de la RDV <span class="text-danger">${idrdv}</span>.</h2></div>`;
					$("#msgEchec").html(msg);
					$('#notificationValidation').modal("show");
				},
				error: function(err) {
					console.error("Erreur AJAX rejet RDV", err);
				}
			});
		});


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

		function getJoursReception(idVilleEff, callback) {
			$.ajax({
				url: "../config/routes.php",
				type: "POST",
				dataType: "json",
				data: {
					idVilleEff: idVilleEff,
					etat: "receptionJourRdv"
				},
				success: function(response) {
					console.log("Jours autorisés reçus :", response);

					// Nettoyage : tableau de nombres
					const joursAutorises = response.map(j => Number(j));

					callback(joursAutorises);
				},
				error: function(xhr, status, error) {
					console.error("Erreur AJAX :", error);
					callback([]); // Aucun jour autorisé si erreur
				}
			});
		}

		function verifierReceptionRDV() {

			const idVilleEff = document.getElementById("idTblBureau").value;
			const villesR = document.getElementById("villesR").value;
			const dateStr = document.getElementById("daterdveff").value;

			if (!dateStr) {
				alert("Veuillez choisir une date.");
				return;
			}

			// Récupération du numéro du jour
			const parts = dateStr.split("-");
			const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
			const dayNumber = dateObj.getDay(); // 0=Dim, 6=Sam

			const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
			const jourNom = jours[dayNumber];

			console.log("Date sélectionnée :", dateStr);
			console.log("Jour :", jourNom, "| Numéro :", dayNumber);

			// Bloquer weekend
			if (dayNumber === 0 || dayNumber === 6) {
				alert("❌ Les rendez-vous ne peuvent pas être pris le week-end.");
				$("#errorDate").html("❌ Les rendez-vous ne peuvent pas etre pris le week-end.");
				return;
			}

			// Récupérer les jours autorisés depuis l'API
			getJoursReception(idVilleEff, function(joursAutorises) {

				//console.log("Jours autorisés :", joursAutorises);

				// Vérification : est-ce que dayNumber est dans les jours autorisés ?
				const autorise = joursAutorises.includes(dayNumber);

				if (autorise) {
					//alert("✅ Le jour " + jourNom + " est autorisé pour la réception !");
					$("#errorDate").html("✅ <span style='color:green;'> Le " + jourNom + " est autorisé pour la réception pour la ville de <b>" + villesR + "</b>!</span>");
				} else {
					//alert("❌ Le jour " + jourNom + " n’est pas autorisé pour la réception.");
					$("#errorDate").html("❌ <span style='color:red;'> Le " + jourNom + " n’est pas autorisé pour la réception pour la ville de <b>" + villesR + "</b>.</span>");
				}
			});
		}


		function checkDate() {

			const villesR = document.getElementById("villesR2").value;
			const dateStr = document.getElementById("daterdveff2").value;
			var idVilleEff = document.getElementById("idTblBureau2").value;


			if (!dateStr) {
				alert("Veuillez choisir une date.");
				return;
			}

			// Récupération du numéro du jour
			const parts = dateStr.split("-");
			const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
			const dayNumber = dateObj.getDay(); // 0=Dim, 6=Sam

			const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
			const jourNom = jours[dayNumber];

			console.log("Date sélectionnée :", dateStr);
			console.log("Jour :", jourNom, "| Numéro :", dayNumber);

			// Bloquer weekend
			if (dayNumber === 0 || dayNumber === 6) {

				alert("❌ Les rendez-vous ne peuvent pas être pris le week-end.");
				$("#errorDate2").html("❌ Les rendez-vous ne peuvent pas etre pris le week-end.");
				//	desactiver le bouton modifierRDV
				$("#modifierDateRDV").prop("disabled", true);
				return;
			}

			// Récupérer les jours autorisés depuis l'API
			getJoursReception(idVilleEff, function(joursAutorises) {

				//console.log("Jours autorisés :", joursAutorises);
				// Vérification : est-ce que dayNumber est dans les jours autorisés ?
				const autorise = joursAutorises.includes(dayNumber);

				if (autorise) {
					//	activer le bouton modifierRDV
					$("#modifierDateRDV").prop("disabled", false);
					//alert("✅ Le jour " + jourNom + " est autorisé pour la réception !");
					$("#errorDate2").html("✅ <span style='color:green;'> Le " + jourNom + " est autorisé pour la réception pour la ville de <b>" + villesR + "</b>!</span>");
				} else {

					//alert("❌ Le jour " + jourNom + " n’est pas autorisé pour la réception.");
					$("#errorDate2").html("❌ <span style='color:red;'> Le " + jourNom + " n’est pas autorisé pour la réception pour la ville de <b>" + villesR + "</b>.</span>");
					//	desactiver le bouton modifierRDV
					$("#modifierDateRDV").prop("disabled", true);
				}
			});
		}
	</script>



</body>

</html>