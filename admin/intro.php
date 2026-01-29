<?php
session_start();

// Vérification session
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// Autoload
include("../autoload.php");

// Rôles autorisés
$rolesAutorises = ['prestation', 'rdv', 'gestionnaire', 'admin', 'superadmin', 'sinistre'];

// Configuration par rôle : route + label + icône + couleur
$rolesConfig = [
    'prestation' => [
        'label' => 'Prestation',
        'route' => 'prestation/intro.php',
        'icon'  => '../assets/img/prestation.png',
        'color' => '#0d6efd'
    ],
    'rdv' => [
        'label' => 'Rendez-vous',
        'route' => 'rdv.php',
        'icon'  => '../assets/img/rdv.png',
        'color' => '#198754'
    ],
    'gestionnaire' => [
        'label' => 'Gestionnaire',
        'route' => 'gestionnaire.php',
        'icon'  => '../assets/img/gestionnaire.png',
        'color' => '#6f42c1'
    ],
    'admin' => [
        'label' => 'Administration',
        'route' => 'admin.php',
        'icon'  => '../assets/img/admin.png',
        'color' => '#dc3545'
    ],
    'superadmin' => [
        'label' => 'Super Admin',
        'route' => 'superadmin.php',
        'icon'  => '../assets/img/superadmin.png',
        'color' => '#212529'
    ],
    'sinistre' => [
        'label' => 'Sinistre',
        'route' => 'sinistre.php',
        'icon'  => '../assets/img/sinistre.png',
        'color' => '#fd7e14'
    ],
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include "../include/entete.php"; ?>
    <style>
        /* Dashboard cards */
        .role-card-link {
            text-decoration: none;
        }

        .role-card {
            border-radius: 10px;
            padding: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        .role-icon {
            width: 60px;
            height: 60px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .opacity-80 {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php include "../include/header.php"; ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Tableau de bord</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="intro.php">Tableau de bord</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Cartes des rôles -->
                <div class="row">
                    <?php foreach ($rolesAutorises as $role): ?>
                        <?php if (isset($rolesConfig[$role])): 
                            $cfg = $rolesConfig[$role];
                        ?>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                                <a href="<?= htmlspecialchars($cfg['route']) ?>" class="role-card-link">
                                    <div class="card-box widget-style1 role-card" style="background-color: <?= $cfg['color'] ?>;">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($cfg['icon']) ?>"
                                                 alt="<?= htmlspecialchars($cfg['label']) ?>"
                                                 class="role-icon">
                                            <div class="widget-data ml-3">
                                                <div class="h4 mb-1 text-white">
                                                    <?= htmlspecialchars($cfg['label']) ?>
                                                </div>
                                                <div class="font-14 text-white opacity-80">
                                                    Accéder au module
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
