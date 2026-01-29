<?php
session_start();
if (empty($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}
include("../autoload.php");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "../include/entete.php"; ?>
    <title>Journal RDV</title>
</head>

<body>
    <?php include "../include/header.php"; ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">

                <h4>EXTRACTION BORDEREAU RDV</h4>
                <hr>

                <button class="btn btn-warning mb-3" onclick="history.back()">Retour</button>

                <div class="card-box pd-20" id="formFiltreRDV">

                    <div class="row">
                        <div class="col-md-6">
                            <label>Date RDV</label>
                            <input type="date" id="rdvLe" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Au</label>
                            <input type="date" id="rdvAu" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label>Ville RDV</label>
                            <?= $fonction->getVillesBureau("", "") ?>
                        </div>
                        <div class="col-md-4">
                            <label>Gestionnaire</label>
                            <select id="ListeGest" class="form-control"></select>
                        </div>
                        <div class="col-md-4">
                            <label>État RDV</label>
                            <?= $fonction->getSelectTypeEtapeRDV(); ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="filtreliste" class="btn btn-success">
                            RECHERCHER
                        </button>
                        <div id="loaderRDV" style="display:none" class="mt-3">
                            Chargement...
                        </div>
                    </div>

                </div>

                <hr>

                <div id="zoneResultats" style="display:none">

                    <div class="alert-zone mb-3"></div>

                    <h5>Résultats</h5>
                    <p>Total : <strong id="totalLignes">0</strong></p>

                    <table id="liste-extraction-bordereau-affichage"
                        class="table table-bordered table-striped nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID RDV</th>
                                <th>Contrat</th>
                                <th>Client</th>
                                <th>Motif</th>
                                <th>Date RDV</th>
                                <th>Ville</th>
                                <th>Gestionnaire</th>
                                <th class="datatable-nosort">État</th>
                            </tr>
                        </thead>
                        <tbody id="body-extraction-affichage"></tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <?php include "../include/footer.php"; ?>
    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>

    <script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>

    <script>
        let tableAffichage = null;

        /* =========================
           INITIALISATION DATATABLE
        ========================= */
        function initDataTable() {

            if ($.fn.DataTable.isDataTable('#liste-extraction-bordereau-affichage')) {
                $('#liste-extraction-bordereau-affichage').DataTable().destroy();
            }

            tableAffichage = $('#liste-extraction-bordereau-affichage').DataTable({
                responsive: true,
                pageLength: 25,
                ordering: true,
                searching: true,
                // language: {
                //     url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
                // },
                columnDefs: [{
                    targets: "datatable-nosort",
                    orderable: false
                }]
            });
        }

        /* =========================
           RECHERCHE
        ========================= */
        $("#filtreliste").on("click", function() {

            let dataPost = {
                objetRDV: $("#villesRDV").val(),
                rdvLe: $("#rdvLe").val(),
                rdvAu: $("#rdvAu").val(),
                ListeGest: $("#ListeGest").val(),
                etaperdv: $("#etaperdv").val(),
                etat: "extraireBordereau"
            };

            if (!dataPost.objetRDV && !dataPost.rdvLe && !dataPost.rdvAu) {
                alert("Veuillez renseigner au moins un critère");
                return;
            }

            $("#loaderRDV").show();
            $("#zoneResultats").hide();

            $.ajax({
                url: "../config/routes.php",
                method: "POST",
                data: dataPost,
                dataType: "json",
                success: function(data) {

                    $("#loaderRDV").hide();

                    if (!data || data.length === 0) {
                        afficherMessage("Aucun résultat", "warning");
                        return;
                    }

                    let html = "";
                    $("#totalLignes").text(data.length);

                    data.forEach((e, i) => {

                        let etats = {
                            1: "En attente",
                            2: "Transmis",
                            3: "Traité"
                        };

                        html += `
				<tr>
					<td>${i + 1}</td>
					<td>${e.idrdv}</td>
					<td>${e.police}</td>
					<td>${e.nomclient}</td>
					<td>${e.motifrdv}</td>
					<td>${convertirEnDateFR(e.daterdv)}</td>
					<td>${e.villes}</td>
					<td>${e.nomgestionnaire}</td>
					<td>${etats[e.etat] ?? "?"}</td>
				</tr>`;
                    });

                    $("#body-extraction-affichage").html(html);
                    $("#zoneResultats").fadeIn();

                    initDataTable();
                    afficherMessage("Recherche terminée", "success");
                },
                error: function() {
                    $("#loaderRDV").hide();
                    afficherMessage("Erreur serveur", "danger");
                }
            });
        });

        /* =========================
           CHANGEMENT VILLE
        ========================= */
        $(document).on("change", "#villesRDV", function() {

            if (!this.value) return;

            const [idVille, nomVille] = this.value.split(";");

            $.ajax({
                url: "../config/routes.php",
                method: "POST",
                dataType: "json",
                data: {
                    idVilleEff: idVille,
                    etat: "afficherGestionnaire"
                },
                success: function(res) {

                    let html = `<option value="">-- Gestionnaire (${nomVille}) --</option>`;
                    res.forEach(g => {
                        html += `<option value="${g.id}|${g.gestionnairenom}||">
					${g.gestionnairenom}
				</option>`;
                    });

                    $("#ListeGest").html(html);
                }
            });
        });

        /* =========================
           OUTILS
        ========================= */
        function convertirEnDateFR(d) {
            if (!d) return "";
            const [y, m, dd] = d.split("-");
            return `${dd}/${m}/${y}`;
        }

        function afficherMessage(msg, type) {
            $(".alert-zone").html(`
	<div class="alert alert-${type}">
		${msg}
	</div>`);
        }
    </script>