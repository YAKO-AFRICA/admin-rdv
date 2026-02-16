<?php
session_start();

if (empty($_SESSION['id'])) {
	header('Location: ../index.php');
	exit;
}

include("../autoload.php");

$plus = " WHERE etape != '1' ";
$libelle = "";
$afficheuse = false;
$periode = "";
$VilleRDV = "";
$nomGest = "";
$effectue = 0;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<?php include "../include/entete.php"; ?>
	<title>Journal RDV</title>
</head>

<body>
	<?php include "../include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-12">
							<div class="title">
								<h4>EXTRACTION BORDEREAU RDV</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">EXTRACTION BORDEREAU RDV</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<!-- Bouton retour -->
				<div class="mb-3">
					<button class="btn btn-warning" onclick="retour()">
						<i class="fa fa-arrow-left"></i> Retour
					</button>
				</div>

				<!-- Titre RDV -->
				<div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
					<div class="card-body text-center ">
						<h3 style="color:white">
							<span class="text-warning">
								Extraction bordereau de RDV
							</span>

						</h3>
						<p>Veuillez renseigner la periode de recherche dans le formulaire ci-dessous </p>
					</div>
				</div>

				<!-- FORMULAIRE -->
				<div class="card-box pd-10 mb-15" id="formFiltreRDV">
					<div class="card-body" style="border:2px solid #F9B233;">

						<div class="row">
							<div class="col-md-6">
								<h6>Affecté du <span class="text-danger">*</span></h6>
								<input type="date" id="affecteLe" class="form-control">
							</div>
							<div class="col-md-6">
								<h6>Au</h6>
								<input type="date" id="affecteAu" class="form-control">
							</div>
						</div>

						<div class="row d-none">
							<div class="col-md-6">
								<h6>Date RDV du <span class="text-danger">*</span></h6>
								<input type="date" id="rdvLe" class="form-control">
							</div>
							<div class="col-md-6">
								<h6>Au</h6>
								<input type="date" id="rdvAu" class="form-control">
							</div>
						</div>

						<div class="row mt-3">
							<div class="col-md-6">
								<h6>Ville RDV</h6>
								<?= $fonction->getVillesBureau("", "villesRDV") ?>
							</div>
							<div class="col-md-6">
								<h6>Gestionnaire</h6>
								<select id="ListeGest" class="form-control"></select>
							</div>
						</div>

						<div class="mt-4">
							<button type="button" id="filtreliste"
								class="btn btn-danger"
								style="background:#F9B233;border-color:#F9B233">
								RECHERCHER
							</button>

							<div id="loaderRDV" style="display:none" class="mt-3 text-center">
								<div class="spinner-border text-warning"></div>
								<p>Recherche en cours...</p>
							</div>
						</div>

						<div class="alert-zone mt-3"></div>
					</div>
				</div>
				<hr>

				<!-- RESULTATS -->
				<div class="card-box mb-4" id="zoneResultats" style="display:none;">
					<!-- Titre RDV -->
					<div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
						<div class="card-body text-center ">
							<h5 style="color:white"><span class="text-warning"> Résultat de la recherche</span></h5>
						</div>
					</div>
					<div class="row pd-20 " id="filtreResultats">
					</div>
				</div>
				<hr>
				<!-- TABLEAU -->
				<div class="card-box mb-4" id="zoneAffichage" style="display:none;">
					<div class="card-box mb-30">

						<div class="pd-20 mb-3">
							<div class="row flex-row">
								<div class="col-md-6">
									<h4 class="text-blue h4" style="color:#033f1f!important;"> Total Ligne rdv affecté pour cette extraction ( <span style="color:#F9B233;" id="totalLignes"> - </span> )</h4>
								</div>
								<div class="col-md-6 text-right">
									<button type="button" class="btn btn-info  bx bx-cloud-upload px-5" id="exportButton"
										name="exportButton">Exporter vers excel</button>
								</div>
							</div>
						</div>
						<div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
						<div class="pb-20" style="display:none" id="tableAffichageExtraction">
							<table class="table table-bordered table-striped nowrap" id="liste-extraction-bordereau-affichage">
								<thead>
									<tr>
										<th><input type="checkbox" class="select-all checkbox" id="checked-all"></th>
										<th>N°</th>
										<th>Id RDV</th>
										<th>Id contrat</th>
										<th>Demandeur</th>
										<th>Motif RDV</th>
										<th>Date RDV</th>
										<th>Ville RDV</th>
										<th>Gestionnaire</th>
										<th class="datatable-nosort">État</th>
									</tr>
								</thead>
								<tbody id="body-extraction-affichage" style="width: 100%;">
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="pb-20" style="display:none" id="zoneAffichageExtraction" hidden>
					<table class="table stripe hover" id="liste-extraction-bordereau-rdv" style="width:100%; font-size:10px;">
						<thead>
							<tr class="text-wrap">
								<th class="d-none"><input type="checkbox" class="select-all checkbox" id="checked-hidden-all"></th>
								<th>N°</th>
								<th>Date prise RDV</th>
								<th>code RDV</th>
								<th>Id RDV</th>
								<th>Id contrat</th>
								<th>Demandeur</th>
								<th>Date naissance</th>
								<th>Telephone</th>
								<th>Motif RDV</th>
								<th>Date RDV Souhaité</th>
								<th>Date RDV Effectif</th>
								<th>Ville RDV</th>
								<th>Gestionnaire</th>
								<th>ref Gestionnaire</th>
								<th>Code agent</th>
								<th>Etat</th>
							</tr>
						</thead>
						<tbody id="body-extraction-bordereau-rdv" style="width: 100%;">
						</tbody>
					</table>
						
				</div>
			</div>
		</div>

		<div class="modal fade" id="notification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
			aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content ">
					<div class="modal-body text-center">
						<div class="form-group">
							<h2><span id="a_afficher"></span></h2>
						</div>
						<div class="card-body radius-12 w-100">
							<span id="a_afficher2"></span>
						</div>
					</div>
					<div class="modal-footer">
						<div id="closeNotif">
							<button type="button" id="closeNotif" class="btn btn-secondary"
								data-dismiss="modal">FERMER</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php"; ?>
		</div>
	</div>

	<!-- JS -->
	<script src="../vendors/scripts/core.js"></script>
	<script src="../vendors/scripts/script.min.js"></script>
	<script src="../vendors/scripts/process.js"></script>
	<script src="../vendors/scripts/layout-settings.js"></script>
	<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
	<script src="../src/plugins/datatables/js/pdfmake.min.js"></script>
	<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
	<script src="../vendors/scripts/datatable-setting.js"></script>
	<script src="../vendors/scripts/rdv-expire-cron.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

	<script>
		let tableAffichage = null;

		$("#zoneResultats").hide();
		$("#tableAffichageExtraction").hide();
		$("#zoneAffichage").hide();
		$("#zoneAffichageExtraction").hide();

		function retour() {
			window.history.back();
		}

		$(document).ready(function() {

			$("#zoneResultats").hide();

		});



		$("#filtreliste").on("click", function() {

			var objetRDV = document.getElementById("villesRDV").value;
			var affecteLe = document.getElementById("affecteLe").value;
			var affecteAu = document.getElementById("affecteAu").value;
	
			var rdvLe = document.getElementById("rdvLe").value;
			var rdvAu = document.getElementById("rdvAu").value;
			var ListeGest = document.getElementById("ListeGest").value;
			//var etaperdv = document.getElementById("etaperdv").value;

			// console.log(objetRDV, affecteLe, affecteAu, rdvLe, rdvAu, ListeGest);

			var periode = "";
			var agent = "";
			var villes = "";
			var etatRDV = "";
			var lib_fichier = "";

			if (affecteLe == "") {
				alert("Veuillez renseigner la periode de début !!");
				document.getElementById("affecteLe").focus();
				return false;
			}
			if (affecteAu == "") {
				alert("Veuillez renseigner la periode de fin !!");
				document.getElementById("affecteAu").focus();
				return false;
			}
			// if (rdvLe == "" && rdvAu == "" && objetRDV == "" && ListeGest == "") {
			// 	alert("Veuillez renseigner la periode de recherche dans le formulaire ci-dessous ");
			// 	document.getElementById("rdvLe").focus();
			// 	return false;
			// }



			if (affecteLe != "" && affecteAu != "") {
				//formater en dd/mm/yyyy
				var date1 = new Date(affecteLe);
				var date2 = new Date(affecteAu);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear() + " au " + date2.getDate() + "/" + (date2.getMonth() + 1) + "/" + date2.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear() + " au " + date2.getDate() + "_" + (date2.getMonth() + 1) + "_" + date2.getFullYear();
			} else if (affecteLe != "" && affecteAu == "") {
				var date1 = new Date(affecteLe);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
			} else if (affecteLe == "" && affecteAu != "") {
				var date1 = new Date(affecteAu);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
			}

			// console.log(periode);

			if (rdvLe != "" && rdvAu != "") {
				//formater en dd/mm/yyyy
				var date1 = new Date(rdvLe);
				var date2 = new Date(rdvAu);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear() + " au " + date2.getDate() + "/" + (date2.getMonth() + 1) + "/" + date2.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear() + " au " + date2.getDate() + "_" + (date2.getMonth() + 1) + "_" + date2.getFullYear();
			} else if (rdvLe != "" && rdvAu == "") {
				var date1 = new Date(rdvLe);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
			} else if (rdvLe == "" && rdvAu != "") {
				var date1 = new Date(rdvAu);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
			}



			if (objetRDV != "") {
				const [idvillesRDV, villesRDV] = objetRDV.split(";");
				villes = villesRDV;
			}
			if (ListeGest != "") {
				const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = ListeGest.split("|");
				agent = nomgestionnaire + " (" + idgestionnaire + ")";
			}

			afficherLoader();

			$.ajax({
				url: "../config/routes.php",
				data: {
					objetRDV: objetRDV,
					rdvLe: rdvLe,
					rdvAu: rdvAu,
					affecteLe: affecteLe,
					affecteAu: affecteAu,
					ListeGest: ListeGest,
					//etaperdv: etaperdv,
					etat: "extraireBordereau"

				},
				dataType: "json",
				method: "post",
				success: function(response, status) {

					cacherLoader();
					if (!response || response.length === 0) {
						afficherMessage("Aucun résultat", "warning");
						return;
					}

					if (response != "-1") {

						let total = response.length;
						let enteteHtml = `<div class="col-md-6 text-left">
								<p><span class="text-color">Période :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;">${periode}</span></p>
								<p><span class="text-color">Total ligne(s) :</span> <span style="text-transform:uppercase; font-weight:bold;" class="badge badge-info">${total}</span>
								</p>
							</div>
							<div class="col-md-6 text-center">
								<p><span class="text-color">Ville :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;">${villes}</span></p>
								<p><span class="text-color">Gestionnaire :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;">${agent}</span></p>
								</p>
							</div>
							<input type="text" id="nom_fichier"  name="nom_fichier" value="${lib_fichier}" hidden>
							`;
						$('#filtreResultats').html(enteteHtml);
						$("#zoneResultats").show();
						$('#totalLignes').text(total);


						remplirTable(response, periode, agent, villes, etatRDV, lib_fichier);
						remplirTableExtraction(response, periode, agent, villes, etatRDV, lib_fichier);
						


					} else {
						//$("#zoneResultats").hide();
						//alert("Aucun RDV extrait pour cette periode");
						afficherMessage("Aucun RDV extrait pour cette periode", "warning");
						return;
					}

				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});


		});



		// Quand la ville change 
		$('#villesRDV').change(function() {
			if ($(this).val() === "null") return;
			const [idvillesRDV, villesRDV] = $(this).val().split(";");
			console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ") ");
			getListeSelectAgentTransformations(idvillesRDV, villesRDV);
		});


		// Action détail RDV
		$(".btn-warning").on("click", function() {
			document.cookie = "idrdv=" + $(this).data("id");
			document.cookie = "idcontrat=" + $(this).data("contrat");
			document.cookie = "action=traiter";
			location.href = "detail-rdv";
		});


		// ===============================
		// FONCTION : Synchroniser headers
		// ===============================
		function syncHeaderCheckboxes() {

			let total = $("#liste-extraction-bordereau-affichage tbody input[type='checkbox']").length;
			let checked = $("#liste-extraction-bordereau-affichage tbody input[type='checkbox']:checked").length;

			if (total > 0 && total === checked) {
				$("#checked-all").prop("checked", true);
				$("#checked-hidden-all").prop("checked", true);
			} else {
				$("#checked-all").prop("checked", false);
				$("#checked-hidden-all").prop("checked", false);
			}
		}


		// ======================================
		// SELECT ALL visible → coche tout
		// ======================================
		$("#checked-all").on("change", function () {

			let checked = this.checked;

			// tableau visible
			$("#liste-extraction-bordereau-affichage tbody input[type='checkbox']")
				.prop("checked", checked);

			// tableau caché
			$("#liste-extraction-bordereau-rdv tbody input[type='checkbox']")
				.prop("checked", checked);

			// header caché
			$("#checked-hidden-all").prop("checked", checked);
		});


		// ======================================
		// SELECT ALL caché → coche tout
		// ======================================
		$("#checked-hidden-all").on("change", function () {

			let checked = this.checked;

			// tableau caché
			$("#liste-extraction-bordereau-rdv tbody input[type='checkbox']")
				.prop("checked", checked);

			// tableau visible
			$("#liste-extraction-bordereau-affichage tbody input[type='checkbox']")
				.prop("checked", checked);

			// header visible
			$("#checked-all").prop("checked", checked);
		});


		// ==================================================
		// Ligne visible → synchronise ligne cachée
		// ==================================================
		$(document).on("change", "#liste-extraction-bordereau-affichage tbody input[type='checkbox']", function () {

			let index = $(this).closest("tr").index();
			let checked = this.checked;

			$("#liste-extraction-bordereau-rdv tbody tr")
				.eq(index)
				.find("input[type='checkbox']")
				.prop("checked", checked);

			syncHeaderCheckboxes();
		});


		// ==================================================
		// Ligne cachée → synchronise ligne visible
		// ==================================================
		$(document).on("change", "#liste-extraction-bordereau-rdv tbody input[type='checkbox']", function () {

			let index = $(this).closest("tr").index();
			let checked = this.checked;

			$("#liste-extraction-bordereau-affichage tbody tr")
				.eq(index)
				.find("input[type='checkbox']")
				.prop("checked", checked);

			syncHeaderCheckboxes();
		});



		// $("#exportButton").click(function () {

		// 	let nom_fichier = document.getElementById("nom_fichier").value;

		// 	let checked = document.querySelectorAll(
		// 		"#liste-extraction-bordereau-rdv tbody input[type='checkbox']:checked"
		// 	);

		// 	if (checked.length === 0) {
		// 		alert("Veuillez sélectionner au moins une ligne à exporter.");
		// 		return;
		// 	}

		// 	if (!confirm("Êtes-vous sûr de vouloir exporter les " + checked.length + " lignes selectionnées ?")) {
		// 		return;
		// 	}

		// 	var objetRDV = document.getElementById("villesRDV").value;
		// 	var affecteLe = document.getElementById("affecteLe").value;
		// 	var affecteAu = document.getElementById("affecteAu").value;
	
		// 	// var rdvLe = document.getElementById("rdvLe").value;
		// 	// var rdvAu = document.getElementById("rdvAu").value;
		// 	var ListeGest = document.getElementById("ListeGest").value;
		// 	//var etaperdv = document.getElementById("etaperdv").value;

		// 	// console.log(objetRDV, affecteLe, affecteAu, rdvLe, rdvAu, ListeGest);

		// 	var periode = "";
		// 	var agent = "";
		// 	var villes = "";
		// 	var etatRDV = "";
		// 	var lib_fichier = "";

		// 	if (affecteLe != "" && affecteAu != "") {
		// 		//formater en dd/mm/yyyy
		// 		var date1 = new Date(affecteLe);
		// 		var date2 = new Date(affecteAu);
		// 		periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear() + " au " + date2.getDate() + "/" + (date2.getMonth() + 1) + "/" + date2.getFullYear();
		// 		lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear() + " au " + date2.getDate() + "_" + (date2.getMonth() + 1) + "_" + date2.getFullYear();
		// 	} else if (affecteLe != "" && affecteAu == "") {
		// 		var date1 = new Date(affecteLe);
		// 		periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
		// 		lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
		// 	} else if (affecteLe == "" && affecteAu != "") {
		// 		var date1 = new Date(affecteAu);
		// 		periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
		// 		lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear();
		// 	}


		// 	if (objetRDV != "") {
		// 		const [idvillesRDV, villesRDV] = objetRDV.split(";");
		// 		villes = villesRDV;
		// 	}
		// 	if (ListeGest != "") {
		// 		const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = ListeGest.split("|");
		// 		agent = nomgestionnaire + " (" + idgestionnaire + ")";
		// 	}

		// 	afficherLoader();

		// 	$.ajax({
		// 		url: "../config/routes.php",
		// 		data: {
		// 			objetRDV: objetRDV,
		// 			affecteLe: affecteLe,
		// 			affecteAu: affecteAu,
		// 			ListeGest: ListeGest,
		// 			etat: "saveBordereauRDV"

		// 		},
		// 		dataType: "json",
		// 		method: "post",
		// 		success: function(response, status) {

		// 			cacherLoader();

		// 			if (response.error != false) {
		// 				if (response.action == "insert" || response.action == "update") {
							
		// 					let tableExport = document.createElement("table");
	
		// 					// ===== HEADER =====
		// 					let theadOriginal = document.querySelector("#liste-extraction-bordereau-rdv thead");
		// 					let theadClone = theadOriginal.cloneNode(true);
	
		// 					// 🔥 Supprimer la colonne checkbox du header
		// 					theadClone.querySelector("tr").removeChild(
		// 						theadClone.querySelector("tr").firstElementChild
		// 					);
	
		// 					tableExport.appendChild(theadClone);
	
		// 					// ===== BODY =====
		// 					let tbody = document.createElement("tbody");
	
		// 					checked.forEach(cb => {
	
		// 						let row = cb.closest("tr").cloneNode(true);
	
		// 						// 🔥 Supprimer la colonne checkbox du body
		// 						row.removeChild(row.firstElementChild);
	
		// 						tbody.appendChild(row);
		// 					});
	
		// 					tableExport.appendChild(tbody);
	
		// 					// ===== EXPORT =====
		// 					let wb = XLSX.utils.table_to_book(tableExport, { sheet: "Feuille1" });
	
		// 					let filename = "extraction-bordereau-rdv-" + nom_fichier + ".xlsx";
	
		// 					XLSX.writeFile(wb, filename);

		// 					if (response.action == "insert") {
								
		// 						afficherMessage("Exportation du bordereau du " + periode + " affectuée avec succès ! Reference Bordereau : " + response.reference, "success");
		
		// 						a_afficher = `<div class="alert alert-success" role="alert">
		// 										<h6> Exportation du bordereau du ` + periode + ` affectuée avec succès ! </h6>
		// 										<h3> Reference Bordereau : ` + response.reference + ` </h3>
		// 									</div>`
		
		// 						$("#a_afficher2").html(a_afficher)
		// 						$('#notification').modal("show")
		// 					}else if (response.action == "update") {
		// 						afficherMessage(response.message, "success");
		// 						$("#zoneResultats").hide();
		// 						// $("#tableAffichageExtraction").hide();
		// 						$("#zoneAffichage").hide();
		// 					}
	
		// 				}else if (response.action == "exist") {
		// 					afficherMessage(response.message, "warning");
		// 					$("#zoneResultats").hide();
		// 					// $("#tableAffichageExtraction").hide();
		// 					$("#zoneAffichage").hide();
		// 				}else {
		// 					$("#zoneResultats").hide();
		// 					// $("#tableAffichageExtraction").hide();
		// 					$("#zoneAffichage").hide();
		// 					afficherMessage("Desolé ! Une erreur est survenue lors de l'exportation du bordereau", "warning");
		// 				}

		// 				return
		// 			} else {
		// 				afficherMessage("Desolé ! Une erreur est survenue lors de l'exportation du bordereau", "warning");
		// 				$("#zoneResultats").hide();
		// 				// $("#tableAffichageExtraction").hide();
		// 				$("#zoneAffichage").hide();
		// 			}

		// 		},
		// 		error: function(response, status, etat) {
		// 			console.log(response, status, etat);
		// 		}
		// 	});
		// });

		$("#exportButton").click(function () {

			let nom_fichier = document.getElementById("nom_fichier").value;

			let checked = document.querySelectorAll(
				"#liste-extraction-bordereau-rdv tbody input[type='checkbox']:checked"
			);

			if (checked.length === 0) {
				alert("Veuillez sélectionner au moins une ligne à exporter.");
				return;
			}

			if (!confirm("Êtes-vous sûr de vouloir exporter les " + checked.length + " lignes selectionnées ?")) {
				return;
			}

			var objetRDV = document.getElementById("villesRDV").value;
			var affecteLe = document.getElementById("affecteLe").value;
			var affecteAu = document.getElementById("affecteAu").value;
			var ListeGest = document.getElementById("ListeGest").value;

			var periode = "";
			var lib_fichier = "";

			// ===== PERIODE =====
			if (affecteLe != "" && affecteAu != "") {
				var date1 = new Date(affecteLe);
				var date2 = new Date(affecteAu);

				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear() +
					" au " +
					date2.getDate() + "/" + (date2.getMonth() + 1) + "/" + date2.getFullYear();

				lib_fichier = date1.getDate() + "_" + (date1.getMonth() + 1) + "_" + date1.getFullYear() +
					"_au_" +
					date2.getDate() + "_" + (date2.getMonth() + 1) + "_" + date2.getFullYear();

			} else if (affecteLe != "") {
				var date1 = new Date(affecteLe);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
			} else if (affecteAu != "") {
				var date1 = new Date(affecteAu);
				periode = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
			}

			afficherLoader();

			$.ajax({
				url: "../config/routes.php",
				data: {
					objetRDV: objetRDV,
					affecteLe: affecteLe,
					affecteAu: affecteAu,
					ListeGest: ListeGest,
					etat: "saveBordereauRDV"
				},
				dataType: "json",
				method: "post",

				success: function (response) {

					cacherLoader();

					if (response.success === true) {
						if (response.action == "insert" || response.action == "update") {

							// ===== CREATION TABLE EXPORT =====

							let tableExport = document.createElement("table");

							// HEADER ORIGINAL
							let theadOriginal = document.querySelector("#liste-extraction-bordereau-rdv thead");
							let theadClone = theadOriginal.cloneNode(true);

							// ❌ Supprimer colonne checkbox
							theadClone.querySelector("tr").removeChild(
								theadClone.querySelector("tr").firstElementChild
							);

							tableExport.appendChild(theadClone);

							// BODY
							let tbody = document.createElement("tbody");

							checked.forEach(cb => {

								let row = cb.closest("tr").cloneNode(true);

								// ❌ Supprimer checkbox
								row.removeChild(row.firstElementChild);

								tbody.appendChild(row);
							});

							tableExport.appendChild(tbody);

							// ===== TABLE FINALE AVEC ENTETE =====

							let finalTable = document.createElement("table");

							// 🔥 Ligne Référence Bordereau
							let headerRow = document.createElement("tr");
							let headerCell = document.createElement("td");

							headerCell.colSpan = theadClone.querySelectorAll("th").length;
							headerCell.style.fontWeight = "bold";
							headerCell.style.fontSize = "16px";
							headerCell.style.textAlign = "center";
							headerCell.style.backgroundColor = "#033f1f";
							headerCell.style.color = "white";
							headerCell.style.padding = "10px";
							headerCell.style.border = "1px solid #033f1f";

							// let nom_fichier = response.reference + "_" + lib_fichier;

							headerCell.innerText =
								"Référence bordereau : " + response.reference +
								" | Période : " + periode;

							headerRow.appendChild(headerCell);
							finalTable.appendChild(headerRow);

							// Ligne vide
							let emptyRow = document.createElement("tr");
							emptyRow.appendChild(document.createElement("td"));
							finalTable.appendChild(emptyRow);

							// Ajouter tableau
							finalTable.appendChild(tableExport);

							// ===== EXPORT EXCEL =====

							let wb = XLSX.utils.table_to_book(finalTable, { sheet: "Feuille1" });

							let filename = "extraction-bordereau-rdv-" + nom_fichier + ".xlsx";

							XLSX.writeFile(wb, filename);

							afficherMessage(
								"Exportation réussie !" + response.message + ". Référence : " + response.reference,
								"success"
							);
							$("#zoneResultats").hide();
							$("#zoneAffichage").hide();
						}else if (response.action == "exist") {
							afficherMessage(response.message, "warning");
							$("#zoneResultats").hide();
							// $("#tableAffichageExtraction").hide();
							$("#zoneAffichage").hide();
						}else {
							$("#zoneResultats").hide();
							// $("#tableAffichageExtraction").hide();
							$("#zoneAffichage").hide();
							afficherMessage("Desolé ! Une erreur est survenue lors de l'exportation du bordereau", "warning");
						}


					} else {
						afficherMessage("Erreur lors de l'exportation", "warning");
						$("#zoneResultats").hide();
						$("#zoneAffichage").hide();
					}
				},

				error: function (response) {
					console.log(response);
				}
			});

		});


		$("#closeNotif").click(function() {
			$('#notification').modal('hide')
			window.history.back();
		})




		function remplirTable(data, periode, agent, villes, etatRDV, lib_fichier) {

			let html = "";
			let html_affiche = "";
			let retourApi = "";
			let total = data.length;

			data.forEach((e, i) => {

				const etats = {
					1: ["En attente", "badge badge-secondary"],
					2: ["Transmis", "badge badge-success"],
					3: ["Traité", "badge badge-warning"]
				};

				const [lib, col] = etats[e.etat] || ["Non défini", "dark"];

				var dateeff = new Date(e.daterdveff);
				daterdveff = dateeff.getDate() + "/" + (dateeff.getMonth() + 1) + "/" + dateeff.getFullYear();
				daterdveff = convertirEnDateFR(e.daterdv);

				var daterdv = new Date(e.daterdv);
				dateRDV = daterdv.getDate() + "/" + (daterdv.getMonth() + 1) + "/" + daterdv.getFullYear();
				dateRDV = convertirEnDateFR(e.daterdv);

				html += `<tr id="ligne-${i}" style="color: #033f1f !important;" >
							<td><input type="checkbox" name="idrdv[]" id="check-${i}" value="${e.idrdv}"></td>
							<td>${i + 1}</td>
							<td id="idrdv-${i}">${e.idrdv}</td>
							<td id="idcontrat-${i}">${e.police}</td>
                            <td class="text-wrap">${e.nomclient}
								<p class="mb-0 text-dark" style="font-size:0.7em;">
													Date naissance :
													<strong>${e.datenaissance}</strong>
								</p>
								<p class="mb-0 text-dark" style="font-size:0.7em;">
													Téléphone :
													<strong>${e.tel}</strong>
								</p>
							</td>
							<td class="text-wrap">${e.motifrdv}</td>
							<td class="text-wrap">${dateRDV}</td>
							<td class="text-wrap">${e.villes}</td>
							<td class="text-wrap">${e.nomgestionnaire}
								<p class="mb-0 text-dark" style="font-size:0.7em;">
													ref gestionnaire :
													<strong>${e.gestionnaire}</strong>
								</p>
								<p class="mb-0 text-dark" style="font-size:0.7em;">
													code gestionnaire :
													<strong>${e.codeagentgestionnaire}</strong>
								</p>
							</td>
							<td class="${col}">${lib}</td>
                        </tr>`;
			});

			$("#body-extraction-affichage").html(html);
			// ✅ afficher résultats
			$("#zoneAffichage").fadeIn();
			$("#tableAffichageExtraction").fadeIn(300);
			initDataTable();
			afficherMessage("Recherche terminée avec succès", "success");
		}


		function remplirTableExtraction(data, periode, agent, villes, etatRDV, lib_fichier) {

			let html = "";
			let html_affiche = "";
			let retourApi = "";
			let total = data.length;

			let produit = "";
			let code_produit = "";
			let prime = "";
			let capital = "";
			let dateEffet = "";
			let dateEcheance = "";
			let assure = "";

			data.forEach((e, i) => {

				const etats = {
					1: ["En attente", "badge badge-secondary"],
					2: ["Transmis", "badge badge-success"],
					3: ["Traité", "badge badge-warning"]
				};

				const [lib, col] = etats[e.etat] || ["Non défini", "dark"];

				var dateeff = new Date(e.daterdveff);
				// console.log('new dateeff :',dateeff);

				daterdveff = dateeff.getDate() + "/" + (dateeff.getMonth() + 1) + "/" + dateeff.getFullYear();
				daterdveff = convertirEnDateFR(e.daterdveff);
				// console.log('daterdveff :',e.daterdveff, '--------> dateEff :', daterdveff);

				var daterdv = new Date(e.daterdv);
				// console.log('new daterdv :',daterdv);
				dateRDV = daterdv.getDate() + "/" + (daterdv.getMonth() + 1) + "/" + daterdv.getFullYear();
				dateRDV = convertirEnDateFR(e.daterdv);

				// console.log('daterdv :',e.daterdv, '--------> dateRDV :', dateRDV);

				html += `
						<tr id="ligne-${i}" style="color: #033f1f !important;" >
							<td class="d-none"><input type="checkbox" class="hidden-check" value="${e.idrdv}"></td>
							<td>${i + 1}</td>
							<td id="idrdv-${i}">${e.dateajou}</td>
							<td id="idrdv-${i}">${e.codedmd}</td>
							<td id="idrdv-${i}">${e.idrdv}</td>
							<td id="idcontrat-${i}">${e.police}</td>
                            <td>${e.nomclient}</td>
							<td>${e.datenaissance}</td>
							<td>${e.tel}</td>
							<td>${e.motifrdv}</td>
							<td>${dateRDV}</td>
							<td>${daterdveff}</td>
							<td>${e.villes}</td>
							<td>${e.nomgestionnaire}</td>
							<td>${e.gestionnaire}</td>
							<td>${e.codeagentgestionnaire}</td>
							<td>${lib}</td>					
                        </tr> `;
			});
			

			$("#body-extraction-bordereau-rdv").html(html);
			$("#zoneAffichageExtraction").fadeIn();
			//$("#tableAffichageExtraction").fadeIn(300);
		}


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

		function afficherLoader() {
			$("#loaderRDV").fadeIn(200);
			$("#zoneResultats").hide();

			$("#body-extraction-bordereau-rdv").empty(); // 🧹 vider table
			$("#body-extraction-affichage").empty(); // 🧹 vider table
			$("#compteurResultats").hide();
			$("#formFiltreRDV button[type='submit']").prop("disabled", true);
		}

		function cacherLoader() {
			$("#loaderRDV").fadeOut(200);
			$("#formFiltreRDV button[type='submit']").prop("disabled", false);
		}

		// function convertirEnDateFR(dateStr) {

		// 	if (!dateStr) return "";

		// 	// ISO : yyyy-mm-dd
		// 	if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
		// 		const [yyyy, mm, dd] = dateStr.split("-");
		// 		return `${dd}/${mm}/${yyyy}`;
		// 	}

		// 	// FR : dd/mm/yyyy (déjà correct)
		// 	if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
		// 		return dateStr;
		// 	}

		// 	// Autre format non géré
		// 	console.warn("Format de date non reconnu :", dateStr);
		// 	return "";
		// }

		function convertirEnDateFR(dateString) {

			if (!dateString) return "";

			// Si déjà un objet Date
			if (dateString instanceof Date && !isNaN(dateString)) {
				return dateString.toLocaleDateString("fr-FR");
			}

			// Format MySQL : YYYY-MM-DD HH:mm:ss
			if (/^\d{4}-\d{2}-\d{2}/.test(dateString)) {
				let d = new Date(dateString.replace(" ", "T"));
				if (!isNaN(d)) return d.toLocaleDateString("fr-FR");
			}

			// Format ISO : YYYY-MM-DD
			if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
				let d = new Date(dateString);
				if (!isNaN(d)) return d.toLocaleDateString("fr-FR");
			}

			// Format FR : DD-MM-YYYY
			if (/^\d{2}-\d{2}-\d{4}$/.test(dateString)) {
				let parts = dateString.split("-");
				return parts[0] + "/" + parts[1] + "/" + parts[2];
			}

			console.warn("Format de date non reconnu :", dateString);
			return "";
		}

		function getAPIVerificationProfil(keys) {
			let resultat;
			$.ajax({
				url: "https://api.laloyalevie.com/oldweb/encaissement-bis",
				data: {
					idContrat: keys
				},
				dataType: "json",
				method: "post",
				async: false,
				success: function(response, status) {
					//console.log(response)
					resultat = response

					if (resultat.error != false) {
						//console.log(response)
					}
				},
				error: function(response, status, etat) {
					resultat = '-1';
				}
			})
			return resultat
		}

		function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
			number = parseFloat(number).toFixed(decimals);

			let parts = number.split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

			return parts.join(dec_point);
		}

		function escapeHtml(text) {
			return $('<div>').text(text ?? '').html();
		}

		function afficherMessage(message, type = "info") {
			$(".alert-zone").html(`
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    `);
		}

		/* =========================
           INITIALISATION DATATABLE
        ========================= */
		function initDataTable() {

			if ($.fn.DataTable.isDataTable('#liste-extraction-bordereau-affichage')) {
				$('#liste-extraction-bordereau-affichage').DataTable().destroy();
			}

			//en français
			tableAffichage = $('#liste-extraction-bordereau-affichage').DataTable({
				responsive: true,
				pageLength: 25,
				ordering: true,
				searching: true,
				language: {
					url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
				},
				columnDefs: [{
					targets: "datatable-nosort",
					orderable: false
				}]
			});
		}
	</script>
</body>

</html>