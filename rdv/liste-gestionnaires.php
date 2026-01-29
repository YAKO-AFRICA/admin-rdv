<?php
session_start();


if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("../autoload.php");

if (isset($_REQUEST['filtreliste'])) {
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];
} else {
	$filtre = '';
}



switch ($_SESSION['typeCompte']) {
	case 'rdv':
		$plus = " AND typeCompte IN ('gestionnaire', 'rdv')";
		break;
	case 'prestation':
		$plus = " AND typeCompte IN ('prestation')";
		break;
	default:
		$plus = '';
		break;
}

$sqlSelect = "
   SELECT 
    users.*, 
    COUNT(tblrdv.idrdv) AS nb_rdv,
    TRIM(tblvillebureau.libelleVilleBureau) AS ville_nom
FROM 
    users
LEFT JOIN 
    tblrdv ON tblrdv.gestionnaire = users.id
LEFT JOIN 
    tblvillebureau ON users.ville = tblvillebureau.idVilleBureau
WHERE 
    users.etat = '1' $plus
GROUP BY 
    users.id, tblvillebureau.libelleVilleBureau
ORDER BY 
    nb_rdv DESC;
";

$liste_rdvs = $fonction->_getSelectDatabases($sqlSelect);
if ($liste_rdvs != null) $effectue = count($liste_rdvs);
else $effectue = 0;

