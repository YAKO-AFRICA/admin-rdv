<?php

/**********************************************************
 * MENU BAR SÉCURISÉ – PAR RÔLE
 **********************************************************/

// Sécurité : accès uniquement via header.php
if (!isset($_SESSION['id'], $_SESSION['typeCompte'])) {
    header("Location: ../index.php");
    exit;
}

$typeCompte = strtolower($_SESSION['typeCompte']);
$paramCompte = strtolower($_SESSION['paramCompte']);
$tablo = explode("|", $paramCompte);
//$this->paramCompte = trim($this->id . "|" . $this->service . "|" . $this->typeCompte . "|" . $this->profil . "|" . $this->cible . "|" . $this->codeagent."|" . $this->userConnect);
$service = $tablo[1];
$profil = $tablo[3];
$cible = $tablo[4];

#print_r($tablo); exit;

?>

<!-- MENU LATERAL -->

<div class="left-side-bar">
    <div class="brand-logo">
        <a href="<?= Config::URL_YAKO ?>">
            <img src="../vendors/images/logo.png" alt="" class="dark-logo">
        </a>
    </div>

    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu" style="font-size: small;">

                <!-- MENU COMMUN -->
                <li>
                    <a href="intro.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Tableau de bord</span>
                    </a>
                </li>

                <!-- MENU PRESTATION -->
                <?php if ($service === 'prestation'): ?>
                    <li>
                        <a href="liste-prestations.php" class="dropdown-toggle no-arrow">
                            <span class="micon dw dw-list"></span>
                            <span class="mtext">Prestations</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- MENU RDV -->
                <?php if ($service === 'rdv' || $service === 'gestionnaire'): ?>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <?php if ($profil === 'supervisseur'): ?>
                        <li>
                            <div class="sidebar-small-cap">Rendez-vous - Supervisseur</div>
                        </li>
                        <li class="dropdown">
                            <a href="liste-rdv?i=1" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon dw dw-edit"></span><span class="mtext">rdv<br>En attente</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="liste-rdv?i=2" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-forward "></span><span class="mtext">rdv<br>transmis</span>

                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="liste-rdv?i=3" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-check"></span><span class="mtext">rdv<br>traite(s)</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="liste-rdv?i=0" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-trash"></span><span class="mtext">rdv<br>Rejeté</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li class="dropdown">
                            <a href="rdv-exceptionnel" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-calendar"></span><span class="mtext">rdv<br>Exceptionnel</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="extraction-bordereau-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-download"></span><span class="mtext">Extraction BORDEREAU</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="charger-bordereau" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-upload"></span><span class="mtext">Charger BORDEREAU</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li class="dropdown">
                            <a href="tableau-suivi-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-area-chart"></span><span class="mtext">Tableau de suivi</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a href="liste-gestionnaires" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-user"></span>
                                <span class="mtext">Utilisateurs</span>
                            </a>
                        </li>


                    <?php elseif ($profil === 'gestionnaire' || ($profil === 'agent' || $profil === 'interim')): ?>

                        <li>
                            <div class="sidebar-small-cap">Rendez-vous - Gestionnaire</div>
                        </li>

                        <li class="dropdown">
                            <a href="rdv-gestionnaire?i=2" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon dw dw-edit"></span><span class="mtext"> Mes rdvs<br>transmis</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="rdv-gestionnaire?i=3" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-check"></span><span class="mtext">rdv<br>traite(s)</span>

                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li class="dropdown">
                            <a href="liste-calendrierJourReceptionVilles" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-calendar"></span><span class="mtext">Jour/Villes<br>Reception</span>

                            </a>
                        </li>

                    <?php endif; ?>


                    <li class="dropdown">
                        <a href="recherche-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-search"></span><span class="mtext">rechercher<br>un rdv</span>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- MENU GESTIONNAIRE -->
                <?php if ($typeCompte === 'admin'): ?>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a href="gestion-utilisateurs.php" class="dropdown-toggle no-arrow">
                            <span class="micon dw dw-user"></span>
                            <span class="mtext">Utilisateurs</span>
                        </a>
                    </li>
                   
                <?php endif; ?>

                <!-- MENU DÉCONNEXION -->
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a href="deconnexion.php" class="dropdown-toggle no-arrow text-danger">
                        <span class="micon dw dw-logout"></span>
                        <span class="mtext">Déconnexion</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>