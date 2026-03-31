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

$liste_rdvs = $fonction->getSelectRDVAfficherGestionnaire(trim($_SESSION['id']), '2');

if ($liste_rdvs != null && is_array($liste_rdvs)) {

    $liste_rdvs = array_filter($liste_rdvs, function ($rdv) use ($fonction) {
        // Calcul du délai
        $delai = $fonction->getDelaiRDV($rdv->daterdveff);

        // On ne filtre que les RDV etat = 2 et $delai['etat'] === 'ok'
        if ($rdv->etat == "2" && $delai['etat'] == 'ok') {
            return true;
        }
        return false;
    });

    // Réindexation du tableau
    $liste_rdvs = array_values($liste_rdvs);
    $effectue = count($liste_rdvs);

    // Vérifier qu'il y a des RDV avant de faire la requête
    if ($effectue > 0) {
        // Extraire les IDs des RDV pour la requête
        $rdvIds = array_map(function($rdv) {
            return $rdv->idrdv;
        }, $liste_rdvs);
        
        $rdvIdsString = implode("','", $rdvIds);
        
        $sqlSelect = "SELECT * FROM tbl_detail_bordereau_rdv 
                      WHERE NumeroRdv IN ('" . $rdvIdsString . "')  
                      ORDER BY created_at DESC";
        $liste_bordereau = $fonction->_getSelectDatabases($sqlSelect);
		$firstBordereau = $liste_bordereau[0];
    } else {
        $liste_bordereau = [];
		$firstBordereau = null;
    }

} else {
    $effectue = 0;
    $liste_bordereau = [];
	$firstBordereau = null;
}



// if (isset($_REQUEST['reference']) && $_REQUEST['reference'] != null) {

// 	$afficheuse = true;
// 	$reference = GetParameter::FromArray($_REQUEST, 'reference');
// 	$sqlSelect = " SELECT * FROM  tbl_detail_bordereau_rdv WHERE NumeroRdv = '" . $liste_rdvs->idrdv . "'  ORDER BY created_at DESC ";
// 	// echo $sqlSelect;	exit;
// } else {
// 	$sqlSelect = " SELECT * FROM  tbl_bordereau_rdv  ORDER BY created_at DESC ";
// }
// $liste_bordereau = $fonction->_getSelectDatabases($sqlSelect);
// $effectue = is_array($liste_bordereau) ? count($liste_bordereau) : 0;

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
						<h4 style="color:#033f1f;">Récapitulatif des bordereaux de RDV du (<span style="color:#F9B233;"><?= date('d/m/Y') ?></span>)</h4>
						<p>Reference : <span style="color:#F9B233; font-weight: bold; font-size: 1.2em;"><?= $firstBordereau->reference ?? '' ?></p>
						<p>Date de création : <span style="color:#F9B233; font-weight: bold; font-size: 1.2em;"><?= date('d/m/Y H:i', strtotime($firstBordereau->created_at)); ?></p>
						<p>Total du jours : <span style="color:#F9B233; font-weight: bold; font-size: 1.2em;"><?= $effectue; ?></p>
					</div>

					
					<div class="pb-20">
						<table class="data-table table stripe hover nowrap">
							<thead>
								<tr>
									<th>#Ref</th>
									<th hidden>Id</th>
									<th hidden>IdRDV</th>
									<!-- <th>Date création</th> -->
									<th>RDV</th>
									<th>Souscripteur</th>
									<th>Type Operation</th>
									<th>Provision & cumul</th>
									<th>Valeur Rachat</th>
									<th>Observations</th>
									<th>Action</th>
									
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
											<td style="font-size: 1.2em;"><?= $i + 1; ?></td>
											<td hidden id="id-<?= $i ?>"><?= $bordereau->id; ?></td>
											<td hidden id="idrdv-<?= $i ?>"><?= $bordereau->NumeroRdv; ?></td>
											
											<td class="text-wrap" style="font-size: 1.2em;">
												N°<?= $bordereau->NumeroRdv; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													id proposition : <strong id="idcontrat-<?= $i ?>"><?= $bordereau->IDProposition; ?></strong>
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
											<td class="text-wrap" style="font-size: 1.2em;">
												<?= $bordereau->souscripteur; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													contact : <strong><?= $telephone; ?></strong>
												</p>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													assure : <strong><?= $bordereau->assure; ?></strong>
												</p>
											</td>
											<td class="text-wrap" style="font-size: 1.2em;"><?= $bordereau->typeOperation; ?></td>
											<td class="text-wrap" style="font-size: 1.2em;"><?= $provisionNette; ?>
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
											<td class="text-wrap" style="font-size: 1.2em;"><?=$rachat; ?>

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
											<td class="text-wrap">
												<button class="btn btn-success btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:#033f1f; color:white"><i class="fa fa-mouse-pointer"></i> Traiter </button>
											</td>
											
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="12" class="text-center text-danger">
											Aucun trouvé. Veuillez reafficher.
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
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
			// Traiter
			$(document).on('click', '.traiter', function() {
				const index = this.id.split('-')[1];
				// alert(index);
				const idrdv = $("#idrdv-" + index).html();
				// alert(idrdv);
				const idcontrat = $("#idcontrat-" + index).html();
				// alert(idcontrat);
				// return false;
				document.cookie = "idrdv=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "traitement-rdv-gestionnaire";
			});						
			
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