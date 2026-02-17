<?php

class Fonction
{
	public $Logger;
	public $Request;

	public $_Database;
	private $_Choice;
	private $_Choosed;

	public function __construct(Logger $Logger, Request $request = null)
	{
		$this->Logger = $Logger;
		$this->Request = $request;
		$this->_Database = new Database($this->Logger);
		$this->_Choosed = NULL;
	}

	public function __destruct()
	{
		$this->_Database->Close();
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


	public function getDelaiRDV($dateRDV, $dayCompare = null)
	{
		// Convertir format d/m/Y en Y-m-d
		if ($dateRDV && strpos($dateRDV, '/') !== false) {
			list($jour, $mois, $annee) = explode('/', $dateRDV);
			$dateRDV = "$annee-$mois-$jour";
		}

		// Sécurisation
		if (!$dateRDV || !strtotime($dateRDV)) {
			return [
				'etat'    => 'indisponible',
				'couleur' => 'gray', // gris
				'badge' => 'badge badge-secondary',
				'libelle' => 'Date non disponible',
				'jours'   => null,
				'code' => 500

			];
		}

		/* --- Création objets Date sans heure --- */
		// if ($dayCompare != null) {
		// 	$today =  new DateTime(date('Y-m-d', strtotime($dayCompare))); // RDV à 00:00:00
		// } else {
		// 	$today = new DateTime(date('Y-m-d'));           // Aujourd’hui à 00:00:00
		// }

		$today = new DateTime(date('Y-m-d'));           // Aujourd’hui à 00:00:00

		$rdv   = new DateTime(date('Y-m-d', strtotime($dateRDV))); // RDV à 00:00:00

		/* --- Calcul différence en jours --- */
		$diff = $today->diff($rdv);
		$jours = (int)$diff->days;

		//RDV AUJOURD'HUI
		if ($today == $rdv) {
			return [
				'etat'    => 'ok',
				'couleur' => '#f39c12', // vert
				'badge' => 'badge badge-warning',
				'libelle' => "Aujourd’hui",
				'jours'   => $jours,
				'code' => 201,
			];
		}
		//RDV EXPIRÉ (date passée)
		if ($today > $rdv) {
			return [
				'etat'    => 'expire',
				'couleur' => 'red', // rouge
				'badge' => 'badge badge-danger',
				'libelle' => "Délai expiré depuis $jours jour(s)",
				'jours'   => $jours,
				'code' => 400
			];
		}
		//RDV AUJOURD'HUI
		// if ($jours === 0) {
		// 	return [
		// 		'etat'    => 'ok',
		// 		'couleur' => '#f39c12', // vert
		// 		'badge' => 'badge badge-warning',
		// 		'libelle' => "Aujourd’hui",
		// 		'jours'   => 0,
		// 		'code' => 201,
		// 	];
		// }

		//RDV À VENIR
		return [
			'etat'    => 'prochain',
			'couleur' => '#033f1f', // orange
			'badge' => 'badge badge-success', // orange
			'libelle' => "$jours jour(s) restant(s)",
			'jours'   => $jours,
			'code' => 200

		];
	}



	function saveDocumentNSIL($fileName, $fileContent)
	{

		$networkPath = '\\\\192.168.20.16\\docnumerises\\PRESTATION\\A' . date('Y');

		if (!is_dir($networkPath)) {
			die('Le répertoire réseau spécifié est inaccessible ou n\'existe pas.');
		}

		$destinationPath = $networkPath . DIRECTORY_SEPARATOR . $fileName;

		if (file_put_contents($destinationPath, $fileContent) !== false) {
			//echo "Le fichier a été téléchargé avec succès et sauvegardé dans : $destinationPath";
			return true;
		} else {
			//echo "Une erreur s'est produite lors de la sauvegarde du fichier sur le réseau.";
			return false;
		}
	}

	function getSelectTypePrestation($params = "")
	{


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="typePrestation" id="typePrestation" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT * FROM " . Config::TABLE_TYPE_PRESTATION . " WHERE etat = 'Actif' $params ORDER BY id ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$donnees = $tab[$i];
				$id = $donnees->id;
				$libelle = $donnees->libelle;
				$description = $donnees->description;
				$values = $id . ";" . $libelle;



				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}


	function getSelectTypePrestationFiltre()
	{

		if (isset($_SESSION['profil']) && ($_SESSION['profil'] != "agent")) $cible = "  ";
		else {

			if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " WHERE prestationlibelle = 'Autre' ";
			else $cible = " WHERE prestationlibelle != 'Autre' ";
		}


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="typePrestation" id="typePrestation" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT distinct(typeprestation) as libelle FROM " . Config::TABLE_PRESTATION . "  $cible ORDER BY id ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$donnees = $tab[$i];
				$libelle = $donnees->libelle;
				$values = $i . ";" . $libelle;
				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}


	function getSelectPartenairePrestationFiltre()
	{

		if (isset($_SESSION['profil']) && ($_SESSION['profil'] != "agent")) $cible = "  ";
		else {

			if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " WHERE prestationlibelle = 'Autre' ";
			else $cible = " WHERE prestationlibelle != 'Autre' ";
		}


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="partenaire" id="partenaire" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT distinct(partenaire) as libelle FROM " . Config::TABLE_PRESTATION . "  $cible ORDER BY id ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {
				if (empty($tab[$i]->libelle)) continue;
				$donnees = $tab[$i];
				$code = $donnees->libelle;
				$libelle = $code;
				if ($libelle == "LLV") {
					$libelle = "YAKO AFRICA ASSURANCES VIE";
				} elseif ($libelle == "092") {
					$libelle = "BNI";
				}

				$values = $code;
				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}

	function getSelectTypeEtapePrestation()
	{


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="etapePrestation" id="etapePrestation" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		foreach (Config::tablo_statut_prestation as $record) {
			$code = $record['statut_traitement'];
			$libelle = $record['libelle'];
			$keyword = $record['lib_statut'];

			$values = $code . ";" . $libelle;
			$ind1 .= '<option value="' . $values . '">' . trim($keyword) . '</option>';
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}


	function getSelectTypeRDVFiltre()
	{

		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="typerdv" id="typerdv" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT distinct(TRIM(motifrdv)) as libelle FROM " . Config::TABLE_RDV . "  WHERE TRIM(motifrdv) != '' ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$donnees = $tab[$i];
				$libelle = $donnees->libelle;
				$values = $i . ";" . $libelle;
				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}

	function getSelectTypeEtapeRDV()
	{


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="etaperdv" id="etaperdv" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		foreach (Config::tablo_statut_rdv as $record) {
			$code = $record['statut_traitement'];
			$libelle = $record['libelle'];
			$keyword = $record['lib_statut'];

			$values = $code . ";" . $libelle;
			$ind1 .= '<option value="' . $values .  '">' . trim($keyword) . '</option>';
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}



	function getSelectTypeMtifRejetPrestation()
	{


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="motifRejet" id="motifRejet" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT *  FROM tbl_motifrejetprestations WHERE etat='1'  ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$donnees = $tab[$i];
				$id = $donnees->code;
				$libelle = $donnees->libelle;
				$values = $id . ";" . $libelle;
				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}


	function getSelectTypeCompteUtilisateur()
	{


		$ind1 = '';
		$ind2 = '';
		$ind = '<select name="typePrestation" id="typePrestation" class="form-control" data-msg="Objet" data-rule="required" >
													<option value="">...</option>';

		$sqlQuery = "SELECT distinct(typeCompte) as libelle FROM " . Config::TABLE_USER . "  ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$donnees = $tab[$i];
				$libelle = $donnees->libelle;
				$values = $i . ";" . $libelle;
				$ind1 .= '<option value="' . $values . '">' . trim($libelle) . '</option>';
			}
		}
		$ind3 = '</select>';
		return $ind . $ind1 . $ind3;
	}




	function getFiltreuse()
	{

		$libelle1 = $libelle2 = $libelle3 = $libelle4 = $libelle5 = $libelle6 = $libelle7 = $libelle8 = $libelle9 = $libelle10 = $libelle11 = NULL;
		$par1 = $par2 = $par3 = $par4 = $par5 = $par6 = $par7 = $par8 = $par9 = $par10 = $par11 = NULL;

		$DateDebutTrait = (isset($_REQUEST["DateDebutTrait"]) ? trim($_REQUEST["DateDebutTrait"]) : null);
		$DateFinTrait = (isset($_REQUEST["DateFinTrait"]) ? trim($_REQUEST["DateFinTrait"]) : null);
		$DateDebutPrest = (isset($_REQUEST["DateDebutPrest"]) ? trim($_REQUEST["DateDebutPrest"]) : null);
		$DateFinPrest = (isset($_REQUEST["DateFinPrest"]) ? trim($_REQUEST["DateFinPrest"]) : null);
		$DateNIL = (isset($_REQUEST["DateNIL"]) ? trim($_REQUEST["DateNIL"]) : null);
		$prenoms = (isset($_REQUEST["prenoms"]) ? trim($_REQUEST["prenoms"]) : null);
		$nom = (isset($_REQUEST["nom"]) ? trim($_REQUEST["nom"]) : null);
		$typePrestation = (isset($_REQUEST["typePrestation"]) ? trim($_REQUEST["typePrestation"]) : null);
		$codePrestation = (isset($_REQUEST["codePrestation"]) ? trim($_REQUEST["codePrestation"]) : null);
		$IdProposition = (isset($_REQUEST["IdProposition"]) ? trim($_REQUEST["IdProposition"]) : null);
		$etapePrestation = (isset($_REQUEST["etapePrestation"]) ? trim($_REQUEST["etapePrestation"]) : null);
		$motifRejet = (isset($_REQUEST["motifRejet"]) ? trim($_REQUEST["motifRejet"]) : null);
		$migration = (isset($_REQUEST["migration"]) ? trim($_REQUEST["migration"]) : null);
		$partenaire = (isset($_REQUEST["partenaire"]) ? trim($_REQUEST["partenaire"]) : null);


		if ($nom != NULL) {
			$par2 = " AND  TRIM(tbl_prestations.nom) LIKE '%" . addslashes($nom) . "%' ";
			$libelle2 = "nom : " . $nom . '</br>';
		}
		if ($prenoms != NULL) {
			$par3 = " AND  TRIM(tbl_prestations.prenom) LIKE '%" . addslashes($prenoms) . "%' ";
			$libelle3 = "prenoms : " . $prenoms . '</br>';
		}

		if ($typePrestation != NULL) {

			list($id_type, $libelle_type) = explode(';', $typePrestation, 2);
			$par4 = " AND tbl_prestations.typeprestation ='" . $libelle_type . "' ";
			$libelle4 = " type prestations : " . $libelle_type . '</br>';
		}

		if ($codePrestation != NULL) {
			$par5 = " AND   TRIM(tbl_prestations.code) ='" . $codePrestation . "' ";
			$libelle5 = " Code Prestation : " . $codePrestation . '</br>';
		}

		if ($IdProposition != NULL) {
			$par6 = " AND   TRIM(tbl_prestations.idcontrat) ='" . $IdProposition . "' ";
			$libelle6 = " Id Proposition / Id Contrat : " . $IdProposition . '</br>';
		}

		if ($DateNIL != NULL) {
			$DateNIL = @date('Y-m-d', strtotime($DateNIL));
			$par7 = " AND  ( date(tbl_prestations.migreele) = '$DateNIL' )";
			$libelle7 = "date migration NSIL : " . @date('d/m/Y', strtotime($DateNIL)) . ' </br>';
		}


		if ($DateDebutPrest != NULL and $DateFinPrest != NULL) {
			$DateDebut = @date('Y-m-d', strtotime($_REQUEST["DateDebutPrest"]));
			$DateFin = @date('Y-m-d', strtotime($_REQUEST["DateFinPrest"]));
			$par1 = " AND  ( date(tbl_prestations.created_at) between '$DateDebut' AND '$DateFin' )";
			$libelle1 = "date : " . $DateDebut . '-' . $DateFin . '</br>';
		}
		if ($DateDebutPrest != NULL and $DateFinPrest == NULL) {
			$DateDebut = @date('Y-m-d', strtotime($_REQUEST["DateDebutPrest"]));
			$par1 = " AND  (  date(tbl_prestations.created_at) = '$DateDebut' )";
			$libelle1 = "date : " . $DateDebut . '</br>';
		}
		if ($DateDebutPrest == NULL and $DateFinPrest != NULL) {
			$DateFin = @date('Y-m-d', strtotime($_REQUEST["DateFinPrest"]));
			$par1 = " AND  (  date(tbl_prestations.created_at) = '$DateFin' )";
			$libelle1 = "date : " . $DateFin . '</br>';
		}


		if ($DateDebutTrait != NULL and $DateFinTrait != NULL) {
			$DateDebut1 = @date('Y-m-d', strtotime($DateDebutTrait));
			$DateFin1 = @date('Y-m-d', strtotime($DateFinTrait));
			$par8 = " AND  ( date(tbl_prestations.updated_at) between '$DateDebut1' AND '$DateFin1' )";
			$libelle8 = "date traitement : " . $DateDebut1 . '-' . $DateFin1 . '</br>';
		}
		if ($DateDebutTrait != NULL and $DateFinTrait == NULL) {
			$DateDebut1 = @date('Y-m-d', strtotime($DateDebutTrait));
			$par8 = " AND  (  date(tbl_prestations.updated_at) = '$DateDebut1' )";
			$libelle8 = "date traitement : " . $DateDebut1 . '</br>';
		}
		if ($DateDebutTrait == NULL and $DateFinTrait != NULL) {
			$DateFin1 = @date('Y-m-d', strtotime($DateFinTrait));
			$par8 = " AND  (  date(tbl_prestations.updated_at) = '$DateFin1' )";
			$libelle8 = "date traitement : " . $DateFin1 . '</br>';
		}

		if ($etapePrestation != NULL) {

			list($etape, $libelle_etape) = explode(';', $etapePrestation, 2);
			$par6 = " AND   TRIM(tbl_prestations.etape) ='" . $etape . "' ";
			$libelle6 = " etape Prestation  : " . $libelle_etape . '</br>';
		}

		if ($motifRejet != NULL) {

			list($idmotif, $libellemotif) = explode(';', $motifRejet, 2);
			$par6 = " AND   TRIM(tbl_prestations.codemotifrejet) ='" . $idmotif . "' ";
			$libelle6 = " motif de rejet  : " . $libellemotif . '</br>';
		}
		if ($migration != NULL) {

			if ($migration == "1") $lib_migration = "Oui";
			else $lib_migration = "En attente";
			$par10 = " AND   TRIM(tbl_prestations.estMigree) ='" . $migration . "' ";
			$libelle10 = " migration NSIL  : " . $lib_migration . '</br>';
		}
		if ($partenaire != NULL) {
			$par11 = " AND   TRIM(tbl_prestations.partenaire) ='" . $partenaire . "' ";
			$libelle11 = " partenaire : " . $partenaire . '</br>';
		}

		$libelle = $libelle1 . $libelle2 . $libelle3 . $libelle4 . $libelle5 . $libelle6 . $libelle7 . $libelle8 . $libelle9 . $libelle10 . $libelle11;
		$filtreuse = $par1 . $par2 . $par3 . $par4 . $par5 . $par6 . $par7 . $par8 . $par9 . $par10 . $par11;
		return array("filtre" => $filtreuse, "libelle" => $libelle);/**/
	}


	function getFiltreuseRDV()
	{

		$libelle1 = $libelle2 = $libelle3 = $libelle4 = $libelle5 = $libelle6 = $libelle7 = $libelle8 = $libelle9 = $libelle10 = $libelle11 = $libelle12 = NULL;
		$par1 = $par2 = $par3 = $par4 = $par5 = $par6 = $par7 = $par8 = $par9 = $par10 = $par11 = $par12 = NULL;

		$rdvSouhaitLe = (isset($_REQUEST["rdvSouhaitLe"]) ? trim($_REQUEST["rdvSouhaitLe"]) : null);
		$rdvSouhaitAu = (isset($_REQUEST["rdvSouhaitAu"]) ? trim($_REQUEST["rdvSouhaitAu"]) : null);
		$rdvLe = (isset($_REQUEST["rdvLe"]) ? trim($_REQUEST["rdvLe"]) : null);
		$rdvAu = (isset($_REQUEST["rdvAu"]) ? trim($_REQUEST["rdvAu"]) : null);
		$affecteLe = (isset($_REQUEST["affecteLe"]) ? trim($_REQUEST["affecteLe"]) : null);
		$affecteAu = (isset($_REQUEST["affecteAu"]) ? trim($_REQUEST["affecteAu"]) : null);
		$saisieLe = (isset($_REQUEST["saisieLe"]) ? trim($_REQUEST["saisieLe"]) : null);
		$saisieAu = (isset($_REQUEST["saisieAu"]) ? trim($_REQUEST["saisieAu"]) : null);
		$traiterAu = (isset($_REQUEST["traiterAu"]) ? trim($_REQUEST["traiterAu"]) : null);
		$traiterLe = (isset($_REQUEST["traiterLe"]) ? trim($_REQUEST["traiterLe"]) : null);

		$ListeGest = (isset($_REQUEST["ListeGest"]) ? trim($_REQUEST["ListeGest"]) : null);
		$nom = (isset($_REQUEST["nom"]) ? trim($_REQUEST["nom"]) : null);
		$prenoms = (isset($_REQUEST["prenoms"]) ? trim($_REQUEST["prenoms"]) : null);
		$IdProposition = (isset($_REQUEST["IdProposition"]) ? trim($_REQUEST["IdProposition"]) : null);
		$typerdv = (isset($_REQUEST["typerdv"]) ? trim($_REQUEST["typerdv"]) : null);
		$etapeRdv = (isset($_REQUEST["etaperdv"]) ? trim($_REQUEST["etaperdv"]) : null);
		$villesRDV = (isset($_REQUEST["villesRDV"]) ? trim($_REQUEST["villesRDV"]) : null);
		$etatRDV = (isset($_REQUEST["etat"]) ? trim($_REQUEST["etat"]) : null);


		if (isset($etatRDV) && !empty($etatRDV) && $etatRDV != NULL) {
			list($statut, $libelle) = explode(';', $etatRDV, 2);
			$par11 = "AND  " . Config::TABLE_RDV . ".etat = '" . trim($statut) . "' ";
			$libelle11 = "etat : " . strtoupper($libelle) . '</br>';
		}

		if ($nom != NULL) {
			$par2 = "AND  TRIM(" . Config::TABLE_RDV . ".nomclient) LIKE '%" . addslashes($nom) . "%' ";
			$libelle2 = "nom : " . $nom . '</br>';
		}
		if ($prenoms != NULL) {
			$par3 = "AND  TRIM(" . Config::TABLE_RDV . ".nomclient) LIKE '%" . addslashes($prenoms) . "%' ";
			$libelle3 = "prenoms : " . $prenoms . '</br>';
		}

		if ($villesRDV != NULL) {

			list($idvillesRDV, $villesRDV) = explode(';', $villesRDV, 2);
			$par4 = " AND " . Config::TABLE_RDV . ".idTblBureau ='" . $idvillesRDV . "' ";
			$libelle4 = " ville RDV : " . $villesRDV . '</br>';
		}

		if ($etapeRdv != NULL) {

			list($etape, $libelle_etape) = explode(';', $etapeRdv, 2);
			$par6 = " AND   TRIM(" . Config::TABLE_RDV . ".etat) ='" . $etape . "' ";
			$libelle6 = " etat RDV  : " . $libelle_etape . '</br>';
		}

		if ($typerdv != NULL) {
			list($idtyperdv, $motifrdv) = explode(';', $typerdv, 2);
			$par5 = " AND   TRIM(" . Config::TABLE_RDV . ".motifrdv) ='" . trim($motifrdv) . "' ";
			$libelle5 = " Motif RDV : " . $motifrdv . '</br>';
		}

		if ($IdProposition != NULL) {
			$par6 = " AND   TRIM(tbl_prestations.idcontrat) ='" . $IdProposition . "' ";
			$libelle6 = " Id Proposition / Id Contrat : " . $IdProposition . '</br>';
		}

		if ($ListeGest != NULL) {
			list($idgestionnaire, $gestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode('|', $ListeGest, 4);
			$par7 = " AND   TRIM(" . Config::TABLE_RDV . ".gestionnaire) ='" . $idgestionnaire . "' ";
			$libelle7 = "gestionnaire : " . $gestionnaire . ' </br>';
		}


		if ($saisieLe != NULL and $saisieAu != NULL) {
			$DateDebut = @date('d/m/Y', strtotime($saisieLe));
			$DateFin = @date('d/m/Y', strtotime($saisieAu));
			$par1 = "AND STR_TO_DATE(" . Config::TABLE_RDV . ".dateajou, '%d/%m/%Y') BETWEEN STR_TO_DATE('$DateDebut', '%d/%m/%Y') AND STR_TO_DATE('$DateFin', '%d/%m/%Y')";
			$libelle1 = "date : " . $DateDebut . '-' . $DateFin . '</br>';
		} else if ($saisieLe != NULL and $saisieAu == NULL) {
			$DateDebut = @date('d/m/Y', strtotime($saisieLe));
			$par1 = "AND  ( " . Config::TABLE_RDV . ".dateajou LIKE '%$DateDebut' )";
			$libelle1 = "date : " . $DateDebut . '</br>';
		} else if ($saisieLe == NULL and $saisieAu != NULL) {
			$DateFin = @date('d/m/Y', strtotime($saisieAu));
			$par1 = "AND  (  " . Config::TABLE_RDV . ".dateajou LIKE '%$DateFin' )";
			$libelle1 = "date : " . $DateFin . '</br>';
		}

		if ($rdvLe != NULL and $rdvAu != NULL) {
			$DateDebut2 = @date('d/m/Y', strtotime($rdvLe));
			$DateFin2 = @date('d/m/Y', strtotime($rdvAu));

			$par2 = "AND ( date(" . Config::TABLE_RDV . ".daterdveff) BETWEEN '$rdvLe' AND '$rdvAu')";
			$libelle2 = "date : " . $DateDebut2 . '-' . $DateFin2 . '</br>';
		} else if ($rdvLe != NULL and $rdvAu == NULL) {
			$DateDebut2 = @date('d/m/Y', strtotime($rdvLe));
			$par2 = "AND  (  date(" . Config::TABLE_RDV . ".daterdveff) LIKE '%$rdvLe' )";
			$libelle2 = "date : " . $DateDebut2 . '</br>';
		} else if ($rdvLe == NULL and $rdvAu != NULL) {
			$DateFin2 = @date('d/m/Y', strtotime($rdvAu));
			$par2 = "AND  (  date(" . Config::TABLE_RDV . ".daterdveff) LIKE '%$rdvAu' )";
			$libelle2 = "date : " . $DateFin2 . '</br>';
		}

		if ($rdvSouhaitLe != NULL and $rdvSouhaitAu != NULL) {
			$DateDebut3 = @date('d/m/Y', strtotime($rdvSouhaitLe));
			$DateFin3 = @date('d/m/Y', strtotime($rdvSouhaitAu));
			$par3 = "AND STR_TO_DATE(" . Config::TABLE_RDV . ".daterdv, '%d/%m/%Y') BETWEEN STR_TO_DATE('$DateDebut3', '%d/%m/%Y') AND STR_TO_DATE('$DateFin3', '%d/%m/%Y')";
			$libelle3 = "date rdv : " . $DateDebut3 . '-' . $DateFin3 . '</br>';
		} elseif ($rdvSouhaitLe != NULL and $rdvSouhaitAu == NULL) {
			$DateDebut3 = @date('d/m/Y', strtotime($rdvSouhaitLe));
			$par3 = "AND  (  date(" . Config::TABLE_RDV . ".daterdv) LIKE '%$DateDebut3' )";
			$libelle3 = "date rdv : " . $DateDebut . '</br>';
		} else if ($rdvSouhaitLe == NULL and $rdvSouhaitAu != NULL) {
			$DateFin3 = @date('d/m/Y', strtotime($rdvSouhaitAu));
			$par3 = "AND  (  date(" . Config::TABLE_RDV . ".daterdv) LIKE '%$DateFin3' )";
			$libelle3 = "date rdv : " . $DateFin3 . '</br>';
		}

		if ($affecteLe != NULL and $affecteAu != NULL) {
			$DateDebut4 = @date('Y-m-d', strtotime($affecteLe));
			$DateFin4 = @date('Y-m-d', strtotime($affecteAu));
			$DateDebut = @date('d/m/Y', strtotime($affecteLe));
			$DateFin = @date('d/m/Y', strtotime($affecteAu));
			// $par4 = "AND date(" . Config::TABLE_RDV . ".transmisLe) BETWEEN ('STR_TO_DATE('$DateDebut4', '%d/%m/%Y') AND DATE_ADD(STR_TO_DATE('$DateFin4', '%d/%m/%Y'), INTERVAL 1 DAY))";
			$par4 = "AND date(" . Config::TABLE_RDV . ".transmisLe) BETWEEN '$DateDebut4' AND '$DateFin4'";
			$libelle4 = "transmis Le : " . $DateDebut . '-' . $DateFin . '</br>';
		} elseif ($affecteLe != NULL and $affecteAu == NULL) {
			$DateDebut = @date('d/m/Y', strtotime($affecteLe));
			$DateDebut4 = @date('Y-m-d', strtotime($affecteLe));
			$par4 = "AND  (  date(" . Config::TABLE_RDV . ".transmisLe) LIKE '%$DateDebut4' )";
			// $par4 = "AND  (  date(" . Config::TABLE_RDV . ".transmisLe) LIKE '%$DateDebut4' )";
			$libelle4 = "transmis Le : " . $DateDebut . '</br>';
		} else if ($affecteLe == NULL and $affecteAu != NULL) {
			$DateFin4 = @date('Y-m-d', strtotime($affecteAu));
			$DateFin = @date('d/m/Y', strtotime($affecteAu));
			$par4 = "AND  (  date(" . Config::TABLE_RDV . ".transmisLe) LIKE '%$DateFin4' )";
			$libelle4 = "transmis Le : " . $DateFin . '</br>';
		}

		if ($traiterLe != NULL and $traiterAu != NULL) {
			$DateDebut5 = @date('Y-m-d', strtotime($traiterLe));
			$DateFin5 = @date('Y-m-d', strtotime($traiterAu));
			$par12 = "AND  (date(" . Config::TABLE_RDV . ".`updatedAt`) BETWEEN '$DateDebut5' AND '$DateFin5')";
			$libelle12 = "traiter Le : " . $DateDebut5 . '-' . $DateFin5 . '</br>';
		} elseif ($traiterLe != NULL and $traiterAu == NULL) {
			$DateDebut5 = @date('Y-m-d', strtotime($traiterLe));
			$par12 = "AND  (  date(" . Config::TABLE_RDV . ".updatedAt) ='$DateDebut5' )";
			$libelle12 = "traiter Le : " . $DateDebut . '</br>';
		} else if ($traiterLe == NULL and $traiterAu != NULL) {
			$DateFin5 = @date('Y-m-d', strtotime($traiterAu));
			$par12 = "AND  (  date(" . Config::TABLE_RDV . ".updatedAt) = '$DateFin5' )";
			$libelle12 = "traiter Le : " . $DateFin5 . '</br>';
		}
		/*SELECT *
		FROM tblrdv
		WHERE 
		STR_TO_DATE(dateajou, '%d/%m/%Y') BETWEEN STR_TO_DATE('01/07/2024', '%d/%m/%Y') AND STR_TO_DATE('31/07/2024', '%d/%m/%Y')
		AND STR_TO_DATE(daterdv, '%d/%m/%Y') BETWEEN STR_TO_DATE('10/07/2024', '%d/%m/%Y') AND STR_TO_DATE('20/07/2024', '%d/%m/%Y');
		*/

		$libelle = $libelle1 . $libelle2 . $libelle3 . $libelle4 . $libelle5 . $libelle6 . $libelle7 . $libelle8 . $libelle9 . $libelle10 . $libelle11 . $libelle12;
		$filtreuse = $par1 . $par2 . $par3 . $par4 . $par5 . $par6 . $par7 . $par8 . $par9 . $par10 . $par11 . $par12;
		return array("filtre" => $filtreuse, "libelle" => $libelle);
	}

	function _getListeDocumentPrestation($id = "1")
	{

		$resultat = json_decode(file_get_contents(Config::URL_DOC_PRESTATION . $id), true);

		if ($resultat["status"] == "success") {
			$reqdoc = $resultat["data"];
			if (count($reqdoc) == 0) {
				return null;
			}
			return $reqdoc;
		}
	}

	function _getListeDocumentSinistre($id = "1")
	{

		$resultat = json_decode(file_get_contents(Config::URL_DOC_SINISTRE . $id), true);

		if ($resultat["status"] == "success") {
			$reqdoc = $resultat["data"];
			if (count($reqdoc) == 0) {
				return null;
			}
			return $reqdoc;
		}
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////

	public function _getSelectDatabases($sqlSelect)
	{

		$tab = $this->_Database->Select($sqlSelect);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}


	public function _getRetournePrestationDetails($id)
	{

		$sqlSelect = "SELECT DISTINCT tbl_prestations.*, TRIM(CONCAT(tbl_prestations.nom ,' ', tbl_prestations.prenom)) as souscripteur , tbldetailcourrier.idProposition, tbldetailcourrier.typeOperation, tbldetailcourrier.idDetail FROM " . Config::TABLE_PRESTATION . " INNER JOIN " . Config::TABLE_DETAIL_COURRIER . " ON tbl_prestations.id = tbldetailcourrier.idCourrier WHERE tbldetailcourrier.estMigree = 0 AND tbl_prestations.id=" . $id . " ORDER BY `tbldetailcourrier`.`createdAt` DESC LIMIT 1";

		$tab = $this->_Database->Select($sqlSelect);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}

	public function _getRetournePrestation($plus = NULL)
	{

		$tab = $this->_Database->Select(Config::SqlSelect_ListPrestations . "  $plus ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}

	public function _getRetournePrestation2($plus = NULL)
	{

		$tab = $this->_Database->Select(Config::SqlSelect_List_Detail_Prestations, array($plus));
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}

	public function _getRetourneListePrestation($etape, $plus = NULL)
	{

		if (isset($_SESSION['profil']) && ($_SESSION['profil'] != "agent")) $cible = "  ";
		else {

			if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " AND prestationlibelle = 'Autre' ";
			else $cible = " AND prestationlibelle != 'Autre' ";
		}
		// echo Config::SqlSelect_ListPrestations . " WHERE etape ='$etape' $cible  $plus ORDER BY id DESC  ";
		//  exit;

		$tab = $this->_Database->Select(Config::SqlSelect_ListPrestations . " WHERE etape ='$etape' $cible  $plus ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}

	public function _getRetourneAllListePrestation($plus = NULL)
	{

		// if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " AND prestationlibelle = 'Autre' ";
		// else $cible = " AND prestationlibelle != 'Autre' ";

		if (isset($_SESSION['profil']) && ($_SESSION['profil'] != "agent")) $cible = "  ";
		else {

			if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " AND prestationlibelle = 'Autre' ";
			else $cible = " AND prestationlibelle != 'Autre' ";
		}


		$tab = $this->_Database->Select(Config::SqlSelect_ListPrestations . "   $plus  $cible ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab;
	}

	public function _getValeursFormuleForSearch($sqlQuery = NULL)
	{
		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		}
		return $tab[0]->resultat;
	}


	public static function formulePourcentage($nb_ligne_element, $nb_ligne_total)
	{
		$pourcentage = ($nb_ligne_element / $nb_ligne_total) * 100;
		$pourcentage = number_format($pourcentage, 2);
		return $pourcentage;
	}
	function rangCandidate($array, $new)
	{
		$rang = 1;
		foreach ($array as $value) {
			if ($new < $value) $rang++;
		}
		return $rang;
		#return $rang.$this->indexRang($rang);
	}

	public function indexRang($rang)
	{
		if ($rang == "1") return "ere";
		else return "eme";
	}

	public function _GetUsers($plus)
	{
		//$tab = $this->_Database->Select(Config::SqlSelect_Users, array($parametreUsers));

		$parametreUsers = "  WHERE  etat = '1'  $plus ORDER BY id  LIMIT 1 ";
		$tab = $this->_Database->Select(Config::SqlSelect_Users . $parametreUsers);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des candidates');
			return NULL;
		}
		return new users($tab[0]);
	}



	function _recapGlobalePrestations()
	{

		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$icone = "";

		// if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " AND prestationlibelle = 'Autre' ";
		// else $cible = " AND prestationlibelle != 'Autre' ";

		if (isset($_SESSION['profil']) && ($_SESSION['profil'] != "agent")) $cible = "  ";
		else {

			if (isset($_SESSION['cible']) && ($_SESSION['cible'] == "administratif")) $cible = " AND prestationlibelle = 'Autre' ";
			else $cible = " AND prestationlibelle != 'Autre' ";
		}

		$tab = $this->_Database->Select(Config::SqlSelect_ListPrestations . " WHERE etape in ('1','2','3') $cible ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des prestations');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$prestation = new tbl_prestations($tab[$i]);

				if ($prestation->etape == "1") {
					$attente = $attente + 1;
				} elseif ($prestation->etape == "2") {
					$Valider = $Valider + 1;
				} elseif ($prestation->etape == "3") {
					$rejete = $rejete + 1;
				} else $autres = $autres + 1;
			}
			$nb_ligne_total = $attente + $Valider + $rejete + $autres;
		}




		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;
		$totalP = 0;
		$totalT = 0;

		foreach (Config::tablo_statut_prestation as $record) {
			$val += 1;

			$code = $record['statut_traitement'];
			$libelle = $record['libelle'];
			$lib_statut = $record['lib_statut'];
			$color_statut = $record['color_statut'];
			$color = $record['color'];
			$url = $record['url'];
			$icone = $record['icone'];

			list($color_b_1, $color_b_2) = explode('-', $color_statut, 2);

			$pourcentage_etat[$code]['etat'] = $code;
			$pourcentage_etat[$code]['libelle'] = $libelle;
			$pourcentage_etat[$code]['keyword'] = $lib_statut;
			$pourcentage_etat[$code]['bagde'] = $color_statut;
			$pourcentage_etat[$code]['bagde_color'] = $color_b_2;
			$pourcentage_etat[$code]['color'] = $color;
			$pourcentage_etat[$code]['url'] = $url;
			$pourcentage_etat[$code]['icone'] = $icone;

			if ($code == Config::etat_ATTENTE) {
				$nb_ligne_element = $attente;
			} else {

				if ($code == Config::etat_VALIDER) {
					$nb_ligne_element = $Valider;
				} else if ($code == Config::etat_REJETE) {
					$nb_ligne_element = $rejete;
				} else {
					$nb_ligne_element = $autres;
				}

				$totalT += $nb_ligne_element;
			}
			$totalT += $nb_ligne_element;

			$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
			$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;

			$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
			$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';

			$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];
			$val += $pourcentage_etat[$code]['pourcentage'];
			foreach ($pourcentage_etat as $key => $infos) {
				$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
				$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
			}
			$pourcentage_candidates['totale'] = $val;
		}

		return $pourcentage_etat;
	}

	public function pourcentageAllTypePrestation($identifiant_fis = NULL, $plus = NULL)
	{

		#Totaux
		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;

		$tab = $this->_Database->Select(Config::SqlSelect_ListTypePrestations . " WHERE etat='Actif' ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des types de prestations');
			return NULL;
		} else {

			$sqlQuery = " SELECT count(id) as resultat FROM " . Config::TABLE_PRESTATION . " ORDER BY id DESC  ";
			$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlQuery);
			if ($nb_ligne_total <= 0) $nb_ligne_total = 1;

			foreach ($tab as $key => $record) {

				$code = $record->libelle;
				$libelle = $record->libelle;
				$description = $record->description;
				$etat = $record->etat;

				$sqlQuery = " SELECT count(id) as resultat FROM " . Config::TABLE_PRESTATION . " WHERE typeprestation='" . trim($code) . "'  ORDER BY id DESC  ";
				$nb_ligne_element = $this->_getValeursFormuleForSearch($sqlQuery);

				$pourcentage_etat[$code]['etat'] = $etat;
				$pourcentage_etat[$code]['libelle'] = $libelle;
				//$pourcentage_etat[$code]['description'] = $description;

				$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
				$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;
				$pourcentage_etat[$code]['color'] = $barColorsType[$key];

				$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
				$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';
				$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];

				$val += $pourcentage_etat[$code]['pourcentage'];
				foreach ($pourcentage_etat as $key => $infos) {
					$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
					$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
				}
				$pourcentage_candidates['totale'] = $val;
			}
			return $pourcentage_etat;
		}
	}


	public function pourcentageAllMotifRejetPrestation($plus = NULL)
	{

		#Totaux
		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;

		$tab = $this->_Database->Select("SELECT * FROM tbl_motif_rejet_prestations WHERE etat='1' ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des types de prestations');
			return NULL;
		} else {

			$sqlQuery = " SELECT count(id) as resultat FROM " . Config::TABLE_PRESTATION . " ORDER BY id DESC  ";
			$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlQuery);
			if ($nb_ligne_total <= 0) $nb_ligne_total = 1;

			foreach ($tab as $key => $record) {

				$code = $record->id;
				$libelle = $record->libelle;
				$keyword = $record->keyword;
				$etat = $record->etat;

				$sqlQuery = " SELECT count(id) as resultat FROM " . Config::TABLE_PRESTATION . " WHERE etape='3' AND codemotifrejet='" . trim($code) . "'  ORDER BY id DESC  ";
				$nb_ligne_element = $this->_getValeursFormuleForSearch($sqlQuery);

				$pourcentage_etat[$code]['etat'] = $etat;
				$pourcentage_etat[$code]['libelle'] = $libelle;
				$pourcentage_etat[$code]['keyword'] = $keyword;

				$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
				$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;
				$pourcentage_etat[$code]['color'] = $barColorsType[$key];

				$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
				$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';
				$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];

				$val += $pourcentage_etat[$code]['pourcentage'];
				foreach ($pourcentage_etat as $key => $infos) {
					$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
					$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
				}
				$pourcentage_candidates['totale'] = $val;
			}
			return $pourcentage_etat;
		}
	}


	public function _UpdatePrestationRejet(tbl_prestations $prestation, $traiterpar, $idmotif, $observation)
	{
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_PRESTATION . " SET etape=? , traiterpar=? , observationtraitement=? ,codemotifrejet=? , updated_at=NOW() , traiterle = NOW()  WHERE id =?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array('3', $traiterpar, $observation, $idmotif,  $prestation->id));
		$this->Logger->Handler(__function__, 'mise à jour du rejet de la prestation ' . json_encode($prestation->id) . ' par ' . json_encode($traiterpar) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	public function _InsertDetailPrestation($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $id_prestation, $maintenant, $type = "prestation")
	{

		$sqlUpdatePrestation = "INSERT INTO " . Config::TABLE_DETAIL_COURRIER . "  ( typeOperation , libelleOperation , delaiTraitement , idProposition , idCourrier  , createdAt , type )  VALUES (?,?,?,?,?,?,?)";
		$tab = $this->_Database->Update($sqlUpdatePrestation, array($CodeTypeAvenant, addslashes($operation), $DelaiTraitement, $idcontrat, $id_prestation, $maintenant, $type));
		$this->Logger->Handler(__function__, 'mise à jour du validation de la prestation  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	public function _UpdateDetailPrestationValider($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $id_prestation, $maintenant, $idDetail)
	{
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_DETAIL_COURRIER . "  ( typeOperation , libelleOperation , delaiTraitement , idProposition , idCourrier  , createdAt )  VALUES (?,?,?,?,?,?) WHERE idDetail = ?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array($CodeTypeAvenant, $operation, $DelaiTraitement,  $idcontrat, $id_prestation, $maintenant, $idDetail));
		$this->Logger->Handler(__function__, 'mise à jour validation de la prestation ' . json_encode($id_prestation) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	public function _InsertMotifRejetPrestation($codeprestation, $codemotif, $maintenant)
	{

		$sqlUpdatePrestation = "INSERT INTO tbl_motifrejetbyprestats  ( codeprestation , codemotif , created_at )  VALUES (?,?,?)";
		$tab = $this->_Database->Update($sqlUpdatePrestation, array($codeprestation, $codemotif, $maintenant));
		$this->Logger->Handler(__function__, 'mise à jour du rejet de la prestation  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}


	function _UpdatePrestationValiderNSIL(tbl_prestations $prestation, $traiterpar, $partenaire = null)
	{

		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_PRESTATION . " SET etape=? , traiterpar=? , estMigree=? , updated_at=NOW() , traiterle = NOW() , migreele = NOW() , partenaire=? WHERE id =?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array('2', $traiterpar, '1', $partenaire,  $prestation->id));
		$this->Logger->Handler(__function__, 'mise à jour du rejet de la prestation ' . json_encode($prestation->id) . ' par ' . json_encode($traiterpar) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	function _UpdatePrestationValiderNSILByNISSA(tbl_prestations $prestation, $traiterpar)
	{

		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_PRESTATION . " SET etape=? , traiterpar=? ,  updated_at=NOW() , traiterle = NOW()  WHERE id =?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array('2', $traiterpar,  $prestation->id));
		$this->Logger->Handler(__function__, 'mise à jour du rejet de la prestation ' . json_encode($prestation->id) . ' par ' . json_encode($traiterpar) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	function _UpdateDetailCourrierPrestationNSIL($traiterpar,  $retourPrestation)
	{

		$executeDetailCourrier = [];
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_DETAIL_COURRIER . " SET idTblCourrier=? , codeCourrier=? , estMigree=?  WHERE idDetail =?";
		$tab = $this->_Database->Update($sqlUpdatePrestation, array($retourPrestation["IDTblCourrier"], $retourPrestation["CodeCourrier"], '1',  intval($retourPrestation["idDetail"])));
		$this->Logger->Handler(__function__, 'mise à jour du rejet de la prestation ' . json_encode($retourPrestation["idDetail"]) . ' par ' . json_encode($traiterpar) . ' pour ' .  ': ' . json_encode($tab));

		if ($tab != null) {
			$executeDetailCourrier[] = $tab;
		}
		return $executeDetailCourrier;
	}


	function transfertDocumentPrestationNSIL($id_prestation)
	{

		$retour_documents = $this->_getListeDocumentPrestation($id_prestation);
		$transferer = 0;
		$echoue = 0;

		if ($retour_documents != null) {

			for ($i = 0; $i < count($retour_documents); $i++) {


				$tablo = $retour_documents[$i];
				$id_prestation = $tablo["idPrestation"];
				$path_doc = trim($tablo["path"]);
				$type_doc = trim($tablo["type"]);
				$doc_name = trim($tablo["libelle"]);
				$ref_doc = trim($tablo["id"]);
				$datecreation_doc = trim($tablo["created_at"]);
				$fileContent = Config::URL_PRESTATION_RACINE . $path_doc;

				list($lib, $extension_fichier) = explode('.', $doc_name);


				$fileName = 'TESTCOUR' . date('YmdHis') . mt_rand(10, 99) . '.' . trim($extension_fichier);
				$etat = $this->saveDocumentNSIL($fileName, $fileContent);
				if ($etat) $transferer++;
				else $echoue++;
			}
		}
		return array("transferer" => $transferer, "echoue" => $echoue);
	}




	public function _GetDetailsTraitementPrestation($id, $type = "prestation")
	{

		$sqlQuery = "SELECT *  FROM " . Config::TABLE_DETAIL_COURRIER . " WHERE idCourrier='" . $id . "' AND type='" . $type . "' ORDER BY idDetail   ";
		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des candidates');
			return NULL;
		}
		return $tab[0];
	}

	public function _GetListeMotifRejetPrestation($codeprestation, $plus = null, $list = false)
	{

		//echo $sqlQuery =  "SELECT DISTINCT tbl_motifrejetbyprestats.*, tbl_motifrejetprestations.libelle as libellemotif ,tbl_motifrejetprestations.code as codemotif FROM tbl_motifrejetbyprestats INNER JOIN tbl_motifrejetprestations ON tbl_motifrejetbyprestats.codemotif = tbl_motifrejetprestations.code WHERE tbl_motifrejetbyprestats.codeprestation='" . trim($codeprestation) . "' $plus GROUP BY tbl_motifrejetbyprestats.codeprestation,tbl_motifrejetbyprestats.codemotif ORDER BY `tbl_motifrejetbyprestats`.`created_at` DESC ";
		$sqlQuery =  "SELECT DISTINCT tbl_motifrejetbyprestats.*, tbl_motifrejetprestations.libelle as libellemotif ,tbl_motifrejetprestations.code as codemotif FROM tbl_motifrejetbyprestats INNER JOIN tbl_motifrejetprestations ON tbl_motifrejetbyprestats.codemotif = tbl_motifrejetprestations.code WHERE tbl_motifrejetbyprestats.codeprestation='" . trim($codeprestation) . "' $plus  ORDER BY `tbl_motifrejetbyprestats`.`created_at` DESC ";
		//$sqlQuery = "SELECT *  FROM tbl_motifrejetprestations WHERE etat='1' $plus ORDER BY id   ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des rejets');
			return NULL;
		}

		if ($list) {
			$list = "";
			if ($tab != NULL) {
				for ($i = 0; $i <= count($tab) - 1; $i++) {
					$list .= "Motif " . intval($i + 1) . " : " . $tab[$i]->libellemotif . "<br>";
				}
				$tab = $list;
			}
		}

		return $tab;
	}


	public function _GetListeMotifRejet($plus = null)
	{

		$sqlQuery = "SELECT *  FROM tbl_motifrejetprestations WHERE etat='1' $plus ORDER BY id   ";
		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des rejets');
			return NULL;
		}
		return $tab;
	}



	public function _UpdateMotDePasse(users $users,  $password)
	{
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_USER . " SET password=?  WHERE id =?";

		$password = md5($password);
		$tab = $this->_Database->Update($sqlUpdatePrestation, array($password, $users->id));
		$this->Logger->Handler(__function__, 'mise à jour du mot de pass user ' . json_encode($users->id) . ' par ' . json_encode($password) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	public function _UpdateInformationUsers(users $users, $nom, $prenoms, $telephone, $email, $mobile2 = null)
	{
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_USER . " SET nom=? , prenom=? , telephone=? , email=? , pays=?   WHERE id =?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array(addslashes($nom), addslashes($prenoms), $telephone, $email, "COTE D'IVOIRE", $users->id));
		$this->Logger->Handler(__function__, 'mise à jour des informations personnelles ' . json_encode($users->id) . ' par ' . json_encode($users->userConnect) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}


	public function _UpdateStatutEnvoiMail(tbl_prestations $prestation, $statut, $contenu = "")
	{
		$sqlUpdatePrestation = "UPDATE " . Config::TABLE_PRESTATION . " SET envoimail=?   WHERE id =?";

		$tab = $this->_Database->Update($sqlUpdatePrestation, array($statut, $prestation->id));

		$this->Logger->Handler(__function__, 'mise à jour des informations envoi mail ' . json_encode($prestation->id) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}


	function getLieuResidence($idVilleBureau1, $option = null)
	{
		$ind1 = '';
		$ind2 = '';
		$ind = '
        
        <select name="lieuResidence" id="lieuResidence" class="form-control" data-msg="Objet" data-rule="required" ' . $option . '>
												<option value="">...</option>';

		$sqlQuery = "SELECT * FROM laloyale_bdweb.tbl_ville  ORDER BY idville ";
		$resultat = $this->_getSelectDatabases($sqlQuery);
		if ($resultat != NULL) {

			for ($a = 0; $a <= count($resultat) - 1; $a++) {
				$result = $resultat[$a];

				$idville = $result->idville;
				$libelleVille = $result->libelleVillle;
				$values = $idville . ";" . $libelleVille;

				if ($idVilleBureau1 == $idville) {
					$ind1 .= '<option value="' . $values . '" id="ob-' . $a . '" selected>' . trim($libelleVille) . '</option>';
				} else $ind1 .= '<option value="' . $values . '" id="ob-' . $a . '">' . trim($libelleVille) . '</option>';
			}

			$ind3 = '</select>';
			return $ind . $ind1 . $ind3;
		}
	}


	function getVillesBureau($idVilleBureau1, $option = null)
	{
		$ind1 = '';
		$ind2 = '';
		$ind = '
        
        <select name="villesRDV" id="villesRDV" class="form-control" data-msg="Objet" data-rule="required" ' . $option . '>
												<option value="">...</option>';

		$sqlQuery = "SELECT * FROM laloyale_bdweb.tblvillebureau WHERE idVilleBureau NOT IN ('6','7') ORDER BY idVilleBureau ";
		$resultat = $this->_getSelectDatabases($sqlQuery);
		if ($resultat != NULL) {

			for ($a = 0; $a <= count($resultat) - 1; $a++) {
				$result = $resultat[$a];

				$idVilleBureau = $result->idVilleBureau;
				$libelleVilleBureau = $result->libelleVilleBureau;
				$values = $idVilleBureau . ";" . $libelleVilleBureau;

				if ($idVilleBureau1 == $idVilleBureau) {
					$ind1 .= '<option value="' . $values . '" id="ob-' . $a . '" selected>' . trim($libelleVilleBureau) . '</option>';
				} else $ind1 .= '<option value="' . $values . '" id="ob-' . $a . '">' . trim($libelleVilleBureau) . '</option>';
			}

			$ind3 = '</select>';
			return $ind . $ind1 . $ind3;
		}
	}

	function getRetourneVillesBureau($idVilleBureau1)
	{

		$sqlQuery = "SELECT * FROM laloyale_bdweb.tblvillebureau WHERE idVilleBureau = '" . $idVilleBureau1 . "' ORDER BY idVilleBureau ";
		$resultat = $this->_getSelectDatabases($sqlQuery);
		if ($resultat != NULL) {

			return $resultat[0];
		}
		return null;
	}


	public function _InsertHistorisqueRdv($idrdv)
	{

		//$sqlHisto = $connect->exec("INSERT INTO tblrdvhistorique SELECT * FROM tblrdv WHERE idrdv =" . intval($id));
		$sqlUpdatePrestation = "INSERT INTO tblrdvhistorique  SELECT * FROM tblrdv WHERE idrdv = ?";
		$tab = $this->_Database->Update($sqlUpdatePrestation, array($idrdv));
		$this->Logger->Handler(__function__, 'mise à jour de la table tblrdvhistorique ' . json_encode($idrdv) . '  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	function _TransmettreRDVGestionnaire($etat, $reponse, $daterdveff, $datetraitement, $idgestionnaire, $idrdv, $idVilleEff, $traiterpar)
	{

		$sqlUpdateRDV = "UPDATE tblrdv SET etat= ?, reponse=?, daterdv=? ,daterdveff=?, datetraitement=?, gestionnaire=?, transmisLe =?, updatedAt =?, transmisPar = ?, villeEffective = ? , idTblBureau=?  WHERE idrdv = ?";
		$paramsRDV = array(
			$etat,
			addslashes($reponse),
			$daterdveff,
			$daterdveff,
			$datetraitement,
			$idgestionnaire,
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			intval($_SESSION["id"]),
			$idVilleEff,
			$idVilleEff,
			$idrdv
		);

		$tab = $this->_Database->Update($sqlUpdateRDV, $paramsRDV);
		$this->Logger->Handler(__function__, 'mise à jour , transmettre rdv gestionnaire ' . json_encode($idgestionnaire) . ' par ' . json_encode($traiterpar) . ' pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	function _getRetourneDetailRDV($idrdv)
	{
		/*
		  $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes   FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";

			$sqlSelect = "
			SELECT 
				tblrdv.*,           TRIM(tblvillebureau.libelleVilleBureau) AS villes,         u.nom AS nomAdmin,         u.prenom AS prenomAdmin,         u.id AS idAdmin     FROM 
				tblrdv     INNER JOIN          tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau     LEFT JOIN         users u ON u.id = tblrdv.transmisPar     WHERE         tblrdv.idrdv = '" . $idrdv . "'
		";
    
		*/
		$sqlSelect = "SELECT 
			tblrdv.*,
			concat (users.nom,' ',users.prenom) as nomgestionnaire ,
			users.email as emailgestionnaire, users.telephone as telgestionnaire,users.adresse as adressegestionnaire,users.codeagent as codeagentgestionnaire,
			tblvillebureau.libelleVilleBureau AS bureauLibelle,
			detailVille.libelleVilleBureau AS villeEffective,
			detailVilleChoisie.libelleVilleBureau AS villeChoisie,
			infoTransmis.nom AS nomAdmin,
			infoTransmis.prenom AS prenomAdmin,
			infoCourrier.etat AS etatCourrier,
			infoCourrier.reponse AS reponseCourrier,
			infoCourrier.createdAt AS createdCourrier,
			infoCourrier.deposerLe AS deposeCourrier,
			infoCourrier.traiteLe AS traiteCourrier
		FROM tblrdv
		LEFT JOIN users ON tblrdv.gestionnaire = users.id
		LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
		LEFT JOIN tblvillebureau detailVille ON detailVille.idVilleBureau = tblrdv.villeEffective
		LEFT JOIN tblvillebureau detailVilleChoisie ON detailVilleChoisie.idVilleBureau = tblrdv.idTblBureau
		LEFT JOIN users infoTransmis ON infoTransmis.id = tblrdv.transmisPar
		LEFT JOIN tblcourrier infoCourrier ON infoCourrier.idcourrier = tblrdv.idCourrier
		WHERE tblrdv.idrdv = '" . $idrdv . "'";
		return $this->_getSelectDatabases($sqlSelect);
	}


	public function pourcentageRDV($critereRecherche = null)
	{

		#Totaux
		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;

		$plus = Config::clauseSelectAnneeEncours;
		$sqlQuery = " SELECT count(idrdv) as resultat FROM " . Config::TABLE_RDV . " WHERE $plus ORDER BY idrdv  DESC  ";

		$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlQuery);
		if ($nb_ligne_total <= 0) $nb_ligne_total = 1;


		$tab = Config::tablo_statut_rdv;
		foreach ($tab as $key => $record) {

			//Array ( [lib_statut] => En attente [libelle] => EN ATTENTE [statut_traitement] => 1 [color_statut] => badge badge-secondary [color] => gray [url] => ) 

			$code = $record["statut_traitement"];
			$libelle = $record["libelle"];
			$lib_statut = $record["lib_statut"];
			$color_statut = $record["color_statut"];
			$color = $record["color"];
			$url = $record["url"];
			$etat = 'actif';

			$sqlQuery = " SELECT count(idrdv) as resultat FROM " . Config::TABLE_RDV . " WHERE etat='" . trim($code) . "' $critereRecherche and $plus  ORDER BY idrdv DESC  ";
			$nb_ligne_element = $this->_getValeursFormuleForSearch($sqlQuery);

			$pourcentage_etat[$code]['etat'] = $etat;
			$pourcentage_etat[$code]['statut'] = $code;
			$pourcentage_etat[$code]['libelle'] = $libelle;
			$pourcentage_etat[$code]['lib_statut'] = $lib_statut;

			$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
			$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;
			$pourcentage_etat[$code]['color'] = $color;
			$pourcentage_etat[$code]['badge'] = $color_statut;
			$pourcentage_etat[$code]['url'] = $url;

			$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
			$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';
			$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];

			$val += $pourcentage_etat[$code]['pourcentage'];
			foreach ($pourcentage_etat as $key => $infos) {
				$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
				$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
			}
			$pourcentage_candidates['totale'] = $val;
			/**/
		}
		return $pourcentage_etat;
	}


	public function pourcentageRDVVILLES()
	{

		#Totaux
		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;

		$sqlQuery2 = "SELECT * FROM " . Config::TABLE_VILLE . " ORDER BY idVilleBureau DESC";
		$sqlQuery = " SELECT count(idrdv) as resultat FROM " . Config::TABLE_RDV . " ORDER BY idrdv  DESC  ";
		$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlQuery);
		if ($nb_ligne_total <= 0) $nb_ligne_total = 1;


		$tab = $this->_Database->Select($sqlQuery2);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des types de prestations');
			return NULL;
		} else {

			foreach ($tab as $key => $record) {

				$code = $record->idVilleBureau;
				$idVilleBureau = $record->idVilleBureau;
				$libelle = $record->libelleVilleBureau;
				$localisation = $record->localisation;
				$etat = 'actif';

				$sqlQuery = " SELECT count(idTblBureau) as resultat FROM " . Config::TABLE_RDV . " WHERE idTblBureau = '" . $idVilleBureau . "'  ";
				$nb_ligne_element = $this->_getValeursFormuleForSearch($sqlQuery);

				$pourcentage_etat[$code]['etat'] = $etat;
				$pourcentage_etat[$code]['libelle'] = $libelle;
				$pourcentage_etat[$code]['localisation'] = $localisation;

				$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
				$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;
				//$pourcentage_etat[$code]['color'] = $barColorsType[$key];

				$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
				$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';
				$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];

				$val += $pourcentage_etat[$code]['pourcentage'];
				foreach ($pourcentage_etat as $key => $infos) {
					$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
					$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
				}
				$pourcentage_candidates['totale'] = $val;
			}
			return $pourcentage_etat;
		}
	}

	public function pourcentageRDVUSERS()
	{

		#Totaux
		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;

		$sqlQuery2 = "SELECT * FROM " . Config::TABLE_USER . " WHERE etat='1'  ORDER BY id DESC  ";
		$sqlQuery = " SELECT count(idrdv) as resultat FROM " . Config::TABLE_RDV . " ORDER BY idrdv  DESC  ";
		$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlQuery);
		if ($nb_ligne_total <= 0) $nb_ligne_total = 1;



		$tab = $this->_Database->Select($sqlQuery2);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des types de prestations');
			return NULL;
		} else {

			foreach ($tab as $key => $record) {

				$code = $record->id;
				$idVilleBureau = $record->ville;
				$nomuser = $record->nom . ' ' . $record->prenom;
				$genre = $record->genre;
				$telephone = $record->telephone;
				$typeCompte = $record->typeCompte;
				$genre = $record->genre;
				$codeagent = $record->codeagent;
				$genre = $record->genre;
				$ville = $record->ville;
				$localisation = $record->adresse . ' ' . $record->ville . ' ' . $record->pays;
				$etat = 'actif';

				$sqlQuery = " SELECT count(idTblBureau) as resultat FROM " . Config::TABLE_RDV . " WHERE idTblBureau = '" . $idVilleBureau . "' and gestionnaire='" . $code . "'  ";
				$nb_ligne_element = $this->_getValeursFormuleForSearch($sqlQuery);

				$pourcentage_etat[$code]['etat'] = $etat;
				$pourcentage_etat[$code]['nomuser'] = $nomuser;
				$pourcentage_etat[$code]['genre'] = $genre;
				$pourcentage_etat[$code]['telephone'] = $telephone;
				$pourcentage_etat[$code]['typeCompte'] = $typeCompte;
				$pourcentage_etat[$code]['codeagent'] = $codeagent;
				$pourcentage_etat[$code]['ville'] = $ville;
				$pourcentage_etat[$code]['localisation'] = $localisation;

				$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
				$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;
				//$pourcentage_etat[$code]['color'] = $barColorsType[$key];

				$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
				$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';
				$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];

				$val += $pourcentage_etat[$code]['pourcentage'];
				foreach ($pourcentage_etat as $key => $infos) {
					$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
					$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
				}
				$pourcentage_candidates['totale'] = $val;
			}
			return $pourcentage_etat;
		}
	}



	function getNomDocument($type)
	{
		$noms = [
			'RIB' => 'RIB',
			'Police' => "Police du contrat d'assurance",
			'bulletin' => 'Bulletin de souscription',
			'AttestationPerteContrat' => 'Attestation de déclaration de perte',
			'CNI' => 'CNI',
			'etatPrestation' => 'Fiche de demande de prestation',
		];
		return $noms[$type] ?? "Fiche d'identification du numéro de paiement";
	}

	function afficherDocumentJoint($id_prestation, $doc)
	{
		$documents = Config::URL_PRESTATION_RACINE . trim($doc["path"]);
		$nom_document = $this->getNomDocument($doc["type"]);
		$ref_doc = trim($doc["id"]);
		$datecreation_doc = trim($doc["created_at"]);
		$doc_name = trim($doc["libelle"]);

		$values = $id_prestation . "-" . $ref_doc . "-" . $nom_document . "-" . $doc_name;
		return <<<HTML
		<div class="document-card d-flex align-items-center mt-3 p-2 border rounded" id="line_".$ref_doc>
			<input type="hidden" class="val_doc" value="{$values}" hidden>
			<div class="fm-file-box text-success p-2"><i class="fa fa-file-pdf-o"></i></div>
			<div class="flex-grow-1 ms-2">
				<h6 class="mb-0" style="font-size: 12px;">
					<a href="{$documents}" target="_blank">$nom_document</a>
				</h6>
				<p class="mb-0 text-secondary" style="font-size: 0.8em;">$datecreation_doc</p>
			</div>
			<span id="checking_".$ref_doc> </span>
			<button class="btn btn-warning" data-doc-id="{$documents}" data-path-doc="{$documents}" style="background-color:#F9B233;">
				<i class="dw dw-eye"> voir</i>
			</button>
		

			
		</div>
		HTML;
	}

	function getRetourneOptionRDV($user)
	{
		$sqlSelect = "SELECT * FROM tbloptionrdv WHERE codelieu = '" . $user->ville . "' ORDER BY codejour ASC ";
		$option_rdv = $this->_getSelectDatabases($sqlSelect);
		return $option_rdv;
	}

	// Charge les options de rendez-vous pour chaque ville réseau
	function getRetourJourReception($idVilleEff)
	{
		$sqlSelect = "SELECT * FROM tbloptionrdv WHERE codelieu = '" . $idVilleEff . "' ORDER BY codejour ASC ";
		//echo $sqlSelect; exit;
		$resultat = $this->_getSelectDatabases($sqlSelect);
		if ($resultat != NULL) {
			return $resultat;
		} else return null;
	}


	function getParametreGlobalPrestations()
	{

		$col = "";

		$retourStatut = $this->_recapGlobalePrestations();
		if (isset($retourStatut) && $retourStatut != null) {
			foreach ($retourStatut as $etat => $statut) {
				$col .= '
						<div class="col-xl-3 mb-30">
						<a href="' . trim($statut["url"]) . '">
						<div class="card-box height-100-p widget-style1 text-white"
							style="background-color:' . trim($statut["color"]) . '; font-weight:bold; ">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									
								</div>
								 
								<div class="widget-data">
									<div class="h4 mb-0 text-white">' . trim($statut["nb_ligne_element"]) . '</div>
									<div class="weight-600 font-14">DEMANDES ' . trim(strtoupper($statut["keyword"])) . '
									</div>
								</div>
							</div>
						</div>
						</a>
					</div>';
			}

			return $col . '<div class="col-xl-3 mb-30">
						<a href="liste-prestations">
						<div class="card-box height-100-p widget-style1"
							style="background-color:whitesmoke; font-weight:bold; color:#033f1f ">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<!-- <span class="micon dw dw-folder"></span> -->

								</div>
								<div class="widget-data">
									<div class="h4 mb-0">' . trim($retourStatut[1]['nb_ligne_total']) . '</div>
									<div class="weight-600 font-14" style="color:#033f1f !important;">TOTALS DEMANDES
										PRESTATIONS</div>
								</div>
							</div>
						</div>
						</a>
					</div>';
		} else return false;
	}

	public function setFiltrePrestationTechnique()
	{
		$form = '
		
			<div class="card-body">
				<form method="POST">
					<div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;">
						<div class="row">
							<div class="col-md-4 form-group">
								<label style="color: #033f1f !important;">Date début</label>
								<input type="date" class="form-control" name="DateDebutPrest" id="DateDebutPrest" /><br>
							</div>

							<div class="col-md-4 form-group">
								<label style="color: #033f1f !important;">Date fin</label>
								<input type="date" class="form-control" name="DateFinPrest" id="DateFinPrest" />
							</div>

							<div class="col-md-4 form-group">
								<label style="color: #033f1f !important;">Type</label>
								' . $this->getSelectTypePrestation() . '
							</div>
						</div>
					</div>

					<div class="modal-footer" id="footer">
						<button type="submit" name="filtreliste" id="filtreliste" class="btn btn-secondary" style="background: #F9B233; color: white">FILTRER</button>
					</div>
				</form>
			</div>
		';

		return $form;
	}


	function _insertInfosBordereauRDV($id_villes, $villes, $id_gestionnaire, $gestionnaire, $periode_1, $periode_2, $reference, $etat, $observation = null, $id_users = null, $auteur = null)
	{
		if ($id_users == null) $id_users = $_SESSION["id"];
		if ($auteur == null) $auteur = $_SESSION["utilisateur"];
		if ($periode_1 == "") $periode_1 = null;
		if ($periode_2 == "") $periode_2 = null;

		//`tbl_bordereau_rdv`(`id`, `reference`, `id_villes`, `villes`, `id_gestionnaire`, `gestionnaire`, `periode_1`, `periode_2`, `observation`, `etat`, `created_at`) 
		$sqlQuery = "INSERT INTO `tbl_bordereau_rdv`(`reference`, `id_villes`, `villes`, `id_gestionnaire`, `gestionnaire`, `periode_1`, `periode_2`, `observation`, `etat`, `id_users`, `auteur`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$parametreInsert = array(
			$reference,
			$id_villes,
			addslashes(trim($villes)),
			$id_gestionnaire,
			addslashes(trim($gestionnaire)),
			$periode_1,
			$periode_2,
			$observation,
			$etat,
			$id_users,
			$auteur,
			date('Y-m-d H:i:s')
		);
		$tab = $this->_Database->Update($sqlQuery, $parametreInsert);
		$this->Logger->Handler(__function__, 'mise à jour de la table tbl_bordereau_rdv de l\'idrdv ' . json_encode($reference) . '  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}
	function _updateInfosBordereauRDV($id_villes, $villes, $id_gestionnaire, $gestionnaire, $periode_1, $periode_2, $reference, $etat, $observation = null, $id_users = null, $auteur = null)
	{
		if ($id_users == null) $id_users = $_SESSION["id"];
		if ($auteur == null) $auteur = $_SESSION["utilisateur"];
		if ($periode_1 == "") $periode_1 = null;
		if ($periode_2 == "") $periode_2 = null;

		$sqlUpdate = "UPDATE tbl_bordereau_rdv SET id_villes=?, villes=?, id_gestionnaire=?, gestionnaire=?, periode_1=?,  periode_2=? , observation =?, etat =?, id_users =?, auteur =?, created_at=? WHERE reference = ?";
		$queryOptions = array(
			
			$id_villes,
			addslashes(trim($villes)),
			$id_gestionnaire,
			addslashes(trim($gestionnaire)),
			$periode_1,
			$periode_2,
			$observation,
			$etat,
			$id_users,
			$auteur,
			date('Y-m-d H:i:s'),
			$reference
		);

		$tab = $this->_Database->Update($sqlUpdate, $queryOptions);
		$this->Logger->Handler(__function__, 'mise à jour de la table tbl_bordereau_rdv de l\'idrdv ' . json_encode($reference) . '  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	function _insertBordereauRDV(BordereauRDV $ligneBordereau, $idrdv, $reference, $id_users = null, $auteur = null)
	{
		if ($id_users == null) $id_users = $_SESSION["id"];
		if ($auteur == null) $auteur = $_SESSION["utilisateur"];
		if ($idrdv == null) $idrdv = $ligneBordereau->NumeroRdv;

		$sqlQuery = "INSERT INTO `tbl_detail_bordereau_rdv`(`reference`, `NumeroOrdre`, `NumeroRdv`, `IDProposition`, `telephone`, `produit`, `souscripteur`, `assure`, `dateEffet`, `dateEcheance`, `dureeContrat`, `typeOperation`, `cumulRachatsPartiels`, `cumulAvances`, `provisionNette`, `valeurRachat`, `valeurMaxRachat`, `valeurMaxAvance`, `observation`, `garantieSurete`, `MontantTransformation`, `conservationCapital`, `id_users`, `auteur`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$parametreInsert = array(
			$reference,
			$ligneBordereau->NumeroOrdre,
			$idrdv,
			$ligneBordereau->IDProposition,
			$ligneBordereau->telephone,
			$ligneBordereau->produit,
			addslashes(trim($ligneBordereau->souscripteur)),
			$ligneBordereau->assure,
			$ligneBordereau->dateEffet,
			$ligneBordereau->dateEcheance,
			$ligneBordereau->dureeContrat,
			$ligneBordereau->typeOperation,
			$ligneBordereau->cumulRachatsPartiels,
			$ligneBordereau->cumulAvances,
			$ligneBordereau->provisionNette,
			$ligneBordereau->valeurRachat,
			$ligneBordereau->valeurMaxRachat,
			$ligneBordereau->valeurMaxAvance,
			$ligneBordereau->observation,
			$ligneBordereau->garantieSurete,
			$ligneBordereau->valeurRachat,
			$ligneBordereau->conservationCapital,
			$id_users,
			$auteur,
			date('Y-m-d H:i:s')
		);
		$tab = $this->_Database->Update($sqlQuery, $parametreInsert);
		$this->Logger->Handler(__function__, 'mise à jour de la table tbl_detail_bordereau_rdv de l\'idrdv ' . json_encode($idrdv) . '  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}

	//////////////////////////////////////////////
	public function afficheuseGlobalStatistiqueRDV($critereRecherche = null, $lienUrl = "liste-rdv")
	{

		$affiche_2 =	'';
		$affiche_3 =	'';
		$total = 0;
		$retourStatut = $this->pourcentageRDV($critereRecherche);
		if (isset($retourStatut) && $retourStatut != null) {

			foreach ($retourStatut as $etat => $statut) {
				if ($statut["statut"] == "-1") {
					continue;
				} else {
					$total += $statut["nb_ligne_element"];

					if ($critereRecherche != null && $statut["nb_ligne_element"] <= 0) {
						continue;
					}
					$values = $statut["statut"] . ";" . $statut["libelle"];
					$affiche_2 .= '<div class="col-xl-3 mb-30">
							<a href="' . $lienUrl . '?i=' . $statut["statut"] . '">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:' . trim($statut["color"]) . '; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">
									</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white">' . trim($statut["nb_ligne_element"]) . '</div>
										<div class="weight-600 font-14">RDV ' . trim(strtoupper($statut["libelle"])) . '</div>
									</div>
								</div>
							</div>
							</a>
						</div>';
				}
			}
			if ($critereRecherche != null) {
				$affiche_3 .= '<div class="col-xl-3 mb-30">
							<a href="' . $lienUrl . '">
								<div class="card-box height-100-p widget-style1"
									style="background-color:whitesmoke; font-weight:bold; color:#033f1f ">
									<div class="d-flex flex-wrap align-items-center">
										<div class="progress-data">

										</div>
										<div class="widget-data">
											<div class="h4 mb-0">' . intval($total) . '</div>
											<div class="weight-600 font-14" style="color:#033f1f !important;">TOTALS DEMANDES
												RDV</div>
										</div>
									</div>
								</div>
							</a>
						</div>';
			} else {
				$affiche_3 .= '
				<div class="col-xl-3 mb-30">
					<a href="' . $lienUrl . '">
						<div class="card-box height-100-p widget-style1"
							style="background-color:white; font-weight:bold; color:#033f1f ">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">

								</div>
								<div class="widget-data">
									<div class="h4 mb-0">' . trim($retourStatut[1]['nb_ligne_total']) . '</div>
									<div class="weight-600 font-14" style="color:#033f1f !important;">TOTALS DEMANDES
										RDV</div>
								</div>
							</div>
						</div>
					</a>
				</div>
				';
			}

			return	'<div class="row">' . $affiche_2 . $affiche_3 . '</div>';
		}
	}

	public function insertRecuperationMotPasse(users $user, $email)
	{
		try {
			$sqlQuery = "INSERT INTO `tbl_recup_users`(`email`, `profil`, `typeCompte`, `login`, `id_users`, `created_at`, `etat`) VALUES (?,?,?,?,?,?,?) ";
			$parametreInsert = array(
				$email ?? '',
				$user->profil ?? '',
				$user->typeCompte ?? '',
				$user->login ?? '',
				$user->id,
				date('Y-m-d H:i:s'),
				1,
			);


			$tab = $this->_Database->Update($sqlQuery, $parametreInsert);
			$this->Logger->Handler(__function__, 'mise à jour de la table tbl_detail_bordereau_rdv de l\'idrdv ' . json_encode($user->id) . '  pour ' .  ': ' . json_encode($tab));
			return $tab["LastInsertId"];
		} catch (Exception $e) {
			$this->Logger->Handler(
				__FUNCTION__,
				sprintf("Erreur d’insertion dans tbl_recup_users (%s) : %s", $email, $e->getMessage())
			);
			return false;
		}
	}

	public function updateRecuperationMotPasse(users $user, $email)
	{

		try {
			$sqlQuery = "UPDATE `tbl_recup_users` SET `profil` = ? ,typeCompte = ?, login = ?, id_users = ?, traiterle = ? , modifiele = ? , etat = ? , email = ? WHERE `id_users` = ?";
			$parametreInsert = array(
				$user->profil ?? null,
				$user->typeCompte ?? null,
				$user->login ?? null,
				$user->id ?? null,
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				1,
				$email ?? null,
				$user->id
			);

			$tab = $this->_Database->Update($sqlQuery, $parametreInsert);
			$this->Logger->Handler(__function__, 'mise à jour de la table tbl_detail_bordereau_rdv de l\'idrdv ' . json_encode($user->id) . '  pour ' .  ': ' . json_encode($tab));
			return $tab;
		} catch (Exception $e) {
			$this->Logger->Handler(
				__FUNCTION__,
				sprintf("Erreur d’insertion dans tbl_recup_users (%s) : %s", $email, $e->getMessage())
			);
			return false;
		}
	}

	public function getRetourneTypePrestation($plus)
	{

		$sqlQuery = "SELECT * FROM " . Config::TABLE_TYPE_PRESTATION . " WHERE etat = 'Actif'  $plus ORDER BY id ";

		$tab = $this->_Database->Select($sqlQuery);
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des details ');
			return NULL;
		} else {
			return $tab;
		}
	}

	public function getSelectRDVAfficher($etape = NULL, $etape2 = NULL)
	{

		if ($etape == NULL) $etape = "";
		else $etape = " AND tblrdv.etat ='$etape'  OR tblrdv.etat = '$etape2' ";

		$plus = Config::clauseSelectAnneeEncours;
		$orderBy = Config::orderBySelectAnneeEncours;


		// $sqlSelect = " SELECT 	tblrdv.*, CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, TRIM(tblvillebureau.libelleVilleBureau) AS villes
		// 	FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id 	LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
		// 	WHERE   $plus  $etape 	ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC ";

		$sqlSelect = "SELECT 
			tblrdv.*,	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id LEFT JOIN tblvillebureau 	ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE  $plus $etape  ORDER BY 		$orderBy	";
		//echo $sqlSelect;exit;
		return  $this->_getSelectDatabases($sqlSelect);
	}
	public function getSelectRDVAfficherGestionnaireOld($gestionnaireId, $critereEtat = NULL)
	{

		if ($critereEtat == NULL) $critereEtat = "";
		else $critereEtat = " AND tblrdv.etat ='$critereEtat' ";

		$plus = Config::clauseSelectAnneeEncours;
		$orderBy = Config::orderBySelectAnneeEncours;

		$sqlSelect = "SELECT 		tblrdv.*,	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id LEFT JOIN tblvillebureau 	ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE tblrdv.gestionnaire= '" . trim($gestionnaireId) . "'  AND $plus $critereEtat  ORDER BY 		$orderBy	";
		//echo $sqlSelect;exit;
		return  $this->_getSelectDatabases($sqlSelect);
	}

	public function getSelectRDVAfficherGestionnaire($gestionnaireId, $critereEtat = NULL)
	{

		if ($critereEtat == NULL) $critereEtat = "";
		else $critereEtat = " AND tblrdv.etat ='$critereEtat' ";

		$plus = Config::clauseSelectAnneeEncours;
		$orderBy = Config::orderBySelectAnneeEncours;

		
		$agent_principal = $_SESSION['agent_principal'];
		// echo $agent_principal;exit;
		if ($_SESSION['profil'] == 'interim') {
			// $sqlSelect = "SELECT
			// 		tblrdv.*,
			// 		CONCAT(u.nom, ' ', u.prenom) AS nomgestionnaire,
			// 		TRIM(vb.libelleVilleBureau) AS villes
			// 	FROM tblrdv
			// 	INNER JOIN users u 
			// 		ON tblrdv.gestionnaire = '$agent_principal'
			// 	INNER JOIN tblvillebureau vb 
			// 		ON tblrdv.idTblBureau = vb.idVilleBureau
			// 	WHERE $plus $critereEtat AND u.id = $agent_principal ORDER BY 		$orderBy  
			// ";
			$sqlSelect = " SELECT
					tblrdv.*,
					CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
					TRIM(tblvillebureau.libelleVilleBureau) AS villes
				FROM tblrdv
				LEFT JOIN users 
					ON tblrdv.gestionnaire = users.id
				LEFT JOIN tblvillebureau  
					ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
				WHERE tblrdv.gestionnaire IN ('$agent_principal', '" . trim($gestionnaireId) . "')
					AND $plus $critereEtat
				ORDER BY $orderBy
			";
		}else{
			$sqlSelect = "SELECT 		tblrdv.*,	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id LEFT JOIN tblvillebureau 	ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE tblrdv.gestionnaire= '" . trim($gestionnaireId) . "'  AND $plus $critereEtat  ORDER BY 		$orderBy	";
		}

		// echo $sqlSelect;exit;


		// echo $sqlSelect;exit;
		return  $this->_getSelectDatabases($sqlSelect);
	}



	public function pourcentageRDVBy($type = "statut", $critereRecherche = null, $formatGraph = false)
	{
		$pourcentage_etat = [];
		$rang_candidates = [];
		$val = 0;

		// Couleurs par défaut pour graph
		$barColorsType = ["red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray"];
		$badgeColorsType = ["badge-danger", "badge-success", "badge-primary", "badge-warning", "badge-info", "badge-light", "badge-dark", "badge-primary", "badge-secondary"];
		//$pourcentage_etat[$code]['badge'] = $color_statut;

		if (isset($_SESSION['typeCompte']) && $_SESSION['typeCompte'] == "rdv") {
			$cible = "  ";
		} else {
			$cible = "  gestionnaire = '" . $_SESSION['id'] . "' AND ";
		}


		$plus = " $cible  YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE())";
		// Total RDV
		$sqlTotal = "SELECT COUNT(idrdv) as resultat FROM " . Config::TABLE_RDV . " WHERE $plus  $critereRecherche ORDER BY idrdv  DESC  ";
		$nb_ligne_total = $this->_getValeursFormuleForSearch($sqlTotal);
		if ($nb_ligne_total <= 0) $nb_ligne_total = 1;

		switch ($type) {
			case "statut":
				$categories = Config::tablo_statut_rdv;
				foreach ($categories as $k => $record) {
					$code = $record["statut_traitement"];
					$sql = "SELECT COUNT(idrdv) as resultat 
                        FROM " . Config::TABLE_RDV . " 
                        WHERE etat='" . trim($code) . "' AND $plus $critereRecherche";
					$nb_ligne_element = $this->_getValeursFormuleForSearch($sql);

					$pct = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
					$pourcentage_etat[$code] = [
						"etat"            => "actif",
						"statut"          => $code,
						"keyword"      => $record["libelle"],
						"libelle"         => $record["libelle"],
						"lib_statut"      => $record["lib_statut"],
						"nb_ligne_element" => $nb_ligne_element,
						"nb_ligne_total"  => $nb_ligne_total,
						"color"           => $record["color"] ?? $barColorsType[$k % count($barColorsType)],
						"badge"           => $record["color_statut"],
						"url"             => $record["url"],
						"pourcentage"     => $pct,
						"lib_pourcentage" => $pct . "%"
					];
					$rang_candidates[] = $pct;
				}
				break;

			case "ville":
				$categories = $this->_Database->Select("SELECT * FROM " . Config::TABLE_VILLE . " WHERE idVilleBureau NOT IN ('6', '7')  ORDER BY idVilleBureau DESC");
				foreach ($categories as $k => $record) {
					$code = $record->idVilleBureau;
					$sql = "SELECT COUNT(idrdv) as resultat 
                        FROM " . Config::TABLE_RDV . " 
                        WHERE idTblBureau = '" . $code . "' AND $plus";
					$nb_ligne_element = $this->_getValeursFormuleForSearch($sql);

					$pct = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
					$pourcentage_etat[$code] = [
						"etat"            => "actif",
						"libelle"         => $record->libelleVilleBureau,
						"keyword"      => $record->idVilleBureau,
						"localisation"    => $record->localisation,
						"nb_ligne_element" => $nb_ligne_element,
						"nb_ligne_total"  => $nb_ligne_total,
						"color"           => $barColorsType[$k % count($barColorsType)],
						"badge"           => $badgeColorsType[$k % count($badgeColorsType)],
						"pourcentage"     => $pct,
						"lib_pourcentage" => $pct . "%"
					];
					$rang_candidates[] = $pct;
				}
				break;

			case "user":
				$categories = $this->_Database->Select("SELECT * FROM " . Config::TABLE_USER . " WHERE etat='1' and typeCompte='gestionnaire' $critereRecherche ORDER BY id DESC");
				foreach ($categories as $k => $record) {
					$code = $record->id;
					$sql = "SELECT COUNT(idrdv) as resultat 
                        FROM " . Config::TABLE_RDV . " 
                        WHERE idTblBureau = '" . $record->ville . "' 
                        AND gestionnaire='" . $code . "' AND $plus ";

					$nb_ligne_element = $this->_getValeursFormuleForSearch($sql);

					$pct = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
					$pourcentage_etat[$code] = [
						"etat"            => "actif",
						"nomuser"         => $record->nom . " " . $record->prenom,
						"genre"           => $record->genre,
						"telephone"       => $record->telephone,
						"typeCompte"      => $record->typeCompte,
						"codeagent"       => $record->codeagent,
						"ville"           => $record->ville,
						"localisation"    => $record->adresse . " " . $record->ville . " " . $record->pays,
						"nb_ligne_element" => $nb_ligne_element,
						"nb_ligne_total"  => $nb_ligne_total,
						"color"           => $barColorsType[$k % count($barColorsType)],
						"pourcentage"     => $pct,
						"lib_pourcentage" => $pct . "%"
					];
					$rang_candidates[] = $pct;
				}
				break;

			default:
				$categories = $this->_Database->Select("SELECT DISTINCT(motifrdv) FROM " . Config::TABLE_RDV . " ORDER BY motifrdv");
				foreach ($categories as $k => $record) {
					$code = $record->motifrdv;
					$sql = "SELECT COUNT(idrdv) as resultat 
						FROM " . Config::TABLE_RDV . " 
						WHERE motifrdv='" . $code . "' AND $plus";
					$nb_ligne_element = $this->_getValeursFormuleForSearch($sql);

					$pct = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
					$pourcentage_etat[$code] = [
						"etat"            => "actif",
						"libelle"         => $record->motifrdv,
						"nb_ligne_element" => $nb_ligne_element,
						"nb_ligne_total"  => $nb_ligne_total,
						"color"           => $barColorsType[$k % count($barColorsType)],
						"pourcentage"     => $pct,
						"lib_pourcentage" => $pct . "%"
					];
					$rang_candidates[] = $pct;
				}
				break;
		}

		// Ajout rang et classement
		foreach ($pourcentage_etat as $key => $infos) {
			$rang = $this->rangCandidate($rang_candidates, $infos["pourcentage"]);
			$pourcentage_etat[$key]["rang"] = $rang;
			$pourcentage_etat[$key]["classement"] = $rang . $this->indexRang($rang);
		}

		// Option format Graph (labels + valeurs + couleurs)
		if ($formatGraph) {
			$labels = [];
			$values = [];
			$colors = [];
			foreach ($pourcentage_etat as $infos) {
				$labels[] = $infos["libelle"] ?? $infos["nomuser"] ?? "N/A";
				$values[] = $infos["pourcentage"];
				$colors[] = $infos["color"] ?? "gray";
			}
			return [
				"labels" => $labels,
				"values" => $values,
				"colors" => $colors,
				"raw"    => $pourcentage_etat
			];
		}

		return $pourcentage_etat;
	}


	public function getSelectSinistreAfficher($etape = NULL)
	{
		if ($etape == NULL) $etape = "";
		else $etape = " AND etape ='$etape' ";
		$plus = " YEAR(tbl_sinistres.created_at) = YEAR(CURDATE())";

		$sqlSelect = " SELECT * FROM tbl_sinistres  WHERE $plus $etape  ORDER BY id DESC ";
		return  $this->_getSelectDatabases($sqlSelect);
	}


	function _recapGlobaleSinistre()
	{

		$total = 0;
		$rejete = 0;
		$attente = 0;
		$Valider = 0;
		$autres = 0;

		$icone = "";

		$tab = $this->_Database->Select("SELECT * FROM tbl_sinistres  WHERE etape in ('1','2','3') ORDER BY id DESC  ");
		if ($this->_Database->ErrorMessage != NULL || count($tab) == 0) {
			$this->Logger->Handler(__function__, 'echec recuperation de la liste des prestations');
			return NULL;
		} else {
			for ($i = 0, $maxI = count($tab); $i < $maxI; $i++) {

				$sinistre = $tab[$i];

				if ($sinistre->etape == "1") {
					$attente = $attente + 1;
				} elseif ($sinistre->etape == "2") {
					$Valider = $Valider + 1;
				} elseif ($sinistre->etape == "3") {
					$rejete = $rejete + 1;
				} else $autres = $autres + 1;
			}
			$nb_ligne_total = $attente + $Valider + $rejete + $autres;
		}




		$pourcentage_etat = array();
		$rang_etat = array();
		$val = 0;
		$totalP = 0;
		$totalT = 0;

		foreach (Config::tablo_statut_prestation as $record) {
			$val += 1;

			$code = $record['statut_traitement'];
			$libelle = $record['libelle'];
			$lib_statut = $record['lib_statut'];
			$color_statut = $record['color_statut'];
			$color = $record['color'];
			$url = $record['url'];
			$icone = $record['icone'];

			list($color_b_1, $color_b_2) = explode('-', $color_statut, 2);

			$pourcentage_etat[$code]['etat'] = $code;
			$pourcentage_etat[$code]['libelle'] = $libelle;
			$pourcentage_etat[$code]['keyword'] = $lib_statut;
			$pourcentage_etat[$code]['bagde'] = $color_statut;
			$pourcentage_etat[$code]['bagde_color'] = $color_b_2;
			$pourcentage_etat[$code]['color'] = $color;
			$pourcentage_etat[$code]['url'] = $url;
			$pourcentage_etat[$code]['icone'] = $icone;

			if ($code == Config::etat_ATTENTE) {
				$nb_ligne_element = $attente;
			} else {

				if ($code == Config::etat_VALIDER) {
					$nb_ligne_element = $Valider;
				} else if ($code == Config::etat_REJETE) {
					$nb_ligne_element = $rejete;
				} else {
					$nb_ligne_element = $autres;
				}

				$totalT += $nb_ligne_element;
			}
			$totalT += $nb_ligne_element;

			$pourcentage_etat[$code]['nb_ligne_element'] = $nb_ligne_element;
			$pourcentage_etat[$code]['nb_ligne_total'] = $nb_ligne_total;

			$pourcentage_etat[$code]['pourcentage'] = self::formulePourcentage($nb_ligne_element, $nb_ligne_total);
			$pourcentage_etat[$code]['lib_pourcentage'] = $pourcentage_etat[$code]['pourcentage'] . '%';

			$rang_candidates[] = $pourcentage_etat[$code]['pourcentage'];
			$val += $pourcentage_etat[$code]['pourcentage'];
			foreach ($pourcentage_etat as $key => $infos) {
				$pourcentage_etat[$key]['rang'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']);
				$pourcentage_etat[$key]['classement'] = $this->rangCandidate($rang_candidates, $infos['pourcentage']) . $this->indexRang($pourcentage_etat[$key]['rang']);
			}
			$pourcentage_candidates['totale'] = $val;
		}

		return $pourcentage_etat;
	}


	function getParametreGlobalSinistre()
	{

		$col = "";

		$retourStatut = $this->_recapGlobaleSinistre();
		if (isset($retourStatut) && $retourStatut != null) {
			foreach ($retourStatut as $etat => $statut) {

				$col .= '
						<div class="col-xl-3 mb-30">
						<a href="liste-sinistres?i=' . trim($statut["etat"]) . '">
						<div class="card-box height-100-p widget-style1 text-white"
							style="background-color:' . trim($statut["color"]) . '; font-weight:bold; ">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									
								</div>
								 
								<div class="widget-data">
									<div class="h4 mb-0 text-white">' . trim($statut["nb_ligne_element"]) . '</div>
									<div class="weight-600 font-14">DECLARATIONS ' . trim(strtoupper($statut["keyword"])) . '
									</div>
								</div>
							</div>
						</div>
						</a>
					</div>';
			}

			return $col . '<div class="col-xl-3 mb-30">
						<a href="liste-sinistres">
						<div class="card-box height-100-p widget-style1"
							style="background-color:whitesmoke; font-weight:bold; color:#033f1f ">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<!-- <span class="micon dw dw-folder"></span> -->

								</div>
								<div class="widget-data">
									<div class="h4 mb-0">' . trim($retourStatut[1]['nb_ligne_total']) . '</div>
									<div class="weight-600 font-14" style="color:#033f1f !important;">TOTALS DECLARATIONS
										SINISTRES</div>
								</div>
							</div>
						</div>
						</a>
					</div>';
		} else return false;
	}



	function retourneMoisCourant($date = null, $formatJourSeulement = false)
	{
		if ($date != null) {
			$mois = date('m', strtotime($date));
			$annee = date('Y', strtotime($date));
		} else {
			$mois = date('m');
			$annee = date('Y');
		}


		$nbJours = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
		$dates = [];

		for ($j = 1; $j <= $nbJours; $j++) {
			$date = strtotime("$annee-$mois-$j");
			$dates[] = $formatJourSeulement
				? date("d", $date)
				: date("Y-m-d", $date);
		}

		return $dates;   // du 1 au 31 selon le mois
	}

	function retourneSemaineCourante($formatJourSeulement = false)
	{
		$debutSemaine = strtotime("monday this week");
		$jours = [];

		for ($i = 0; $i < 7; $i++) {
			$date = strtotime("+$i day", $debutSemaine);
			$jours[] = $formatJourSeulement
				? date("d", $date)
				: date("Y-m-d", $date);
		}

		return $jours; // lundi → dimanche
	}

	function recapTraitementEffectue($jour, $profil, $usersid)
	{
		$plus = Config::clauseSelectAnneeEncours;
		//$plus = "  DATE(tblrdv.daterdveff) = '$jour' ";
		$plus = "  ( (tblrdv.daterdv LIKE '%/%' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') = '$jour') OR (tblrdv.daterdv LIKE '%-%' AND STR_TO_DATE(tblrdv.daterdv, '%d-%m-%Y') = '$jour') )";
		if ($profil == "gestionnaire" || $profil == "agent") $plus .= " AND tblrdv.gestionnaire = '$usersid' ";
		$sqlSelect = " SELECT * FROM " . Config::TABLE_RDV . " WHERE $plus ORDER BY idrdv  DESC  ";

		$resultat  = $this->_getSelectDatabases($sqlSelect);
		if (isset($resultat) && $resultat != null) {

			$nb_ligne_total = count($resultat);
			$compteur = array("jour" => $jour);
			$result = $this->compteur($resultat);
			$compteur = array_merge($compteur, $result);
			return $compteur;
		}
		return null;
	}

	function compteur($tablo)
	{

		$total_en_attente = 0;
		$total_transmis = 0;
		$total_rejete = 0;
		$total_traiter = 0;
		$total_saisi_inacheve = 0;

		if (!empty($tablo)) {
			$total = count($tablo);
			for ($i = 0; $i <= ($total - 1); $i++) {
				$ligne = $tablo[$i];

				if (!empty($ligne->etat)) {
					if ($ligne->etat == "1") $total_en_attente = $total_en_attente + 1;
					if ($ligne->etat == "2") $total_transmis = $total_transmis + 1;
					if ($ligne->etat == "0" || $ligne->etat == "") $total_rejete = $total_rejete + 1;
					if ($ligne->etat == "4" || $ligne->etat == "3") {
						$total_traiter = $total_traiter + 1;

						//print_r($ligne->traiterLe);
					}
					if ($ligne->etat == "5" || $ligne->etat == "-1") $total_saisi_inacheve = $total_saisi_inacheve + 1;
				}
			}

			return array("en_attente" => $total_en_attente, "transmis" => $total_transmis, "rejeter" => $total_rejete, "traiter" => $total_traiter, "saisi_inacheve" => $total_saisi_inacheve, "result" => "OK", "total" => $i, "tablo" => $tablo);
		}
		return null;
	}

	function getCompteurParJour($jour)
	{

		$plus = " YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE()) AND DATE(tblrdv.daterdveff) = '$jour' ";
		$sql = "SELECT etat FROM " . Config::TABLE_RDV . " WHERE $plus";
		$retour = $this->_getSelectDatabases($sql);
		if (isset($retour) && $retour != null) {
			return $this->compteur($retour);
		}
		return null;
	}



	function getStatsGenerales(array $rows, array $colonnes)
	{
		$stats = [];
		$stats['total'] = count($rows);

		foreach ($colonnes as $col) {
			$stats[$col] = [];
		}

		foreach ($rows as $row) {
			foreach ($colonnes as $col) {
				$val = $row->$col ?? null;
				if ($val === null) $val = '(NULL)';

				if (!isset($stats[$col][$val])) {
					$stats[$col][$val] = 0;
				}
				$stats[$col][$val]++;
			}
		}

		return $stats;
	}


	function getRecupereInfosGestionnaire($codeagent)
	{

		$sql = "SELECT * FROM laloyale_bduser.membre WHERE codeagent = '$codeagent' AND memberok='1' AND typ_membre IN ('2','3')  ";
		$retour = $this->_getSelectDatabases($sql);
		if (isset($retour) && $retour != null) {
			return $retour[0];
		}
		return null;
	}

	function getRetourneContactInfosGestionnaire($codeagent)
	{

		// Initialisation
		$contactsGestionnaireHTML = '';
		$telephone = '';
		$email_agent = '';
		$email_membre = '';
		$i = 0;

		// Récupération gestionnaire
		$retour_detail_agent = $this->get_detail_agent($codeagent);
		$retour_membre = $this->getRecupereInfosGestionnaire($codeagent);
		// Email membre (fallback si agent sans email)
		if ($retour_membre && !empty($retour_membre->email)) {
			$email_membre = trim($retour_membre->email);
		}

		// Sécurisation du tableau des contacts
		$contacts = $retour_detail_agent->mescontacts ?? [];

		foreach ($contacts as $contact) {

			// Sécurisation des champs
			$value       = trim($contact->Contact ?? '');
			$valueAnc    = trim($contact->Contact_anc ?? '');
			$type        = strtoupper(trim($contact->CodeTypeContact ?? ''));
			$label       = $contact->typeContact ?? 'Contact';

			// Si rien dans les deux champs → ignorer
			if ($value === '' && $valueAnc === '') {
				continue;
			}

			// Valeur finale (priorité au contact principal)
			$finalValue = $value !== '' ? $value : $valueAnc;

			$i++; // compteur propre

			/** --------------------------------
			 *   GESTION EMAIL
			 * -------------------------------- */
			if (in_array($type, ['EMAIL', 'MAIL'], true)) {
				$email_agent = $finalValue; // toujours dernier email valable
			}

			/** --------------------------------
			 *   GESTION TÉLÉPHONE
			 * -------------------------------- */
			if (in_array($type, ['TEL', 'CEL', 'MOBILE'], true)) {
				$telephone = $finalValue;
			}

			/** --------------------------------
			 *   CONSTRUCTION LISTE HTML
			 * -------------------------------- */
			$contactsGestionnaireHTML .= sprintf(
				'<li><strong>%s %d :</strong> %s</li>',
				htmlspecialchars($label),
				$i,
				htmlspecialchars($finalValue)
			);
		}


		// ------------------------------------
		// Fallback email final
		// ------------------------------------
		$email_final = $email_agent !== '' ? $email_agent : $email_membre;


		// ------------------------------------
		// Résultat final structuré (PRODUCTION)
		// ------------------------------------
		$result = [
			'email_agent'     => $email_agent,
			'email_membre'    => $email_membre,
			'email_final'     => $email_final,
			'telephone'       => $telephone,
			'contacts_html'   => $contactsGestionnaireHTML
		];
		return $result;
	}


	function get_detail_agent($codeagent)
	{

		if ($codeagent != null) {

			$tabloCritere = [
				'codeagent' => $codeagent,
				'myinfo' => 'PERSO'
				//'typeReseau' => $codInfo
			];

			$api_detail_agent = $this->getAPI($tabloCritere, "https://api.laloyalevie.com/enov/fiche-detaille-agent-bis");
			return $api_detail_agent;
		}
		return null;
	}

	function getAPI($tabloCritere, $url_api)
	{
		try {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
			curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 2);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tabloCritere));
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 60000);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/json",));
			$data = curl_exec($ch);
			$data = json_decode($data);
			return $data;
		} catch (Exception $e) {
			echo 'Exception reçue : ', $e->getMessage(), "\n";
			return 0;
		}
	}

	function getRetourneInfosBordereaux($critere)
	{

		$sqlQuery = "SELECT * FROM `tbl_detail_bordereau_rdv`  $critere ";
		//echo $sqlQuery; exit;
		return  $this->_getSelectDatabases($sqlQuery);
	}

	function priseRdvExceptionnel($idContrat, $telephone, $email, $statutDemandeur, $typePrestation, $daterdveff, $villesRDV, $ListeGest, $nomclient, $datenaissance, $lieuResidence, $etat = "1")
	{
		/*
		`tblrdv`(`idrdv`, `nomclient`, `tel`, `email`, `daterdv`, `daterdv2`, `messageclient`, `codedmd`, `dateajou`, `dateajou2`, `etat`, 
		`motifrdv`, `police`, `reponse`, `datetraitement`, `gestionnaire`, `envoimail`, `titre`, `datenaissance`, `lieuresidence`, `idTblBureau`, 
		`villeEffective`, `createdAt`, `reponseGest`, `estPermit`, `etatTraitement`, `libelleTraitement`, `idCourrier`, `creeLe`, `updatedAt`, 
		`transmisLe`, `traiterLe`, `daterdveff`, `transmisPar`, `etatSms`, `orderInsert`, `oldgestionnaire`, `rangaff`)
		*/

		$codeRdv = $this->genererCodeRDV();
		list($idmotifrdv, $motifrdv) = explode(";", $typePrestation, 2);
		list($idTblBureau, $villeEffective) = explode(";", $villesRDV, 2);
		list($idGestionnaire, $gestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode("|", $ListeGest, 4);
		$sqlQuery = " INSERT INTO `tblrdv` (`codedmd`,`nomclient`,`datenaissance`,`police`, `tel`, `email`, `titre`, `motifrdv`, `daterdveff`,`daterdv`, `idTblBureau`,`villeEffective`, `gestionnaire`, `etat`, `lieuresidence`, `dateajou`, `createdAt`, `creeLe`, `transmisLe`, `transmisPar`,`orderInsert`) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW(), ?, ?)";
		$parametreInsert = array(
			$codeRdv,
			addslashes($nomclient),
			$datenaissance,
			$idContrat,
			$telephone,
			$email,
			addslashes($statutDemandeur),
			$motifrdv,
			$daterdveff,
			$daterdveff,
			$idTblBureau,
			$idvilleGestionnaire,
			$idGestionnaire,
			'2',
			$lieuResidence,
			date('Y-m-d à H:i:s'),
			intval($_SESSION["id"]),
			'1'
		);

		$tab = $this->_Database->Update($sqlQuery, $parametreInsert);
		$this->Logger->Handler(__function__, 'insertion de la table rdv du contrat ' . json_encode($idContrat) . '.  pour ' .  ': ' . json_encode($tab));
		return $tab;
	}


	function genererCodeRDV($prefix = "RDV", $longueur = 6)
	{
		$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$code = '';

		for ($i = 0; $i < $longueur; $i++) {
			$code .= $caracteres[random_int(0, strlen($caracteres) - 1)];
		}

		return $prefix . '-' . $code;
	}
}
