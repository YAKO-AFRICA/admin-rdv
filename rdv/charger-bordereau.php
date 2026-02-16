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
$error = "";
$message = "";
$preview = "";
$periode = "";
$VilleRDV = "";
$nomGest = "";
$effectue = 0;

$sqlSelect = " SELECT * FROM  tbl_bordereau_rdv WHERE etat = '0'  ORDER BY created_at DESC ";
$liste_bordereau = $fonction->_getSelectDatabases($sqlSelect);




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
								<h4>CHARGER BORDEREAU RDV</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">CHARGER BORDEREAU RDV</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<div class="mb-3">
					<button class="btn btn-warning" onclick="retour()">
						<i class="fa fa-arrow-left"></i> Retour
					</button>
				</div>

				<!-- <?php print_r($liste_bordereau); ?> -->

				<!-- Titre RDV -->
				<div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
					<div class="card-body text-center ">
						<h3 style="color:white">
							<span class="text-warning">
								Détails du fichier à charger
							</span>
						</h3>
					</div>
				</div>


				<div class="card-box mb-30">

					<div class="pd-20">
						<div class="card-body radius-12 w-100 pb-20">

							<!-- Informations sur le format attendu -->
							<div class="row pd-20">
								<div class="card-body radius-12 w-100 p-4" style="color:#033f1f;">
									<div class="row">
										<div class="col-md-6">
											<p>
												<span class="font-weight-bold">Format conventionnel :</span>
												<span style="font-size:16px; font-weight:bold; color:#F9B233">.xlxs</span>
												<span style="font-size:16px; font-weight:bold; color:#F9B233">;</span>
											</p>
											<p>
												<span class="font-weight-bold">Modèle fichier :</span>
												<a href="modele-bordereau-rdv.xlsx" target="_blank">
													<span style="font-size:16px; font-weight:bold; color:#F9B233">Cliquez ici</span>
												</a>
											</p>
										</div>

										<div class="col-md-6">
											<p>
												<span class="font-weight-bold">Taille maximale du fichier :</span>
												<span style="font-size:16px; font-weight:bold; color:#F9B233">5MB</span>
											</p>
											<p>
												<span class="font-weight-bold">Nombre de colonnes attendu :</span>
												<span style="font-size:16px; font-weight:bold; color:#F9B233">21</span>
											</p>
										</div>
									</div>
								</div>
							</div>

							<!-- Formulaire de chargement -->
							<div class="card-body mb-4 p-3 row" style="border:2px dashed #F9B233; background-color:#fefbf4;">
								<div class="col-md-6">
									<label for="bordereauReference" class="font-weight-bold mb-2">Sélectionnez un la Référence du Bordereau :</label>
									<select name="bordereauReference" id="bordereauReference" class="form-control height-auto" required>
										<option value="" selected disabled>Choisir la Référence</option>
										<?php foreach ($liste_bordereau as $bordereau): ?>
											<option value="<?= $bordereau->reference; ?>">
												<?= $bordereau->reference .' ( du ' . date('d/m/Y', strtotime($bordereau->periode_1)). ' au ' . date('d/m/Y', strtotime($bordereau->periode_2) ) . ' ) '; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-6">
									<label for="upload" class="font-weight-bold mb-2">Sélectionnez un fichier :</label>
									<input type="file" name="upload" id="upload" accept=".xlsx, .csv" class="form-control-file form-control height-auto" required>
								</div>
								
							</div>

							<!-- Aperçu et bouton d'import -->
							<div class="card-body w-100" style="font-size:12px">
								<!-- Bouton d'import -->
								<button id="importBtn" style="display: none;" class="btn btn-success mb-3">
									<i class="fa fa-cloud-upload" aria-hidden="true"></i> Charger le fichier
								</button>

								<div id="loaderRDV" style="display:none" class="mt-3 text-center">
									<div class="spinner-border text-warning"></div>
									<p>Chargement en cours...</p>
								</div>

								<div class="alert-zone mt-3"></div>

								<!-- Tableau de prévisualisation -->
								<div class="table-responsive" style="max-height:600px; overflow-y:auto;" id="previewTableContainer">
									<table class="table table-bordered table-striped nowrap" id="previewTable" style="width:100%; font-size:10px;">
										<!-- Le contenu sera injecté dynamiquement -->
									</table>
								</div>
								<div class="resultat-operation-zone mt-3"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "../include/footer.php";    ?>
		</div>

	</div>


	<div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-body text-center">
					<div class="card-body" id="iframeAfficheDocument">

					</div>
				</div>
				<div class="modal-footer">
					<div>

						<button class="btn btn-success" onclick="window.open('documents/template-document.csv', '_blank')">
							TÉLÉCHARGER DOCUMENT
						</button>
						<button type="button" id="closeEchec" class="btn btn-secondary"
							data-dismiss="modal">FERMER</button>
					</div>
				</div>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


	<script>
		function retour() {
			window.history.back();
		}

		let excelData = []; // Pour stocker les données Excel



		document.getElementById('upload').addEventListener('change', function(e) {
			const file = e.target.files[0];

			const reader = new FileReader();
			reader.onload = function(e) {
				const data = new Uint8Array(e.target.result);
				const workbook = XLSX.read(data, {
					type: 'array'
				});

				const firstSheetName = workbook.SheetNames[0];
				const worksheet = workbook.Sheets[firstSheetName];

				const json = XLSX.utils.sheet_to_json(worksheet, {
					header: 1
				});



				// ✅ Colonnes de type date (les entêtes doivent correspondre exactement)
				const dateColumns = ['Date d\'effet', 'Date d\'échéance'];

				const headers = json[0];
				for (let i = 1; i < json.length; i++) {
					for (let j = 0; j < json[i].length; j++) {
						const headerName = headers[j]?.trim();
						const cellValue = json[i][j];

						// 🔁 Conversion selon le type de colonne
						if (dateColumns.includes(headerName)) {
							json[i][j] = excelDateToJSDate(cellValue);
						} else if (cellValue === '' || typeof cellValue === 'undefined') {
							json[i][j] = null;
						}
					}
				}

				excelData = json;
				console.log(excelData);

				if (json.length === 0) return;

				// Construction de THEAD
				let thead = '<thead class="text-wrap" style="background-color: #033f1f; color: #fff;"><tr>';
				headers.forEach(cell => {
					thead += `<th class="text-wrap">${cell ?? ''}</th>`;
				});
				thead += '</tr></thead>';

				// Construction de TBODY
				let tbody = '<tbody>';
				for (let i = 1; i < json.length; i++) {
					tbody += '<tr>';
					json[i].forEach(cell => {
						const displayValue = (cell === null) ? '' : cell;
						tbody += `<td class="text-wrap">${displayValue}</td>`;
					});
					tbody += '</tr>';
				}
				tbody += '</tbody>';

				// Injecter dans le tableau HTML
				document.getElementById('previewTable').innerHTML = thead + tbody;
				document.getElementById('importBtn').style.display = 'inline-block';
			};

			reader.readAsArrayBuffer(file);
		});




		document.getElementById('importBtn').addEventListener('click', function() {
			// if (excelData.length < 1) {
			// 	alert("Aucune donnée à importer !");
			// 	return;
			// }
			if (excelData.length < 2) {
				alert("Le fichier ne contient pas de données.");
				return;
			}
			const bordereauReference = document.getElementById("bordereauReference").value;
			if(!bordereauReference){
				alert("Veuillez sélectionner la référence du bordereau à importer SVP !!");
				return false;
			}

			// Transformer en objets (clé: en-tête)
			const headers = excelData[0];
			const rows = [];


			for (let i = 1; i < excelData.length; i++) {
				const obj = {};
				for (let j = 0; j < headers.length; j++) {
					obj[headers[j].trim()] = excelData[i][j] ?? null;
				}
				rows.push(obj);
			}

			const dataATraiter = JSON.stringify(rows);
			console.log(dataATraiter);


			afficherLoader();
			$.ajax({
				url: "../config/routes.php",
				data: {
					params: dataATraiter,
					bordereauReference : bordereauReference,
					etat: "importBordereau"
				},
				dataType: "json",
				method: "post",
				success: function(response, status) {

					cacherLoader();

					if (response.error != false) {

						afficherMessage("Recherche terminée avec succès", "success");
						console.log(response);

						$('#previewTableContainer').hide();

						if (response.success == true) {

							let reference = response.reference
							let inserted = response.inserted
							let total = response.total

							$(".resultat-operation-zone").html(`
								<div class="alert alert-success alert-dismissible fade show">
									<h4> Succès ! le bordereau (` + reference + `) a été importé avec ` + inserted + ` lignes sur ` + total + ` lignes ! </h4>
									<br>
									<a href="detail-bordereau.php?reference=` + reference + `" class="btn btn-success">Visualiser le bordereau chargé </a>
									
								</div>
								`);

							// a_afficher = `<div class="alert alert-success" role="alert">
							// <h2> Bravo ! le document ` + reference + ` a ete importé avec ` + inserted + ` lignes sur ` + total + ` lignes ! </h2> </div>`
							// $("#a_afficher2").html(a_afficher)
							// $('#notification').modal("show")


						} else {
							let message = response.error

							a_afficher = `<div class="alert alert-danger" role="alert">
								<h2> Desolé ! une erreur est survenue sur le chargement du document  ! </h2> </div>`
							$("#a_afficher2").html(a_afficher)
							$('#notification').modal("show")
						}
					} else {
						a_afficher = `<div class="alert alert-warning" role="alert">
								<h2> Desolé ! desole le document ` + response.reference + ` n'a pas ete importe ! </h2> </div>`

						$("#a_afficher2").html(a_afficher)
						$('#notification').modal("show")
					}

				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});

		});


		$("#closeNotif").click(function() {
			$('#notification').modal('hide')
			window.history.back();
		})



		$(".dw-eye").click(function(evt) {
			var path_document = "exemple_RDV.xlsx";

			alert(path_document);
			if (path_document !== "" && path_document.length > 0) {
				// Charger le contenu du fichier CSV
				fetch(path_document)
					.then(response => response.text())
					.then(csvText => {
						// Afficher le texte brut dans le modal
						let html = `<pre style="text-align:left;">${csvText}</pre>`;
						$("#lienAfficheDocument").val(path_document);
						$("#iframeAfficheDocument").html(html);
						$('#modaleAfficheDocument').modal("show");
					})
					.catch(error => {
						alert("Erreur lors du chargement du fichier : " + error);
					});
			} else {
				alert("Aucun document trouvé");
			}
		});



		// ✅ Fonction pour convertir une date Excel en 'YYYY-MM-DD'
		function excelDateToJSDate(serial) {
			if (!serial || isNaN(serial)) return null;
			const utc_days = Math.floor(serial - 25569);
			const utc_value = utc_days * 86400;
			const date_info = new Date(utc_value * 1000);
			return date_info.toISOString().split('T')[0];
		}

		function afficherLoader() {
			$("#loaderRDV").fadeIn(200);
			$("#importBtn").prop("disabled", true);
		}

		function cacherLoader() {
			$("#loaderRDV").fadeOut(200);
			$("#importBtn").prop("disabled", false);
		}

		function afficherMessage(message, type = "info") {
			$(".alert-zone").html(`
			<div class="alert alert-${type} alert-dismissible fade show">
				${message}
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
    		`);
		}
	</script>
</body>


</html>