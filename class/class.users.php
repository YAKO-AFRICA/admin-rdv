<?php

class users
{

    ///`id`, `nom`, `prenom`, `login`, `password`, `genre`, `date`, `telephone`, `adresse`, `ville`, `pays`, `modifiele`, `image`, `typeCompte`, `etat`
    var $id = null;
    var $nom = null;
    var $prenom = null;
    var $login = null;
    var $password = null;
    var $genre = null;
    var $date = null;
    var $telephone = null;
    var $adresse = null;
    var $ville = null;

    var $pays = null;
    var $modifiele = null;

    var $image = null;
    var $typeCompte = null;
    var $etat = null;
    var $profil = null;
    var $contact = null;

    var $infos = null;
    var $identifiant = null;

    var $email = null;
    var $codeagent = null;
    var $cible = null;
    var $villes_reception = null;
    var $localisation = null;
    var $service = null;
    var $paramCompte = null;
    var $paramSession = null;
    var $userConnect = null;


    public function __construct($infos)
    {

        if ($infos != null) {

            foreach ($infos as $key => $value) {
                if ($value == null) $value = "";
                $this->{$key} = trim($value);
            }
            //$this->email = $this->login;

            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            if (preg_match($regex, $this->login)) {
                list($this->identifiant, $hhhhh) = explode('@', $this->login, 2);
            } else $this->identifiant = $this->login;


            //$this->profil =  $this->typeCompte;
            $this->userConnect = strtoupper($this->nom . " " . $this->prenom);
            $this->paramSession = trim($this->identifiant . "|" . $this->typeCompte . "|" . $this->id . "|" . $this->userConnect);
            $this->paramCompte = trim($this->id . "|" . $this->service . "|" . $this->typeCompte . "|" . $this->profil . "|" . $this->cible . "|" . $this->codeagent."|" . $this->userConnect);
            $this->infos = trim($this->paramSession . "|" . $this->telephone . "|" . $this->email . "|" . $this->profil . "|" . $this->cible . "|" . $this->codeagent);
        }
    }
}
