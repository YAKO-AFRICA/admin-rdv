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

	//echo $filtre; exit;
	if ($filtre) {
		list(, $conditions) = explode('AND', $filtre, 2);
		$plus = " WHERE $conditions ";
	}
	//echo $plus; exit;
} else {
	$plus = " WHERE etape != '1'  AND YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE()) ";
}

$sqlSelect = "
    SELECT 
        tblrdv.*,
        CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
        users.telephone AS telgest,
        users.adresse AS localisation,
        users.email AS mailgest,
        TRIM(tblvillebureau.libelleVilleBureau) AS villes
    FROM tblrdv
    LEFT JOIN users ON tblrdv.gestionnaire = users.id
    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
    $plus
    ORDER BY tblrdv.idrdv DESC
";



$liste_prestations = $fonction->_getSelectDatabases($sqlSelect);
$effectue = is_array($liste_prestations) ? count($liste_prestations) : 0;
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
								<h4>TRAITEMENT DES DEMANDES</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item " aria-current="page">Liste des demandes</li>
									<li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<div class="pd-20">
					<div style="float:left">
						<button class="btn btn-warning text-white" style="background:#F9B233;" onclick="retour()"><i class='fa fa-arrow-left'> Retour</i></button>
					</div>
				</div>
				<br>


				<?php
				if ($afficheuse) {



				?>
					<div class="card-box mb-30">
						<div class="pd-20">
							<h4 class="text-center" style="color:#033f1f;">Récapitulatif des traitements des prestations (<span style="color:#F9B233;"><?= $effectue ?></span>)</h4>
							<h6 class="text-center" style="color:#033f1f;"><?= $libelle ?></h6>

						</div>

						<div class="pb-20">
							<table class="table hover data-table-export nowrap" id="liste-recherche-rdv" style="width: 100%; font-size:10px;">
								<thead class="text-white" style="background-color: #033f1f;">
									<tr>
										<th class="table-plus datatable-nosort">#Ref</th>
										<th hidden>Idprestation</th>
										<th>Date<br>demande</th>
										<th>Demandeur</th>
										<th>Id contrat</th>
										<th>Motif RDV</th>
										<th>Date RDV</th>
										<th>Ville RDV</th>
										<th>Gestionnaire</th>
										<!--th>Montant souhaite</th-->
										<th>Etat </th>
										<th class="datatable-nosort"></th>

									</tr>
								</thead>
								<tbody>
									<?php
									if ($liste_prestations != null) {

										foreach ($liste_prestations as $i => $rdv) {
											$etat = (isset($rdv->etat) && array_key_exists($rdv->etat, Config::tablo_statut_rdv)) ? $rdv->etat : 1;
											$retourEtat = Config::tablo_statut_rdv[$etat];
									?>
											<tr class="text-wrap">
												<td class="table-plus"><?= $i + 1; ?></td>
												<td hidden id="id-<?= $i ?>"><?= $rdv->idrdv; ?></td>
												<td><?= $rdv->dateajou; ?></td>
												<td class="text-wrap">
													<?= $rdv->nomclient; ?>
													<p class="mb-0 text-dark" style="font-size: 0.7em;">
														Téléphone : <strong><?= $rdv->tel; ?></strong>
													</p>
												</td>
												<td class="text-wrap"><?= $rdv->police; ?></td>
												<td class="text-wrap"><?= $rdv->motifrdv ?? "Non renseigné"; ?></td>
												<td><?= $rdv->daterdv; ?></td>
												<td class="text-wrap" style="font-weight:bold; color:#F9B233;"><?= $rdv->villes ?? "Non renseigné"; ?></td>
												<td class="text-wrap" style="font-weight:bold; color:#F9B233;"><?= $rdv->nomgestionnaire ?? "Non renseigné"; ?></td>
												<td><span class="<?= $retourEtat["color_statut"]; ?>"><?= $retourEtat["libelle"]; ?></span></td>
												<td>
													<button class="btn btn-warning btn-sm view"
														id="view-<?= $i ?>"
														style="background-color:#F9B233;color:white">
														<i class="fa fa-eye"></i> Détail
													</button>
												</td>
											</tr>
										<?php } ?>
									<?php

									} else {

										echo '<script>alert("Desole aucun resultat n\'a ete trouve, veuillez ressayer");</script>';
									}

									?>
								</tbody>
							</table>
						</div>
					</div>

				<?php

				} else {
				?>
					<div class="card-box pd-10 height-100-p mb-15" id="myDIV">
						<div class="card-body mb-30">
							<div class="card-body " style="border:2px solid #F9B233;">
								<form method="POST">
									<div class="row">
										<div class=" col-md-6">
											<label for="" class=" control-label">Date de Rdv souhaitée du</label>
											<div class=" form-group" style="display:flex;flex-direction:row">
												<input type="date" name="rdvSouhaitLe" class=" form-control" id="" value=""> au
												<input type="date" name="rdvSouhaitAu" class=" form-control" id="" value="">
											</div>
										</div>
										<div class=" col-md-6">
											<label for="" class=" control-label">Date de Rdv effective du</label>
											<div class=" form-group" style="display:flex;flex-direction:row">
												<input type="date" name="rdvLe" class=" form-control" id="" value=""> au
												<input type="date" name="rdvAu" class=" form-control" id="" value="">
											</div>
										</div>
									</div>
									<div class="row">
										<div class=" col-md-6">
											<label for="" class=" control-label">Affecté du </label>
											<div class=" form-group" style="display:flex;flex-direction:row">
												<input type="date" name="affecteLe" class=" form-control" id="" value=""> au
												<input type="date" name="affecteAu" class=" form-control" id="" value="">
											</div>
										</div>
										<div class=" col-md-6">
											<label for="" class=" control-label">Saisie du</label>
											<div class=" form-group" style="display:flex;flex-direction:row">
												<input type="date" name="saisieLe" class=" form-control" id="" value=""> au
												<input type="date" name="saisieAu" class=" form-control" id="" value="">
											</div>
										</div>

									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<h6 style="color: #033f1f !important;">Nom</h6>
													<p> <input type="text" name="nom" id="nom" class="form-control" /></p>
												</div>
												<div class="col-md-4">
													<h6 style="color: #033f1f !important;">Prenom(s)</h6>
													<p> <input type="text" name="prenoms" id="prenoms" class="form-control" /></p>
												</div>

												<div class="col-md-4">
													<h6 style="color: #033f1f !important;"> Id proposition / Id contrat </h6>
													<p><input type="number" name="IdProposition" id="IdProposition" class="form-control" /></p>
												</div>

											</div>
										</div>

										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6 form-group">
													<h6 style="color: #033f1f !important;">Motif de rdv</h6>
													<?php echo $fonction->getSelectTypeRDVFiltre(); ?>
												</div>
												<div class="col-md-6 form-group">
													<h6 style="color: #033f1f !important;">Etat du rdv</h6>
													<?php echo $fonction->getSelectTypeEtapeRDV(); ?>
												</div>
												<div class="col-md-4 form-group">
													<h6 style="color: #033f1f !important;">Ville RDV</h6>
													<?= $fonction->getVillesBureau("", "") ?>
												</div>
												<div class="col-md-8 form-group">
													<h6 style="color: #033f1f !important;"> Gestionnaire </h6>
													<select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>

												</div>
											</div>
										</div>
									</div>


									<div class="modal-footer" id="footer">
										<button type="submit" name="filtreliste" id="filtreliste" class="btn btn-danger" style="background: #F9B233 !important;border-color: #F9B233 !important;">RECHERCHER</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php";    ?>
		</div>

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
	<!-- <script src="../config/fonction.js"></script> -->


	<script>
		function retour() {
			window.history.back();
		}

		$(document).ready(function() {

			var objetRDV = document.getElementById("villesRDV").value;
			if (objetRDV === "null") return;

			const [idvillesRDV, villesRDV] = objetRDV.split(";");

			getListeSelectAgentTransformations(idvillesRDV, villesRDV);
			//var dateRDVEffective = document.getElementById("daterdveff").value;
			//alert(objetRDV)
		})

		// Quand la ville change
		$('#villesRDV').change(function() {

			if ($(this).val() === "null") return;

			const [idvillesRDV, villesRDV] = $(this).val().split(";");

			console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  ");
			getListeSelectAgentTransformations(idvillesRDV, villesRDV);

		});


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

		function getListeSelectAgentTransformations(idVilleEff, villesRDV) {

			console.log("selection de gestionnaire de transformation", idVilleEff, villesRDV)
			$.ajax({
				url: "../config/routes.php",
				data: {
					idVilleEff: idVilleEff,
					etat: "afficherGestionnaire"
				},
				dataType: "json",
				method: "post",
				success: function(response, status) {

					console.log(response);

					if (response != "-1") {

						let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;
						$.each(response, function(indx, data) {
							let agent = data.gestionnairenom;
							html += `<option value="${data.id}|${agent}|${idVilleEff}|${villesRDV}" id="ob-${indx}">${agent}</option>`;
						});

						$("#ListeGest").html(html);
					} else {
						if (villesRDV != undefined) {
							villesRDV = villesRDV.toUpperCase();
							let html = `<option value="">[aucun agent de Transformations pour ${villesRDV}]</option>`;
							$("#ListeGest").html(html);
						}

					}

					//verifierActivationBouton(); // Vérifie après chargement
				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});
		}
	</script>
</body>


</html>