<!DOCTYPE html>
<html>
<head>
    <title>Statistiques Types de Prestations</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>

<div id="containerTypePrestation" style="width:100%; height:400px;"></div>

<script>
    // Exemple de données récupérées depuis le back-end PHP (via AJAX ou directement injectées)
    const dataFromPHP = [
        { libelle: "Consultation", pourcentage: 40, color: "blue" },
        { libelle: "Hospitalisation", pourcentage: 30, color: "green" },
        { libelle: "Pharmacie", pourcentage: 20, color: "orange" },
        { libelle: "Autres", pourcentage: 10, color: "red" }
    ];

    Highcharts.chart('containerTypePrestation', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Répartition des types de prestations'
        },
        xAxis: {
            categories: dataFromPHP.map(item => item.libelle),
            title: {
                text: 'Type de prestation'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Pourcentage (%)'
            }
        },
        tooltip: {
            pointFormat: 'Pourcentage: <b>{point.y:.1f}%</b>'
        },
        plotOptions: {
            column: {
                colorByPoint: true
            }
        },
        series: [{
            name: 'Types',
            data: dataFromPHP.map(item => ({
                y: item.pourcentage,
                color: item.color
            }))
        }]
    });
</script>

</body>
</html>



