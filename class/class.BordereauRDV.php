<?php

// class BordereauRDV
// {
//     public $NumeroOrdre = null;
//     public $NumeroRdv = null;
//     public $IDProposition = null;
//     public $telephone = null;
//     public $produit = null;
//     public $souscripteur = null;
//     public $assure = null;
//     public $dateEffet = null;
//     public $dateEcheance = null;
//     public $dureeContrat = null;

//     public $typeOperation = null;
//     public $cumulRachatsPartiels = null;

//     public $cumulAvances = null;
//     public $provisionNette = null;
//     public $valeurRachat = null;

//     public $valeurMaxRachat = null;
//     public $valeurMaxAvance = null;

//     public $observation = null;
//     public $garantieSurete = null;

//     public $MontantTransformation = null;
//     public $conservationCapital = null;

//     public $id_users = null;
//     public $date = null;
//     public $auteur = null;
//     public $created_at = null;
//     public $reference = null;
//     public $id= null;

//     /**
//      * Constructeur permettant de mapper les colonnes Excel aux attributs PHP
//      */
//     public function __construct($infos)
//     {
//         if ($infos !== null && is_array($infos)) {
//             //print_r($infos);

//             $map = [
//                 'Numero d\'ordre' => 'NumeroOrdre',
//                 'Numero du rendez-vous' => 'NumeroRdv',
//                 'ID Proposition' => 'IDProposition',
//                 'Numéro(s) de téléphone' => 'telephone',
//                 'Produit' => 'produit',
//                 'Nom du souscripteur' => 'souscripteur',
//                 'Nom de l\'assuré' => 'assure',
//                 'Date d\'effet' => 'dateEffet',
//                 'Date d\'échéance' => 'dateEcheance',
//                 'Durée du contrat' => 'dureeContrat',

//                 'TYPES D\'OPERATIONS' => 'typeOperation',
//                 'Cumul Rachats partiels' => 'cumulRachatsPartiels',
//                 'Cumul  Avances' => 'cumulAvances',
//                 'Provision nette' => 'provisionNette',
//                 'Valeur de rachat du contrat' => 'valeurRachat',
//                 'Valeur maximale du rachat partiel' => 'valeurMaxRachat',
//                 'Valeur maximale de l\'avance' => 'valeurMaxAvance',

//                 'OBSERVATION' => 'observation',
//                 'Garantie Sureté' => 'garantieSurete',
//                 'Montant transformation invest+' => 'MontantTransformation',
//                 'CONSERVATION DU CAPITAL' => 'conservationCapital'
//             ];
//             $map = [
//                 '0' => 'NumeroOrdre',
//                 '1' => 'NumeroRdv',
//                 '2' => 'IDProposition',
//                 '3' => 'telephone',
//                 '4' => 'produit',
//                 '5' => 'souscripteur',
//                 '6' => 'assure',
//                 '7' => 'dateEffet',
//                 '8' => 'dateEcheance',
//                 '9' => 'dureeContrat',

//                 '10' => 'typeOperation',
//                 '11' => 'cumulRachatsPartiels',
//                 '12' => 'cumulAvances',
//                 '13' => 'provisionNette',
//                 '14' => 'valeurRachat',
//                 '15' => 'valeurMaxRachat',
//                 '16' => 'valeurMaxAvance',

//                 '17' => 'observation',
//                 '18' => 'garantieSurete',
//                 '19' => 'MontantTransformation',
//                 '20' => 'conservationCapital'
//             ];

//             foreach ($map as $excelKey => $property) {
//                 if (isset($infos[$excelKey])) {
//                     $this->$property = $infos[$excelKey] !== '' ? trim($infos[$excelKey]) : null;
//                 }
//             }

//             if ($this->dateEffet != null) {
//                 $this->dateEffet = date('Y-m-d', strtotime($this->dateEffet));
//             }

//             if ($this->dateEcheance != null) {
//                 $this->dateEcheance = date('Y-m-d', strtotime($this->dateEcheance));
//             }
//         }
//     }


// }

class BordereauRDV
{
    public $NumeroOrdre = null;
    public $NumeroRdv = null;
    public $IDProposition = null;
    public $telephone = null;
    public $produit = null;
    public $souscripteur = null;
    public $assure = null;
    public $dateEffet = null;
    public $dateEcheance = null;
    public $dureeContrat = null;
    public $typeOperation = null;
    public $cumulRachatsPartiels = null;
    public $cumulAvances = null;
    public $provisionNette = null;
    public $valeurRachat = null;
    public $valeurMaxRachat = null;
    public $valeurMaxAvance = null;
    public $observation = null;
    public $garantieSurete = null;
    public $MontantTransformation = null;
    public $conservationCapital = null;
    public $id_users = null;
    public $date = null;
    public $auteur = null;
    public $created_at = null;
    public $reference = null;
    public $id = null;

