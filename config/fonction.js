function getStatsGenerales(rows, colonnes) {
    const stats = {};
    stats.total = rows.length;

    // Initialiser les clés pour chaque colonne
    colonnes.forEach(col => {
        stats[col] = {};
    });

    // Parcourir les lignes
    rows.forEach(row => {
        colonnes.forEach(col => {
            let val = row[col] ?? null; // opérateur nullish coalescing
            if (val === null) val = "NON RENSEIGNÉ";

            if (!stats[col][val]) {
                stats[col][val] = 0;
            }
            stats[col][val]++;
        });
    });

    return stats;
}

function getStatsDelaiRDV(rows, colonneDate) {
    const stats = {};

    rows.forEach(row => {
        const delai = getDelaiRDV(row[colonneDate]);
        const etat = delai.etat;

        // Si l'état n'existe pas encore, on le crée
        if (!stats[etat]) {
            stats[etat] = {
                total: 0,
                couleur: delai.couleur,
                badge: delai.badge,
                libelle: delai.libelle,
                jours: [], // liste des jours pour cet état
                lignes: [] // si tu veux lister les lignes associées
            };
        }

        stats[etat].total++;
        stats[etat].jours.push(delai.jours);
        stats[etat].lignes.push({
            ...row,
            delai: delai // on ajoute toutes les infos du délai
        });
    });

    return stats;
}

