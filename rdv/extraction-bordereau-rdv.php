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
								<h6>Date de RDV <span class="text-danger">*</span></h6>
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
						<!-- <div class="pd-20">
							<button type="button" class="btn btn-info  bx bx-cloud-upload px-5" id="exportButton"
								name="exportButton">Exporter vers excel</button>
						</div> -->

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
					<table class="data-table table stripe hover nowrap" id="liste-extraction-bordereau-rdv" style="width:100%; font-size:10px;">
						<thead>
							<tr class="text-wrap">
								<th>N°</th>
								<th>Date prise RDV</th>
								<th>code RDV</th>
								<th>Id RDV</th>
								<th>Id contrat</th>
								<!-- <td>Code produit</td>
										<td>Produit</td>
										<td>Date Effet</td>
										<td>Date Echeance</td> -->
								<th>Demandeur</th>
								<th>Date naissance</th>
								<th>Telephone</th>
								<th>Motif RDV</th>
								<!-- <td>Prime</td>
										<td>Capital</td> -->
								<th>Date RDV Souhaité</th>
								<th>Date RDV Effectif</th>
								<th>Ville RDV</th>
								<th>Gestionnaire</th>
								<th>ref Gestionnaire</th>
								<th>Code agent</th>
								<th class="datatable-nosort">Etat</th>
							</tr>
						</thead>
						<tbody id="body-extraction-bordereau-rdv" style="width: 100%;">
						</tbody>
					</table>
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
			var rdvLe = document.getElementById("rdvLe").value;
			var rdvAu = document.getElementById("rdvAu").value;
			var ListeGest = document.getElementById("ListeGest").value;
			//var etaperdv = document.getElementById("etaperdv").value;

			var periode = "";
			var agent = "";
			var villes = "";
			var etatRDV = "";
			var lib_fichier = "";

			if (rdvLe == "") {
				alert("Veuillez renseigner la periode de recherche dans le formulaire ci-dessous ");
				document.getElementById("rdvLe").focus();
				return false;
			}
			// if (rdvLe == "" && rdvAu == "" && objetRDV == "" && ListeGest == "") {
			// 	alert("Veuillez renseigner la periode de recherche dans le formulaire ci-dessous ");
			// 	document.getElementById("rdvLe").focus();
			// 	return false;
			// }



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

			// if (etaperdv != "") {
			// 	//$code . ";" . $libelle
			// 	const [code, libelle] = etaperdv.split(";");
			// 	etatRDV = libelle;
			// }

			afficherLoader();

			$.ajax({
				url: "../config/routes.php",
				data: {
					objetRDV: objetRDV,
					rdvLe: rdvLe,
					rdvAu: rdvAu,
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


		$("#exportButton").click(function() {

			var nom_fichier = document.getElementById("nom_fichier").value;

			//alert("nom_fichier.value : " + nom_fichier);
			var table = document.getElementById("liste-extraction-bordereau-rdv");
			// Convertir le tableau HTML en un "workbook" Excel
			var wb = XLSX.utils.table_to_book(table, {
				sheet: "Feuille1"
			});
			// Générer le fichier Excel et télécharger
			//formater avec la date du jour
			var filename = "extraction-bordereau-rdv-" + nom_fichier + ".xlsx";
			XLSX.writeFile(wb, filename);
		});



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

				// retourApi = getAPIVerificationProfil(e.police);

				// if (retourApi) {
				// 	if (retourApi.error === true) {
				// 		//alert("Contrat non trouvé !!");
				// 	} else {


				// 		let details = retourApi["details"]
				// 		let contactsPersonne = retourApi["contactsPersonne"]

				// 		let data = details[0];

				// 		produit = data.produit;
				// 		code_produit = data.codeProduit;
				// 		prime = number_format(data.TotalPrime, 2, ',', ' ');
				// 		capital = number_format(data.CapitalSouscrit, 2, ',', ' ');
				// 		dateEffet = convertirEnDateFR(data.DateEffetReel);
				// 		dateEcheance = convertirEnDateFR(data.FinAdhesion);
				// 	}
				// }

				const [lib, col] = etats[e.etat] || ["Non défini", "dark"];

				var dateeff = new Date(e.daterdveff);
				daterdveff = dateeff.getDate() + "/" + (dateeff.getMonth() + 1) + "/" + dateeff.getFullYear();
				daterdveff = convertirEnDateFR(e.daterdv);

				var daterdv = new Date(e.daterdv);
				dateRDV = daterdv.getDate() + "/" + (daterdv.getMonth() + 1) + "/" + daterdv.getFullYear();
				dateRDV = convertirEnDateFR(e.daterdv);

				html += `
						<tr id="ligne-${i}" style="color: #033f1f !important;" >
							<td>${i + 1}</td>
							<td id="idrdv-${i}">${e.dateajou}</td>
							<td id="idrdv-${i}">${e.codedmd}</td>
							<td id="idrdv-${i}">${e.idrdv}</td>
							<td id="idcontrat-${i}">${e.police}</td>
							<!--<td>${code_produit}</td>
							<td>${produit}</td>
							<td>${dateEffet}</td>
							<td>${dateEcheance}</td>-->
                            <td class="text-wrap">${e.nomclient}</td>
							<td class="text-wrap">${e.datenaissance}</td>
							<td class="text-wrap">${e.tel}</td>
							<td class="text-wrap">${e.motifrdv}</td>
							<!--<td>${prime}</td>
							<td>${capital}</td>-->
							<td class="text-wrap">${dateRDV}</td>
							<td class="text-wrap">${daterdveff}</td>
							<td class="text-wrap">${e.villes}</td>
							<td class="text-wrap">${e.nomgestionnaire}</td>
							<td class="text-wrap">${e.gestionnaire}</td>
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

		function convertirEnDateFR(dateStr) {

			if (!dateStr) return "";

			// ISO : yyyy-mm-dd
			if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
				const [yyyy, mm, dd] = dateStr.split("-");
				return `${dd}/${mm}/${yyyy}`;
			}

			// FR : dd/mm/yyyy (déjà correct)
			if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
				return dateStr;
			}

			// Autre format non géré
			console.warn("Format de date non reconnu :", dateStr);
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