    /**
     * Constructeur permettant de mapper les colonnes Excel aux attributs PHP
     */
    public function __construct($infos)
    {
        if ($infos !== null && is_array($infos)) {
            
            // Mapping basé sur les noms de colonnes (recommandé)
            $map = [
                'Numero d\'ordre' => 'NumeroOrdre',
                'Numero du rendez-vous' => 'NumeroRdv',
                'ID Proposition' => 'IDProposition',
                'Numéro(s) de téléphone' => 'telephone',
                'Produit' => 'produit',
                'Nom du souscripteur' => 'souscripteur',
                'Nom de l\'assuré' => 'assure',
                'Date d\'effet' => 'dateEffet',
                'Date d\'échéance' => 'dateEcheance',
                'Durée du contrat' => 'dureeContrat',
                'TYPES D\'OPERATIONS' => 'typeOperation',
                'Cumul Rachats partiels' => 'cumulRachatsPartiels',
                'Cumul  Avances' => 'cumulAvances',
                'Provision nette' => 'provisionNette',
                'Valeur de rachat du contrat' => 'valeurRachat',
                'Valeur maximale du rachat partiel' => 'valeurMaxRachat',
                'Valeur maximale de l\'avance' => 'valeurMaxAvance',
                'OBSERVATION' => 'observation',
                'Garantie Sureté' => 'garantieSurete',
                'Montant transformation invest+' => 'MontantTransformation',
                'CONSERVATION DU CAPITAL' => 'conservationCapital'
            ];

            foreach ($map as $excelKey => $property) {
                if (isset($infos[$excelKey])) {
                    $value = $infos[$excelKey];
                    $this->$property = ($value !== '' && $value !== null) ? trim($value) : null;
                }
            }

            // Conversion des dates
            if ($this->dateEffet != null) {
                $this->dateEffet = date('Y-m-d', strtotime($this->dateEffet));
            }

            if ($this->dateEcheance != null) {
                $this->dateEcheance = date('Y-m-d', strtotime($this->dateEcheance));
            }
            
            // Nettoyage du NumeroRdv (s'assurer que c'est un entier)
            if ($this->NumeroRdv != null) {
                $this->NumeroRdv = intval($this->NumeroRdv);
            }
        }
    }
}

// public function __construct($infos)
// {
//     if ($infos !== null && is_array($infos)) {

//         // ✅ Mapping EXACT des colonnes Excel
//         $map = [
//             "Numero d'ordre" => 'NumeroOrdre',
//             "Numero du rendez-vous" => 'NumeroRdv',
//             "ID Proposition" => 'IDProposition',
//             "Numéro(s) de téléphone" => 'telephone',
//             "Produit" => 'produit',
//             "Nom du souscripteur" => 'souscripteur',
//             "Nom de l'assuré" => 'assure',
//             "Date d'effet" => 'dateEffet',
//             "Date d'échéance" => 'dateEcheance',
//             "Durée du contrat" => 'dureeContrat',

//             "TYPES D'OPERATIONS" => 'typeOperation',
//             "Cumul Rachats partiels" => 'cumulRachatsPartiels',
//             "Cumul  Avances" => 'cumulAvances',
//             "Provision nette" => 'provisionNette',
//             "Valeur de rachat du contrat" => 'valeurRachat',
//             "Valeur maximale du rachat partiel" => 'valeurMaxRachat',
//             "Valeur maximale de l'avance" => 'valeurMaxAvance',

//             "OBSERVATION" => 'observation',
//             "Garantie Sureté" => 'garantieSurete',
//             "Montant transformation invest+" => 'MontantTransformation',
//             "CONSERVATION DU CAPITAL" => 'conservationCapital'
//         ];

//         foreach ($map as $excelKey => $property) {
//             if (isset($infos[$excelKey])) {
//                 $this->$property = $infos[$excelKey] !== '' ? trim($infos[$excelKey]) : null;
//             }
//         }

//         // ✅ Conversion dates
//         if (!empty($this->dateEffet)) {
//             $this->dateEffet = date('Y-m-d', strtotime($this->dateEffet));
//         }

//         if (!empty($this->dateEcheance)) {
//             $this->dateEcheance = date('Y-m-d', strtotime($this->dateEcheance));
//         }
//     }
// }


// class BordereauRDV
// {
//     public $NumeroOrdre;
//     public $NumeroRdv;
//     public $IDProposition;
//     public $telephone;
//     public $produit;
//     public $souscripteur;
//     public $assure;
//     public $dateEffet;
//     public $dateEcheance;
//     public $dureeContrat;

//     public $typeOperation;
//     public $cumulRachatsPartiels;

//     public $cumulAvances;
//     public $provisionNette;
//     public $valeurRachat;

//     public $valeurMaxRachat;
//     public $valeurMaxAvance;

//     public $observation;
//     public $garantieSurete;

//     public $MontantTransformation;
//     public $conservationCapital;

//     public $id_users;
//     public $date;
//     public $auteur;
//     public $created_at;
//     public $reference;
//     public $id;

//     public function __construct($infos)
//     {
//         $map = [
//             "Numero d'ordre" => 'NumeroOrdre',
//             "Numero du rendez-vous" => 'NumeroRdv',
//             "ID Proposition" => 'IDProposition',
//             "Numéro(s) de téléphone" => 'telephone',
//             "Produit" => 'produit',
//             "Nom du souscripteur" => 'souscripteur',
//             "Nom de l'assuré" => 'assure',
//             "Date d'effet" => 'dateEffet',
//             "Date d'échéance" => 'dateEcheance',
//             "Durée du contrat" => 'dureeContrat'
            
//         ];

//         foreach ($map as $excelKey => $property) {
//             if (isset($infos[$excelKey])) {
//                 $this->$property = trim($infos[$excelKey]);
//             }
//         }

//     // ✅ Conversion dates
//         if (!empty($this->dateEffet)) {
//             $this->dateEffet = date('Y-m-d', strtotime($this->dateEffet));
//         }

//         if (!empty($this->dateEcheance)) {
//             $this->dateEcheance = date('Y-m-d', strtotime($this->dateEcheance));
//         }
//     }
// }