function getDelaiRDV(dateRDV) {
    // Convertir d/m/Y → Y-m-d
    if (dateRDV && dateRDV.includes("/")) {
        const [jour, mois, annee] = dateRDV.split("/");
        dateRDV = `${annee}-${mois}-${jour}`;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const rdv = new Date(dateRDV);
    if (isNaN(rdv.getTime())) {
        return {
            etat: "indisponible",
            couleur: "gray",
            badge: "",
            libelle: "Date non disponible",
            jours: null
        };
    }

    rdv.setHours(0, 0, 0, 0);

    const diffTime = rdv - today;
    const jours = Math.round(diffTime / (1000 * 60 * 60 * 24));

    // RDV EXPIRÉ
    if (jours < 0) {
        return {
            etat: "expire",
            couleur: "red",
            badge: "badge badge-danger",
            libelle: `Délai expiré depuis ${Math.abs(jours)} jour(s)`,
            jours: Math.abs(jours)
        };
    }

    // RDV AUJOURD'HUI
    if (jours === 0) {
        return {
            etat: "ok",
            couleur: "#f39c12",
            badge: "badge badge-warning",
            libelle: "Aujourd’hui",
            jours: 0
        };
    }

    // RDV À VENIR
    return {
        etat: "prochain",
        couleur: "#033f1f",
        badge: "badge badge-success",
        libelle: `${jours} jour(s) restant(s)`,
        jours: jours
    };
}


function afficheuseEtat(tabloEtat) {

    const tablo_statut_rdv = {
        "1": {
            lib_statut: "En attente",
            libelle: "En attente",
            statut_traitement: "1",
            color_statut: "badge badge-secondary",
            color: "gray",
            url: "liste-rdv-attente",
            icone: "micon dw dw-edit"
        },

        "2": {
            lib_statut: "Transmis",
            libelle: "TRANSMIS",
            statut_traitement: "2",
            color_statut: "badge badge-secondary",
            color: "blue",
            url: "liste-rdv-transmis",
            icone: "micon fa fa-forward fa-2x"
        },

        "0": {
            lib_statut: "Rejete",
            libelle: "REJETE",
            statut_traitement: "0",
            color_statut: "badge badge-danger",
            color: "red",
            url: "",
            icone: "micon fa fa-close"
        },

        "3": {
            lib_statut: "Traiter",
            libelle: "TRAITER",
            statut_traitement: "3",
            color_statut: "badge badge-success",
            color: "#033f1f",
            url: "liste-rdv-traite",
            icone: "micon fa fa-check"
        },

        "-1": {
            lib_statut: "Saisie inachevée",
            libelle: "SAISIE INACHEVEE",
            statut_traitement: "-1",
            color_statut: "badge badge-dark",
            color: "black",
            url: "liste-rdv-rejet",
            icone: "micon fa fa-close"
        },
        // "4": {
        //     lib_statut: "Saisie inachevée",
        //     libelle: "SAISIE INACHEVEE",
        //     statut_traitement: "-1",
        //     color_statut: "badge badge-dark",
        //     color: "black",
        //     url: "liste-rdv-rejet",
        //     icone: "micon fa fa-close"
        // }
    };

    let optionEtat = ``;

    // ---- CALCUL TOTAL ----
    let total = 0;
    $.each(tabloEtat, function (indx, data) {
        total += parseInt(data);
    });

    // ---- CARTES INDIVIDUELLES ----
    $.each(tabloEtat, function (indx, data) {
        let valeurDDD = tablo_statut_rdv[indx] ?? 0;
        //console.log(valeurDDD);
        if (!valeurDDD) return;
        optionEtat += `
                    <div class="col-xl-3 mb-30">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:${valeurDDD.color}; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">	</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white"> ${data}</div>
										<div class="weight-600 font-14">RDV ${valeurDDD.libelle}</div>
									</div>
								</div>
							</div>
						</div>
                    `;
    });
    // ---- INJECTION HTML ----
    $("#afficheuseEtat").html(`<div class="row mb-4"> ${optionEtat} </div>`);
}

function afficheuseDelaiRDV(tabloMotif) {

    let optionMotif = ``;
    let tablo_graph = [];
    $val = "";

    $.each(tabloMotif, function (indx, data) {

        // Renommage des clés
        if (indx == "ok") {
            indx = "Aujourd’hui";
            $val = "today";
        } else if (indx == "expire") {
            indx = "Délai expiré";
            $val = "expire";
        } else if (indx == "prochain") {
            indx = "À venir";
            $val = "prochain";
        } else $val = "";

        // --- TABLEAU HTML ---
        optionMotif += `
                    <tr>
                        
                        <td><a href="synthese-rdv.php?delai=${$val}">${indx}</a></td>
                        <td>
                            <span class="badge badge-pill" 
                                style="background:${data.couleur};color:white;font-size:12px">
                                ${data.total}
                            </span>
                        </td>
                        
                    </tr>
                `;

        // --- GRAPHIQUE HIGHCHARTS (mode objet obligatoire) ---
        tablo_graph.push({
            name: indx,
            y: data.total,
            color: data.couleur
        });
    });

    // --- TABLE HTML ---
    let htmlMotif = `
                <div class="card-box pd-20 shadow-sm border rounded">
                    <h5 class="mb-3">Statistiques par Delai de Rendez-vous :</h5>
                    
                    <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Motif</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${optionMotif}
                            </tbody>
                        </table>
                    </div>
                </div>`;

    $("#afficheuseDelai").html(htmlMotif);

    Highcharts.chart('chart7', {
        chart: {
            type: 'bar', // ou 'column' ou 'pie' selon tes besoins
            options3d: {
                enabled: true,
                alpha: 15,
                beta: 15,
                depth: 50,
                viewDistance: 25
            }
        },
        title: {
            text: 'Statistiques par Delai de Rendez-vous'
        },
        xAxis: {
            type: "category"
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        legend: {
            enabled: true
        },
        series: [{
            name: 'Delai RDV',
            data: tablo_graph, // 👉 data: [{name,y,color}]
            colorByPoint: false
        }]
    });



}

function afficheuseRDVGestionnaire(tabloGestionnaire, colors) {

    let optionGestionnaire = ``;
    let tablo_graph = [];
    let tablo_color = [];
    let idcolor = 0;
    $.each(tabloGestionnaire, function (indx, data) {
        //console.log(indx);
        tablo_graph.push([indx, data, false],);
        tablo_color.push(colors[idcolor]);
        optionGestionnaire += `<tr>
                                    <td><a href="synthese-rdv.php?agent=${indx}">${indx}</a></td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
        idcolor++;
    });

    let htmlGestionnaire = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par RDV Gestionnaire :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionGestionnaire + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
    $("#afficheuseRDVGestionnaire").html(htmlGestionnaire);

    Highcharts.chart('chartRDVGestionnaire', {
        chart: {
            type: 'bar', // ou 'column' ou 'pie' selon tes besoins
            options3d: {
                enabled: true,
                alpha: 15,
                beta: 15,
                depth: 50,
                viewDistance: 25
            }
        },
        title: {
            text: 'Statistiques par RDV Gestionnaire'
        },
        xAxis: {
            type: "category"
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        legend: {
            enabled: true
        },
        series: [{
            name: 'RDV Gestionnaire',
            data: tablo_graph, // 👉 data: [{name,y,color}]
            colorByPoint: true,
            colors: tablo_color
        }]
    });
}

function formGraphEtat(valueEtat) {
    $(".dial2").knob();
    $({
        animatedVal: 0
    }).animate({
        animatedVal: valueEtat
    }, {
        duration: 3000,
        easing: "swing",
        step: function () {
            $(".dial2").val(Math.ceil(this.animatedVal)).trigger("change");
        }
    });
}

function afficheuseVilles(tabloVilles, colors) {
    //console.log(tabloVilles);
    let optionVilles = ``;
    let tablo_graph = [];
    let tablo_color = [];
    let idcolor = 0;

    $.each(tabloVilles, function (indx, data) {

        tablo_graph.push([indx, data, false],);
        tablo_color.push(colors[idcolor]);
        optionVilles += `<tr>
                                    <td><a href="synthese-rdv.php?ville=${indx}">${indx}</a></td>
                                    <td><span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px">${data}</span></td>
                                </tr>`;
        idcolor++;
    });

    //console.log(tablo_graph);
    let htmlVilles = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par ville :</h5>
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionVilles + `
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
    $("#afficheuseVilles").html(htmlVilles);
    // chart 5
    Highcharts.chart('chartVilles', {
        colors: tablo_color,
        title: {
            text: 'Statistiques par ville'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        series: [{
            type: 'pie',
            allowPointSelect: true,
            keys: ['name', 'y', 'selected', 'sliced'],
            data: tablo_graph,
            showInLegend: true
        }]
    });

}

function afficheuseMotif(tabloMotif, colors) {
    //console.log(tabloMotif);
    let optionMotif = ``;
    let tablo_graph = [];
    let tablo_color = [];
    let idcolor = 0;
    //let colors = ["red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow", "red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow"];
    $.each(tabloMotif, function (indx, data) {

        //console.log(indx);
        tablo_graph.push([indx, data, false],);
        tablo_color.push(colors[idcolor]);
        optionMotif += `<tr>
                                    <td><a href="synthese-rdv.php?motif=${indx}">${indx}</a></td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
        idcolor++;
    });

    let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Motif :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionMotif + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
    $("#afficheuseMotif").html(htmlMotif);

    if (tablo_graph.length == 0) {
        tablo_graph.push(["Aucun", 1, false],);
    } else {
        Highcharts.chart('chartMotif', {
            colors: tablo_color,
            title: {
                text: 'Statistiques par Motif'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            series: [{
                type: 'pie',
                allowPointSelect: true,
                keys: ['name', 'y', 'selected', 'sliced'],
                data: tablo_graph,
                showInLegend: true
            }]
        });
    }
}