//print_r($liste_rdvs);exit;

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
								<h4>Liste des Utilisateurs</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Liste des Utilisateurs</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<div class="card-box mb-30">
					<div class="pd-20">
						<button class="btn btn-secondary p-2 m-2" name="addRDV" id="addRDV" style="background: #033f1f!important;float:right"><i class="icon-copy fa fa-user-plus" aria-hidden="true"> AJOUTER UTILISATEUR</i></button>
					</div>
					<div class="pd-20">
						<h4 class="text-center" style="color:#033f1f; "> Liste des Utilisateurs (<span style="color:#F9B233;"><?= $effectue ?></span>) </h4>
					</div>

					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Id</th>
									<th hidden>nom</th>
									<th hidden>prenom</th>
									<th hidden>email</th>
									<th hidden>telephone</th>
									<th hidden>typeCompte</th>
									<th hidden>etat</th>
									<th hidden>ville</th>
									<th>Nom & prénom(s)</th>
									<th>Contact</th>
									<th>Code agent</th>
									<th>lieu Reception</th>
									<th>type compte</th>
									<th>profil</th>

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

										if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_rdv[$etat];

								?>
										<tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $rdv->id; ?></td>
											<td id="nom-<?= $i ?>" hidden><?php echo $rdv->nom; ?></td>
											<td id="prenom-<?= $i ?>" hidden><?php echo $rdv->prenom; ?></td>
											<td id="email-<?= $i ?>" hidden><?php echo $rdv->email; ?></td>
											<td id="telephone-<?= $i ?>" hidden><?php echo $rdv->telephone; ?></td>
											<td id="typeCompte-<?= $i ?>" hidden><?php echo $rdv->typeCompte; ?></td>
											<td id="etat-<?= $i ?>" hidden><?php echo $rdv->etat; ?></td>
											<td id="villes-<?= $i ?>" hidden><?php echo $rdv->ville; ?></td>
											<td><?php echo $rdv->nom . " " . $rdv->prenom; ?></td>
											<td class="text-wrap"><?= $rdv->email ? $rdv->email : $rdv->login; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span
														style="font-weight:bold;"><?php echo $rdv->telephone; ?></span>
												</p>

											</td>

											<td class="text-wrap" id="codeagent-<?= $i ?>"><?php echo $rdv->codeagent; ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#F9B233!important;"><?php echo $rdv->ville_nom; ?></td>
											<td class="text-wrap"><?= $rdv->typeCompte; ?>
												<?php if ($rdv->typeCompte == "gestionnaire") {
													echo "<p class=\"mb-0 text-dark\" style=\"font-size: 0.7em; color:#F9B233;\">Compteur rdv : <span style=\"font-weight:bold;\">" . $rdv->nb_rdv . "</span></p>";
												} ?>
											</td>
											<td id="profil-<?= $i ?>"><?php echo $rdv->profil; ?></td>
											<td>
												<?php if (isset($rdv->etat) && $rdv->etat !== null && $rdv->etat == "1") {
													echo "<span class=\"badge badge-success\">Actif</span>";
												} else {
													echo "<span class=\"badge badge-danger\">Inactif</span>";
												}
												?>
											</td>
											<td class="table-plus text-wrap">

												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>" style="background-color:#F9B233;color:white"><i class="fa fa-eye"></i> Détail</button>
												<?php if ($rdv->etat == "1"): ?>
													<button class="btn btn-primary btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:blue; color:white"><i class="fa fa-edit"></i> modifier</button>
												<?php endif; ?>

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


	<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">Voulez vous supprimer la demande n° : <span id="a_afficher3" style="color: #F9B233;"> </span>?</h4>
					<input type="text" hidden class="form-control" name="idobjet" id="idobjet">

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="validerSuprime" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
							OUI
						</div>
						<div class="col-6">
							<button type="button" id="annulerSuprime" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="AjouterRDV" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 style="color: #033f1f !important;"><span id="titreModale" style="font-size:24px; font-weight:bold; color:#F9B233;"></span> </h5>
				</div>
				<div class="modal-body">
					<div class="col-12 col-lg-12 col-xl-12 d-flex">
						<div class="card">
							<!-- <form id="formOperateur" name="formOperateur" method="post"> -->
							<div class="card-body radius-12 w-100">
								<div class="row" id="formOperateur">

									<div class="form-group col-sm-12 col-md-12">
										<h4 style="color:#033f1f; font-size:16px; font-weight:bold;"> Veuillez renseigner les informations de l'utilisateur a ajouter svp !!</h4>
									</div>
									<input type="text" class="form-control" name="agent_id" id="agent_id" hidden>
									<input type="text" class="form-control" name="action" id="action" hidden>
									<hr>
									<div class="form-group col-sm-12 col-md-4">
										<label for="nomRdv" style="color: #000000;">Nom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="nom" name="nom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le nom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-8">
										<label for="nomRdv" style="color: #000000;">Prenom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="prenom" name="prenom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le prenom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">Telephone <bold style="color: #F9B233;"> *</bold></label>
										<input type="number" id="telephone" name="telephone" data-rule="required" required placeholder="Entrez le telephone" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">email <bold style="color: #F9B233;"> *</bold></label>
										<input type="email" id="email" name="email" data-rule="required" required placeholder="Entrez le email" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">Type de compte <bold style="color: #F9B233;"> *</bold></label>
										<select id="typeCompte" name="typeCompte" class="form-control" required>
											<option value="" selected disabled>Veuillez selectionner</option>
											<option value="rdv">admin RDV</option>
											<option value="prestation">gestionnaire Prestation</option>
											<option value="sinistre">gestionnaire Sinistre</option>
											<option value="gestionnaire">gestionnaire RDV</option>
											<option value="compte-ynov">compte-ynov</option>
											<option value="interim">compte interim</option>
										</select>
									</div>

									<div class="form-group col-sm-12 col-md-6" id="divProfil">

									</div>
									<div class="form-group col-sm-12 col-md-12" id="divCible"></div>
									<div class="form-group col-sm-12 col-md-12" id="ListeVilles"></div>

									<div class="form-group col-sm-12 d-flex align-items-center justify-content-end col-md-12" id="divEtat">

									</div>


								</div>
								<div class="modal-footer" id="footer">
									<button type="submit" name="traitAll" id="traitAll" class="btn btn-warning" onclick="getTraitementAjoutUtilisateur()"><span id="titreAction" style="color:white"></span></button>
									<button type="button" id="closeModaleUser" name="closeModaleUser" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
								</div>
							</div>
							<!-- </form> -->
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
					<button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success" style="background: #033f1f !important;">OK</button>
					<button type="button" id="closeModaleNotification" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
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




			function updateFormFields() {
				const typeCompte = $("#typeCompte").val();
				const profil = $("#profil").val();
				const idville = $("#villeRDV").val();
				//alert(idville);


				$("#ListeVilles").html('');
				// -- PROFIL --
				let profilSelect = `
					<label for="profil" style="color: #000000;">
						profil <strong style="color: #F9B233;">*</strong>
					</label>
					<select id="profil" name="profil" class="form-control" required>
						<option value="" disabled ${!profil ? "selected" : ""}>Veuillez sélectionner</option>
						<option value="agent" ${profil === "agent" ? "selected" : ""}>agent</option>
						<option value="supervisseur" ${profil === "supervisseur" ? "selected" : ""}>superviseur</option>
						<option value="admin" ${profil === "admin" ? "selected" : ""}>administrateur</option>
						<option value="nsil" ${profil === "nsil" ? "selected" : ""}>gestionnaire NSIL</option>
					</select>
				`;
				$("#divProfil").html(profilSelect);

				// -- CIBLE / CODE AGENT --
				let cibleHtml = "";

				if ((typeCompte === "prestation" && profil === "agent")) {
					cibleHtml = `
						<label for="ciblePrestation" style="color: #000000;">
						cible <strong style="color: #F9B233;">*</strong>
						</label>
						<select id="ciblePrestation" name="ciblePrestation" class="form-control" required>
						<option value="" selected disabled>Veuillez sélectionner</option>
						<option value="administratif">administratif</option>
						<option value="technique">technique</option>
						</select>
					`;
				} else if ((typeCompte === "rdv" || typeCompte === "gestionnaire") && profil === "agent") {
					cibleHtml = `
						<label for="codeagent" style="color: #000000;">
						code agent <strong style="color: #F9B233;">*</strong>
						</label>
						<input type="text" id="codeagent" name="codeagent" required placeholder="Entrez le code agent" class="form-control">
					`;
					//console.log(" 2 : idville", idville);
					getListeVillesRDV(null);
				} else if (typeCompte === "interim" && profil === "agent") {
					cibleHtml = `
						<label for="codeagent" style="color: #000000;">
						code agent <strong style="color: #F9B233;">*</strong>
						</label>
						<input type="text" id="codeagent" name="codeagent" required placeholder="Entrez le code agent" class="form-control">
					`;
					//console.log(" 2 : idville", idville);
					getListeVillesRDV(null);
				} else if ((typeCompte === "sinistre" && profil === "nsil")) {
					cibleHtml = `<div class="row">
                    <div class="form-group col-md-6 col-sm-12">
                        <label class="col-form-label">Provenance  (<span style="color:red;">*</span>) :</label>
                        <select name="provenance" id="provenance" class="form-control" required>
                            <option value="" disabled selected>...</option>
                            <option value="LLV">Reseaux Particulier</option>
                            <option value="entreprisePartenaire">Entreprise Partenaire</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label class="col-form-label">Partenaire  (<span style="color:red;">*</span>) :</label>
                        <select name="ListePartenaire" id="ListePartenaire" class="form-control" required>
                        </select>
                    </div>
                </div>`;

					// chargerListePartenaireYAKO();
				} else if ((typeCompte === "prestation" && profil === "nsil")) {
					cibleHtml = `
					<div class="row">
						<div class="form-group col-12">
							<label for="ciblePrestation" style="color: #000000;">
							cible <strong style="color: #F9B233;">*</strong>
							</label>
							<select id="ciblePrestation" name="ciblePrestation" class="form-control" required>
								<option value="" selected disabled>Veuillez sélectionner</option>
								<option value="administratif">administratif</option>
								<option value="technique">technique</option>
							</select>
						</div>
					</div>
					
					<div class="row">
                    <div class="form-group col-md-6 col-sm-12">
                        <label class="col-form-label">Provenance  (<span style="color:red;">*</span>) :</label>
                        <select name="provenance" id="provenance" class="form-control" required>
                            <option value="" disabled selected>...</option>
                            <option value="LLV">Reseaux Particulier</option>
                            <option value="entreprisePartenaire">Entreprise Partenaire</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label class="col-form-label">Partenaire  (<span style="color:red;">*</span>) :</label>
                        <select name="ListePartenaire" id="ListePartenaire" class="form-control" required>
                        </select>
                    </div>
                </div>`;

				}


				$("#divCible").html(cibleHtml);
			}

			$("#divCible").on("input change", "#provenance", function() {
				chargerListePartenaireYAKO();
			});

			// --- Insérer la case à cocher (état) une seule fois ---

			const divEtat = `
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="1" name="etat" id="etat" checked>
					<label class="form-check-label" for="etat">
					Activer le compte de l'utilisateur ?
					</label>
				</div>
			`;
			$("#divEtat").html(divEtat);

			// --- Gérer les changements ---
			$("#AjouterRDV").on("change", "#typeCompte", updateFormFields);
			$("#AjouterRDV").on("change", "#profil", updateFormFields);

			// --- Gérer le clic sur modification ---
			$(document).on('click', '.traiter', function() {
				const ind = this.id.split('-')[1];

				// Remplissage du modal
				$("#titreModale").text("Modifier un utilisateur");
				$("#titreAction").text("Modifier");
				$("#action").val("modifier");

				$("#agent_id").val($("#id-" + ind).html());
				$("#nom").val($("#nom-" + ind).html());
				$("#prenom").val($("#prenom-" + ind).html());
				$("#email").val($("#email-" + ind).html());
				$("#telephone").val($("#telephone-" + ind).html());
				$("#typeCompte").val($("#typeCompte-" + ind).html());
				$("#profil").val($("#profil-" + ind).html());
				$("#villesRDV").val($("#villes-" + ind).html());
				const villeid = $("#villes-" + ind).html();

				// Met à jour les champs dynamiques selon les valeurs remplies
				updateFormFields();
				if (($("#typeCompte-" + ind).html() === "rdv" || $("#typeCompte-" + ind).html() === "gestionnaire") && $("#profil-" + ind).html() === "agent") {

					getListeVillesRDV(villeid);
				}

				// Gérer le code agent si présent
				const codeagent = $("#codeagent-" + ind).html();
				$("#codeagent").val(codeagent || "");

				// ✅ Gérer l'état (1 = actif, 0 = inactif)
				const etat = $("#etat-" + ind).html()?.trim();
				if (etat === "1" || etat === "oui" || etat.toLowerCase() === "actif") {
					$("#etat").prop("checked", true);
				} else {
					$("#etat").prop("checked", false);
				}

				// Afficher le modal
				$("#AjouterRDV").modal("show");

			});

			// $(".fa-edit").click(function(evt) {

			// 	const data = evt.target.id;
			// 	const ind = data.split('-')[1];

			// 	if (ind) {
			// 		// Remplissage du modal
			// 		$("#titreModale").text("Modifier un utilisateur");
			// 		$("#titreAction").text("Modifier");
			// 		$("#action").val("modifier");

			// 		$("#agent_id").val($("#id-" + ind).html());
			// 		$("#nom").val($("#nom-" + ind).html());
			// 		$("#prenom").val($("#prenom-" + ind).html());
			// 		$("#email").val($("#email-" + ind).html());
			// 		$("#telephone").val($("#telephone-" + ind).html());
			// 		$("#typeCompte").val($("#typeCompte-" + ind).html());
			// 		$("#profil").val($("#profil-" + ind).html());
			// 		$("#villesRDV").val($("#villes-" + ind).html());
			// 		const villeid = $("#villes-" + ind).html();

			// 		// Met à jour les champs dynamiques selon les valeurs remplies
			// 		updateFormFields();
			// 		if (($("#typeCompte-" + ind).html() === "rdv" || $("#typeCompte-" + ind).html() === "gestionnaire") && $("#profil-" + ind).html() === "agent") {

			// 			getListeVillesRDV(villeid);
			// 		}

			// 		// Gérer le code agent si présent
			// 		const codeagent = $("#codeagent-" + ind).html();
			// 		$("#codeagent").val(codeagent || "");

			// 		// ✅ Gérer l'état (1 = actif, 0 = inactif)
			// 		const etat = $("#etat-" + ind).html()?.trim();
			// 		if (etat === "1" || etat === "oui" || etat.toLowerCase() === "actif") {
			// 			$("#etat").prop("checked", true);
			// 		} else {
			// 			$("#etat").prop("checked", false);
			// 		}

			// 		// Afficher le modal
			// 		$("#AjouterRDV").modal("show");
			// 	}
			// });

			// --- Cas initial (ajout d’un utilisateur) ---
			updateFormFields();
			$("#etat").prop("checked", true); // ✅ par défaut, activé




			$("#closeModaleUser").click(function(evt) {
				$('#AjouterRDV').modal('hide')
				location.reload();

			})

			// Voir detail
			$(document).on('click', '.view', function() {
				const index = this.id.split('-')[1];

				const idrdv = $("#id-" + index).html();
				const codeagent = $("#codeagent-" + index).html();
				document.cookie = "idusers=" + idrdv;
				document.cookie = "codeagent=" + codeagent;
				document.cookie = "action=traiter";
				location.href = "fiche-users";
			});





			$("#addRDV").click(function(evt) {

				var action = "ajouterMotif"
				var agent_id = null


				$("#titreModale").text("Ajouter un utilisateur")
				$("#titreAction").text("Ajouter")

				$("#action").val(action)
				$("#agent_id").val(agent_id)
				$('#AjouterRDV').modal("show")

			})


			$("#retourNotification").click(function() {

				$('#notificationValidation').modal('hide')
				location.reload();

			})

			$("#closeModaleNotification").click(function(evt) {
				$('#notificationValidation').modal('hide')
				location.reload();

			})

		})

		function myFunction() {
			var x = document.getElementById("myDIV");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
		}

		function getListeVillesRDV(idVilleEff = "") {
			//console.log("Chargement des villes de réception, sélection :", idVilleEff);

			$.ajax({
				url: "config/routes.php",
				method: "POST",
				dataType: "json",
				data: {
					idVilleEff: idVilleEff,
					etat: "getListeVillesTransformations"
				},
				success: function(response) {
					let optionsHtml = `<option value="" disabled ${!idVilleEff ? "selected" : ""}>[Villes de réception des RDV]</option>`;

					//console.log("Villes de réception des RDV :", response);
					if (response && response.length > 0) {
						$.each(response, function(index, ville) {

							const id = ville.idVilleBureau;
							const libelle = ville.libelleVilleBureau;
							const selected = idVilleEff == id ? "selected" : "";
							//console.log(id, libelle, idVilleEff);
							optionsHtml += `<option value="${id}" ${selected}>${libelle}</option>`;
						});
					} else {
						optionsHtml += `<option value="" disabled>Aucune ville disponible</option>`;
					}

					const selectHtml = `
						<label for="villesRDV" style="color: #000000;">
						Ville de réception du RDV <strong style="color: #F9B233;">*</strong>
						</label>
						<select id="villesRDV" name="villesRDV" class="form-control" required>
						${optionsHtml}
						</select>
					`;

					$("#ListeVilles").html(selectHtml);
				},
				error: function(xhr, status, error) {
					console.error("Erreur AJAX :", status, error);
					$("#ListeVilles").html(
						`<p style="color:red;">Erreur lors du chargement des villes.</p>`
					);
				}
			});
		}




		function getTraitementAjoutUtilisateur() {

			let agent_id = document.getElementById("agent_id").value;
			let action = document.getElementById("action").value;
			let nom = document.getElementById("nom").value;
			let prenom = document.getElementById("prenom").value;
			let email = document.getElementById("email").value;
			let telephone = document.getElementById("telephone").value;
			let typeCompte = document.getElementById("typeCompte").value;
			let profil = document.getElementById("profil").value;
			let etatCompte = document.getElementById("etat").checked;

			let ciblePrestation = null;
			let codeagent = null;
			let villesRDV = null;
			let provenance = null;
			let ListePartenaire = null;

			// verifier que tous les champs requis sont remplis


			if (verifierChampsRequis("#formOperateur")) {
				// Tous les champs sont remplis
				console.log("Formulaire valide, on peut envoyer !");
				// … ton code AJAX ou ta soumission ici …
			} else {
				console.log("Formulaire incomplet !");
				return;
			}




			if (etatCompte == true) {
				etatCompte = 1;
			} else {
				etatCompte = 0;
			}

			if ((typeCompte == "prestation" && profil == "agent") || (typeCompte == "prestation" && profil == "nsil")) {
				ciblePrestation = document.getElementById("ciblePrestation").value;
			}

			if ((typeCompte == "rdv" || typeCompte == "gestionnaire") && profil == "agent") {
				codeagent = document.getElementById("codeagent").value;
				villesRDV = document.getElementById("villesRDV").value;
			}

			if ((typeCompte == "sinistre" || typeCompte == "prestation") && profil == "nsil") {
				provenance = document.getElementById("provenance").value;
				ListePartenaire = document.getElementById("ListePartenaire").value;

			}



			alert(action + " " + agent_id + " " + nom + " " + prenom + " " + email + " " + telephone + " " + typeCompte + " " + profil + " " + codeagent + " " + villesRDV + " " + etatCompte + " " + ciblePrestation + " " + provenance + " " + ListePartenaire);

			$.ajax({
				url: "config/routes.php",
				data: {
					typeaction: action,
					agent_id: agent_id,
					nom: nom,
					prenom: prenom,
					email: email,
					telephone: telephone,
					typeCompte: typeCompte,
					profil: profil,
					ciblePrestation: ciblePrestation,
					codeagent: codeagent,
					villesRDV: villesRDV,
					statut: etatCompte,
					provenance: provenance,
					ListePartenaire: ListePartenaire,
					etat: "getTraitementAjoutUtilisateur"
				},
				dataType: "json",
				method: "post",
				success: function(response, status) {

					//console.log(response);
					if (response != "0") {

						let a_afficher = `<div class="alert alert-success" role="alert" style="text-align: center; font-size: 18px ; color: #033f1f; font-weight: bold">
                            ${response}
                        </div>`;
						$("#msgEchec").html(a_afficher)
						$('#notificationValidation').modal("show")
					} else {
						let a_afficher = `
						<div class="alert alert-danger" role="alert"> desole une erreur est survenue lors de ${action}  </div>`;
						$("#msgEchec").html(a_afficher)
						$('#notificationValidation').modal("show")
					}


				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});

		};

		function verifierChampsRequis(formSelector) {
			let formulaire = document.querySelector(formSelector);
			let champs = formulaire.querySelectorAll("[required]");
			let tousRemplis = true;
			let premierManquant = null;

			champs.forEach((champ) => {
				// Supprime la mise en évidence précédente
				champ.classList.remove("is-invalid");
				champ.classList.add("is-valid");


				if (
					champ.type === "checkbox" && !champ.checked ||
					champ.value.trim() === ""

				) {
					tousRemplis = false;
					champ.classList.add("is-invalid"); // Pour visuel Bootstrap par ex.
					if (!premierManquant) premierManquant = champ;
					return;
				}
			});

			if (!tousRemplis) {
				alert("Veuillez remplir tous les champs requis avant de continuer.");
				if (premierManquant) premierManquant.focus();
				tousRemplis = false;
			}

			return tousRemplis;
		}

		function chargerListePartenaireYAKO() {

			const provenance = $("#provenance").val();
			//alert(provenance)

			$("#ListePartenaire").html(`<option value="">Chargement des partenaires...</option>`);

			$.ajax({
				url: "https://api.yakoafricassur.com/enov/partenaire-list",
				method: "get",
				dataType: "json",
				data: {},
				success: function(response) {
					//console.log(response);
					if (provenance == "LLV") {
						response = response.filter(partenaire => partenaire.code == "LLV");

					} else {
						response = response.filter(partenaire => partenaire.code != "LLV");
					}

					let options = `<option value="">Choisir un partenaire </option>`;
					if (provenance != "LLV") {
						options += `<option value="All">TOUS LES PARTENAIRES</option>`;
					}
					$.each(response, function(i, value) {

						if (value.actif == "1") {

							const id = value.id;
							const code = value.code || "";
							const libelle = code == "LLV" ? "YAKO AFRICA ASSURANCES VIE" : value.designation || "";
							const valueOpe = `${id}-${code}-${libelle}`;

							options += `<option value="${valueOpe}">${libelle} (${code})</option>`;

						}

					});

					$("#ListePartenaire").html(options);
				},
				error: function(xhr, statut, err) {
					console.error("Erreur AJAX :", err);
					$("#ListePartenaire").html(`<option value="">Erreur de chargement</option>`);
				}
			});
		}
	</script>



</body>




<script>
	/*var oTable = $('#listeReclams').DataTable({
		order: [
			[0, 'desc']
		],
		buttons: [
			'copy', 'excel', 'pdf'
		]
	});*/
</script>

</html>