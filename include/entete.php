<?php
// Racine du projet (adapter si besoin)
$ROOT = '/espace-validationyako';
?>

<!-- Basic Page Info -->
<meta charset="utf-8">
<title>RDV ADMIN</title>

<!-- Site favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="../vendors/images/favicon.png">
<link rel="icon" type="image/png" sizes="32x32" href="../vendors/images/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="../vendors/images/favicon.png">

<!-- Mobile Specific Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- CSS PRINCIPAUX -->
<link rel="stylesheet" href="../vendors/styles/core.css">
<link rel="stylesheet" href="../vendors/styles/icon-font.min.css">
<link rel="stylesheet" href="../vendors/styles/style.css">

<!-- DATA TABLES -->
<link rel="stylesheet" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

<!-- FORM STEPS -->
<link rel="stylesheet" href="../src/plugins/jquery-steps/jquery.steps.css">
<link rel="stylesheet" href="../vendors/styles/step-form.css">

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  gtag('config', 'UA-119386393-1');
</script>

<!-- STEPPER STYLE -->
<style>
  .steppers {
    position: relative;
    margin-bottom: 30px;
    counter-reset: step;
  }

  .steppers li {
    list-style-type: none;
    font-size: 12px;
    text-align: center;
    width: 25%;
    position: relative;
    float: left;
  }

  .steppers li:before {
    display: block;
    content: counter(step);
    counter-increment: step;
    width: 64px;
    height: 32px;
    background-color: #019875;
    line-height: 32px;
    border-radius: 32px;
    font-size: 16px;
    color: #fff;
    text-align: center;
    font-weight: 700;
    margin: 0 auto 8px auto;
  }

  .steppers li~li:after {
    content: '';
    width: 100%;
    height: 2px;
    background-color: #019875;
    position: absolute;
    left: -50%;
    top: 15px;
    z-index: -1;
  }

  .steppers li.active:after {
    background-color: #019875;
  }

  .steppers li.active~li:before,
  .steppers li.active~li:after {
    background-color: #777;
  }
</style>