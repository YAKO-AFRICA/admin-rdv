<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: ../index.php');
	exit;
}

include("../autoload.php");

$plus = "";
$libelle = "";
$afficheuse = false;



if (isset($_REQUEST['filtreliste'])) {
	$afficheuse = true;
	$retourPlus = $fonction->getFiltreuseRDV();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];

	if ($filtre) {
		list(, $conditions) = explode('AND', $filtre, 2);
		$plus = " WHERE $conditions ";
	}
} else {
	$plus = " WHERE etape != '1' ";
}

if (isset($_REQUEST['reference']) && $_REQUEST['reference'] != null) {

	$afficheuse = true;
	$reference = GetParameter::FromArray($_REQUEST, 'reference');
	$sqlSelect = " SELECT * FROM  tbl_detail_bordereau_rdv WHERE reference = '" . $reference . "'  ORDER BY created_at DESC ";
	// echo $sqlSelect;	exit;
} else {
	$sqlSelect = " SELECT * FROM  tbl_bordereau_rdv  ORDER BY created_at DESC ";
}
$liste_bordereau = $fonction->_getSelectDatabases($sqlSelect);
$effectue = is_array($liste_bordereau) ? count($liste_bordereau) : 0;

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
				<!-- Page Header -->
				<div class="page-header">
					<div class="row">
						<div class="col-md-12">
							<div class="title">
								<h4>Liste de Bordereau RDV</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">Liste de Bordereau RDV</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<!-- Bouton Retour -->
				<div class="pd-20 mb-3">
					<button class="btn btn-warning text-white" style="background:#F9B233;" onclick="retour()">
						<i class="fa fa-arrow-left"></i> Retour
					</button>
				</div>



				<div class="card-box mb-30">
					<div class="pd-20 text-center">
						<h4 style="color:#033f1f;">Récapitulatif des bordereaux de RDV (<span style="color:#F9B233;"><?= $effectue ?></span>)</h4>
					</div>

					<?php if ($afficheuse): ?>
						<div class="pb-20">
							<table class="data-table table stripe hover nowrap">
								<thead>
									<tr>
										<th>#Ref</th>
										<th hidden>Id</th>
										<th>Date</th>
										<th>RDV</th>
										<th>Souscripteur</th>
										<th>Type Operation</th>
										<th>Provision & cumul</th>
										<th>Valeur Rachat</th>
										<th>Observations</th>
										
									</tr>
								</thead>
								<tbody>
									<?php if ($liste_bordereau): ?>
										<?php foreach ($liste_bordereau as $i => $bordereau): ?>
											<?php
											
											if (isset($bordereau->valeurMaxAvance) && $bordereau->valeurMaxAvance != null) {
												$avance = number_format($bordereau->valeurMaxAvance, 0, ',', ' ') . " FCFA";
											} else {
												$avance = 0;
											}
											if (isset($bordereau->valeurMaxRachat) && $bordereau->valeurMaxRachat != null) {
												$Maxrachat = number_format($bordereau->valeurMaxRachat, 0, ',', ' ') . " FCFA";
											} else {
												$Maxrachat = 0;
											}

											if (isset($bordereau->valeurRachat) && $bordereau->valeurRachat != null) {
												$rachat = number_format($bordereau->valeurRachat, 0, ',', ' ') . " FCFA";
											} else {
												$rachat = 0;
											}

											if (isset($bordereau->provisionNette) && $bordereau->provisionNette != null) {
												$provisionNette = number_format($bordereau->provisionNette, 0, ',', ' ') . " FCFA";
											} else {
												$provisionNette = 0;
											}

											if (isset($bordereau->cumulRachatsPartiels) && $bordereau->cumulRachatsPartiels != null) {
												$cumulRachatsPartiels = number_format($bordereau->cumulRachatsPartiels, 0, ',', ' ') . " FCFA";
											} else {
												$cumulRachatsPartiels = 0;
											}

											if (isset($bordereau->cumulAvances) && $bordereau->cumulAvances != null) {
												$cumulAvances = number_format($bordereau->cumulAvances, 0, ',', ' ') . " FCFA";
											} else {
												$cumulAvances = 0;
											}

											if(isset($bordereau->telephone) && $bordereau->telephone != null){
												$telephone = '0'.substr($bordereau->telephone, -9);
											}
											else {
												$telephone = '';
											}


											?>
											<tr>
												<td><?= $i + 1; ?></td>
												<td hidden id="id-<?= $i ?>"><?= $bordereau->id; ?></td>
												<td><?= $bordereau->created_at; ?></td>
												<td class="text-wrap">
													N°<?= $bordereau->NumeroRdv; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														id proposition : <strong><?= $bordereau->IDProposition; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														produit : <strong><?= $bordereau->produit; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														date Effet : <strong><?= $bordereau->dateEffet; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														date Echeance : <strong><?= $bordereau->dateEcheance; ?></strong>
													</p>
												</td>
												<td class="text-wrap">
													<?= $bordereau->souscripteur; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														contact : <strong><?= $telephone; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														assure : <strong><?= $bordereau->assure; ?></strong>
													</p>
												</td>
												<td class="text-wrap"><?= $bordereau->typeOperation; ?></td>
												<td class="text-wrap"><?= $provisionNette; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														provision Nette : <strong><?= $provisionNette; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														cumul Avances : <strong><?= $cumulAvances; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														cumul Rachats Partiels : <strong><?= $cumulRachatsPartiels; ?></strong>
													</p>

												</td>
												<td class="text-wrap"><?=$rachat; ?>

													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														valeur Rachat : <strong><?= $rachat; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														valeur Max Rachat : <strong><?= $Maxrachat; ?></strong>
													</p>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														valeur Max Avance : <strong><?= $avance; ?></strong>
													</p>
												</td>
												<td class="text-wrap"><?= $bordereau->observation; ?>
												</td>
												
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="12" class="text-center text-danger">
												Aucun 결과 trouvé. Veuillez reafficher.
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>

					<?php else: ?>
						<div class="pb-20">
							<table class="data-table table stripe hover nowrap">
								<thead>
									<tr>
										<th>#Ref</th>
										<th hidden>Id</th>
										<th>Date</th>
										<th>Ville RDV</th>
										<th>Gestionnaire</th>
										<th>Periode Bordereau</th>
										<th>Reference</th>
										<th>État</th>
										<th colspan="2">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($liste_bordereau): ?>
										<?php foreach ($liste_bordereau as $i => $bordereau): ?>
											<?php
											if ($bordereau->periode_2 != null) {
												$periode = $bordereau->periode_1 . " au " . $bordereau->periode_2;
											} else {
												$periode = $bordereau->periode_1;
											}

											if ($bordereau->etat == 1) {
												$libelleEtat = "En cours";
												$colorEtat = "badge badge-secondary";
											} else {
												$libelleEtat = "Transmis";
												$colorEtat = "badge badge-success";
											}

											?>
											<tr>
												<td><?= $i + 1; ?></td>
												<td hidden id="id-<?= $i ?>"><?= $bordereau->reference; ?></td>
												<td><?= $bordereau->created_at; ?></td>
												<td class="text-wrap">
													<?= $bordereau->villes; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														id ville : <strong><?= $bordereau->id_villes; ?></strong>
													</p>
												</td>
												<td class="text-wrap"><?= $bordereau->gestionnaire; ?></td>
												<td class="text-wrap fw-bold text-warning"><?= $periode; ?></td>
												<td><span class="<?= $colorEtat; ?>"><?= $libelleEtat; ?></span></td>
												<td class="text-wrap"><?= $bordereau->reference; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														Auteur : <strong><?= $bordereau->auteur; ?></strong>
													</p>
												</td>
												<td colspan="2">
													<label class="btn btn-secondary" style="background-color:#F9B233;" for="click-<?= $i ?>">
														<i class="fa fa-eye-slash" id="click-<?= $i ?>"> Détail</i>
													</label>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="12" class="text-center text-danger">
												Aucun résultat trouvé. Veuillez réessayer.
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>


				</div>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php"; ?>
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
		function retour() {
			window.history.back();
		}

		$(document).ready(function() {

			
		})

		

		$(".fa-eye-slash").click(function(evt) {
			const [_, index] = evt.target.id.split('-');
			if (index !== undefined) {
				const reference = $("#id-" + index).text();

				document.cookie = "reference=" + reference;
				location.href = "liste-bordereau-rdv";
			}
		});

	
	</script>
</body>

</html>