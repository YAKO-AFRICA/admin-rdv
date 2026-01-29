<?php

class tbl_prestations
{

    /* `id`, `code`, `idOtp`, `idcontrat`, `typeprestation`, `idclient`, `nom`, `prenom`, `datenaissance`, 
    `lieunaissance`, `sexe`, `cel`, `tel`, `email`, `msgClient`, `lieuresidence`, `montantSouhaite`, 
    `moyenPaiement`, `Operateur`, `telPaiement`, `IBAN`, `saisiepar`, `villedeclaration`, `mailtraitement`, `etape`, `etat`, `created_at`, `updated_at`*/
    var $id = null;
    var $code = null;
    var $idOtp = null;
    var $idcontrat = null;
    var $typeprestation = null;
    var $idclient = null;
    var $nom = null;
    var $prenom = null;
    var $datenaissance = null;
    var $lib_datenaissance = null;

    var $lieunaissance = null;
    var $sexe = null;

    var $cel = null;
    var $tel = null;
    var $email = null;
    var $msgClient = null;
    var $lieuresidence = null;
    var $montantSouhaite = null;

    var $moyenPaiement = null;
    var $lib_moyenPaiement = null;

    var $Operateur = null;
    var $lib_Operateur = null;
    var $telPaiement = null;
    var $IBAN = null;
    var $saisiepar = null;
    var $villedeclaration = null;
    var $mailtraitement = null;
    var $etape = null;
    var $etat = null;
    var $created_at = null;
    var $updated_at = null;

    var $traiterpar = null;
    var $observationtraitement = null;
    var $estMigree = null;

    var $contact = null;
    var $contact2 = null;
    var $souscripteur = null;
    var $souscripteur2 = null;
    var $details = null;

    var $genre = null;
    var $photo_genre = null;
    var $cillivilite = null;


    var $lib_statut = null;
    var $statut_traitement = null;
    var $color_statut = null;

    var $delai = null;
    var $delai_h = null;
    var $delai_i = null;
    var $delai_j = null;
    var $lib_delai = null;

    var $envoimail = false;
    var $traiterle = null;
    var $migreele  = null;
    var $migrationNsil  = "NON";

    var $color  = null;
    var  $lib_datedemande = null;

    var $libellemotif  = null;
    var  $keywordmotif = null;

    public $codeBanque;
    public $codeGuichet;
    public $numCompte;
    public $cleRIB;
    public $codemotifrejet;


    public function __construct($infos = null)
    {


        if ($infos != null) {


            foreach ($infos as $key => $value) {
                $this->{$key} = $value;
            }

            if ($this->prenom != null) $this->prenom = $this->del_char_spe2($this->prenom);

            if ($this->lieuresidence != null) $this->lieuresidence =  $this->del_char_spe2($this->lieuresidence);

            $this->id = intval($this->id);

            if ($this->nom != null) $this->souscripteur = ucfirst($this->nom . "<br>" . $this->prenom);
            if ($this->nom != null) $this->souscripteur2 = ucfirst($this->nom . " " . $this->prenom);

            if ($this->tel != "" && $this->tel != "NULL") {
                $this->contact = $this->cel . "<br>" . $this->tel;
                $this->contact2 = $this->cel . " / " . $this->tel;
            } else {
                $this->contact = $this->cel;
                $this->contact2 = $this->cel;
            }

            if ($this->prenom != null) $this->prenom = str_replace('?', 'E', $this->prenom);
            if ($this->moyenPaiement != null) $this->lib_moyenPaiement = str_replace('_', ' ', $this->moyenPaiement);
            if ($this->Operateur != null)  $this->lib_Operateur = str_replace('_', ' ', $this->Operateur);

            if ($this->montantSouhaite != null) $this->montantSouhaite = number_format($this->montantSouhaite, 0, ',', ' ');

            $this->created_at = date_format(date_create($this->created_at), "d/m/Y H:i:s");
            $this->updated_at = date_format(date_create($this->updated_at), "d/m/Y H:i:s");

            $this->lib_datedemande = $this->created_at;
            if ($this->created_at != null) {
                $dateAj = date_create($this->created_at);
                if ($dateAj !== false) {
                    $this->lib_datedemande = date_format($dateAj, "d/m/Y H:i:s");
                }
            }
            /**/

            if ($this->datenaissance != null) {
                $dateN = date_create($this->datenaissance);

                if ($dateN !== false) {
                    // Format the date
                    $this->lib_datenaissance = date_format($dateN, "d/m/Y");
                } else {
                    // Handle the error, maybe log it or assign a default value
                    //echo "Invalid date format: " . $this->datenaissance;
                    // Optionally, assign a default value to lib_datenaissance
                    $this->lib_datenaissance = '';
                }
            }


            $this->details = "Date : " . $this->created_at . "<br>dernier traitement : " . $this->updated_at . "<br>Type prestations : " . $this->typeprestation;

            if ($this->estMigree == '1') {
                $this->migrationNsil = "OUI";
            }

            if ($this->etape != null) {

                $retour_etat = $this->retourEtat($this->etape);

                $this->lib_statut = $retour_etat["lib_statut"];
                $this->statut_traitement = $retour_etat["statut_traitement"];
                $this->color_statut = $retour_etat["color_statut"];
                $this->color = $retour_etat["color"];


                $this->delai = $this->diff_date($this->created_at);
                $this->delai_j = $this->delai['jour'];
                $this->delai_h = $this->delai['heure'];
                $this->delai_i = $this->delai['minute'];


                if ($this->delai_j < '1') $this->lib_delai = $this->delai_h . ' h ' . $this->delai_i . 'min écoulé';
                else $this->lib_delai = $this->delai_j . ' jr(s) écoulé(s)';
            }
        }
    }



    function retourEtat($etat)
    {

        if ($etat == '0') $etat = 1;

        $array[1] = array("lib_statut" => Config::EN_ATTENTE, "statut_traitement" => "1", "color_statut" => Config::color_NOUVEAU, "color" => "gray");
        $array[2] = array("lib_statut" => Config::VALIDER, "statut_traitement" => "2", "color_statut" => Config::color_SUCCESS, "color" => "green");
        $array[3] = array("lib_statut" => Config::REJETE, "statut_traitement" => "3", "color_statut" => Config::color_REJETE, "color" => "red");
        $array[0] = array("lib_statut" => Config::EN_SAISIE, "statut_traitement" => "0", "color_statut" => Config::color_EN_SAISIE, "color" => "blue");

        return $array[$etat];
    }

    function del_char_spe2($str)
    {
        $a = array("\'", "\\", "\"", "/", "  ", "%", "&", 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array("", "", "", "/", " ", "", " ", 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_ireplace($a, $b, $str);
    }

    function diff_date($date_debut)
    {
        $maintenant =  date('Y-m-d H:i:s');

        $date1 = strtotime($maintenant);
        $date2 = strtotime($date_debut);

        $date_diff = $this->dateDiff($date1, $date2);
        return $date_diff;
    }

    function dateDiff($date1, $date2)
    {
        $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
        $retour = array();

        $tmp = $diff;
        $retour['second'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['second']) / 60);
        $retour['minute'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['minute']) / 60);
        $retour['heure'] = $tmp % 24;

        $tmp = floor(($tmp - $retour['heure'])  / 24);
        $retour['jour'] = $tmp;
        return $retour;
    }
}
