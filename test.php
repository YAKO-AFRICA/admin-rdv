                               
<?php


function get_nb_open_days($date_start, $date_stop)
{
    $arr_bank_holidays = array(); // Tableau des jours feriés	

    // On boucle dans le cas où l'année de départ serait différente de l'année d'arrivée
    $diff_year = date('Y', $date_stop) - date('Y', $date_start);
    for ($i = 0; $i <= $diff_year; $i++) {
        $year = (int)date('Y', $date_start) + $i;
        // Liste des jours feriés
        $arr_bank_holidays[] = '1_1_' . $year; // Jour de l'an
        $arr_bank_holidays[] = '1_5_' . $year; // Fete du travail
        $arr_bank_holidays[] = '7_8_' . $year; // independance 1960
        $arr_bank_holidays[] = '15_8_' . $year; // Assomption
        $arr_bank_holidays[] = '1_11_' . $year; // Toussaint
        //$arr_bank_holidays[] = '11_11_'.$year; // Armistice 1918
        $arr_bank_holidays[] = '25_12_' . $year; // Noel

        // Récupération de paques. Permet ensuite d'obtenir le jour de l'ascension et celui de la pentecote	
        $easter = easter_date($year);
        $arr_bank_holidays[] = date('j_n_' . $year, $easter + 86400); // Paques
        $arr_bank_holidays[] = date('j_n_' . $year, $easter + (86400 * 39)); // Ascension
        $arr_bank_holidays[] = date('j_n_' . $year, $easter + (86400 * 50)); // Pentecote	
    }
    //print_r($arr_bank_holidays);
    $nb_days_open = 0;
    $nb_days_out = 0;

    // Mettre <= si on souhaite prendre en compte le dernier jour dans le décompte	
    $i = 0;
    while ($date_start < $date_stop) {
        // Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés	
        if (
            !in_array(date('w', $date_start), array(0, 6))
            && !in_array(date('j_n_' . date('Y', $date_start), $date_start), $arr_bank_holidays)
        ) {
            $nb_days_open++;
        } else $nb_days_out++;
        $date_start = mktime(date('H', $date_start), date('i', $date_start), date('s', $date_start), date('m', $date_start), date('d', $date_start) + 1, date('Y', $date_start));
        $i++;
    }
    return array("ouvrable" => $nb_days_open, "ferie" => $nb_days_out, "total" => $i);
}

function getDatesBetween($start, $end)
{
    if ($start > $end) {
        return false;
    }

    //$sdate    = strtotime("$start +1 day");
    $sdate    = strtotime($start);
    $edate    = strtotime("$end +1 day");

    $dates = array();
    $s = 0;
    for ($i = $sdate; $i < $edate; $i += strtotime('+1 day', 0)) {
        $dates[] = date('Y-m-d', $i);
        $s++;
    }

    return $dates;
}

function listerLesSemaines($dateDebut, $dateFin)
{
    $date_debut = date('Y-m-d H:i:s', strtotime($dateDebut));
    $date_fin = date('Y-m-d H:i:s', strtotime($dateFin));

    $dates = getDatesBetween($date_debut, $date_fin);
    $compteurSemaine = 0;

    $tablo_lundi = array();
    $tablo_dim = array();

    for ($t = 0; $t <= count($dates) - 1; $t++) {

        $jour =  $dates[$t];
        if ($compteurSemaine ==  0) {
            $firstday = $jour;
            #print "1 er jour de semaine : ".$firstday." 00:00:00".PHP_EOL;
            array_push($tablo_lundi, $firstday);
            $compteurSemaine++;
        } elseif ($compteurSemaine ==  6) {
            $lastday = $jour;
            $compteurSemaine = 0;

            array_push($tablo_dim, $lastday);
            #print "dernier jour de semaine : ".$lastday." 23:59:59".PHP_EOL;
        } else {
            $compteurSemaine++;
        }
    }
    return array("debut" => $tablo_lundi, "fin" => $tablo_dim, "jours" => $dates);
}

// Exemple : Du 11 au 15 juillet il n'y a qu'un jour ouvré (week-end + 1 jours férié)
$dateDebut = '2024-01-01';
$dateFin = '2024-07-15';

$date_depart = strtotime('2024-01-01');
$date_fin = strtotime('2024-07-15');
$tablo = get_nb_open_days($date_depart, $date_fin);
$nb_jours_ouvres = $tablo["ouvrable"];
$nb_jours_ferie = $tablo["ferie"];
$nb_jours_total = $tablo["total"];

$liste_des_jours = listerLesSemaines($dateDebut, $dateFin) ;

print_r($liste_des_jours);

echo 'Il y a ' . $nb_jours_total . ' total dont : ' . $nb_jours_ouvres . ' jours ouvrés , ' . $nb_jours_ferie . ' jours feriés entre le ' . date('d/m/Y', $date_depart) . ' et le ' . date('d/m/Y', $date_fin);

?>


