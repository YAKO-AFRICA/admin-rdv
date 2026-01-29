<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>VALIDATION SERVICE CLIENT YAKO </title>
    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon.png">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css">
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
</head>

<body class="login-page">
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <a href="">
                    <img src="vendors/images/logo-icon.png" width="180" alt="">
                </a>
            </div>
            <div class="login-menu">
                <h5>
                    <span id="dateheure"></span>
                </h5>
            </div>
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container" style="background-color: #033f1f;">

            <div class="row align-items-center">

                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center" style="color:#033f1f;  font-weight:bold; ">Connexion</h2>
                        </div>
                        <div class="input-group custom">
                            <input type="text" class="form-control form-control-lg" id="login" placeholder="Entrez votre login">

                        </div>
                        <div class="input-group custom">
                            <input type="password" class="form-control form-control-lg" id="passW" placeholder="Entrez votre mot de passe">

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 text-left">
                                <bold style="color: #F9B233; font-weight: bold;font-size: 9px;"> *</bold>
                                <span style="color:#033f1f; font-weight: bold;font-size: 12px;"> vous n'avez oublie votre mot de passe </span>
                                <a class="btn" style="color:#F9B233 !important;font-weight: bold; font-size: 11px;" type="submit" name="passOublie" id="passOublie">cliquez ici</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="input-group mb-0">
                                    <button id="connexion" name="connexion" class="btn btn-success btn-lg btn-block " style="background: #033f1f !important;">connexion</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <img src="vendors/images/images-2.jpg" alt="">
                </div>

            </div>
        </div>
    </div>
    <!------POP UP NOTIFICATION -->
    <div class="modal fade" id="error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="form-group">
                        <h2>
                            <span id="a_afficher2"></span>
                        </h2>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="annuler">
                        <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal  hide fade in" data-backdrop="static" id="PassOublieModale" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-header" style="background:#033f1f; color: #fff; font-weight:bold;"> Identification Agent pour mot de passe oublie - PLATEFORME VALIDATION YAKO AFRICA ASSURANCES VIE</div>
                <div class="modal-body ">
                    <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;"> Identification agent </h4>
                    <hr>
                    <h6 class="text-center p-2" style="color:red ; font-weight:bold;"> Veuillez renseigner le formulaire ci-dessous : </h6>
                    <div class="row">

                        <div class="form-group col-sm-12 col-md-7">
                            <label for="nomRdv">Veuillez renseigner votre Login / nom d'utilisateur <bold style="color: #F9B233;"> *</label>
                            <input type="text" id="loginPO" name="loginPO" data-rule="required" required placeholder="Entrez votre Login / nom d'utilisateur" class="form-control">
                        </div>

                        <div class="form-group col-sm-12 col-md-5">
                            <label for="nomRdv">Veuillez renseigner votre email <bold style="color: #F9B233;"> *</label>
                            <input type="text" id="email" name="email" data-rule="required" required placeholder="Entrez votre email" class="form-control">
                        </div>

                        <small class="text-danger" id="notif_n_mdp"></small>
                    </div>

                    <div class="modal-footer">
                        <div id="closeNotif">
                            <button type="button" id="closeNotif" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                            <button type="submit" class="btn btn-warning text-white" name="verifierPass" id="verifierPass" style="background: #F9B233">SOUMETTRE LA DEMANDE </button>
                            <span id="lib2"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>

    <script>
        function pause(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        async function afficherDate() {
            while (true) {
                await pause(1000);
                var cejour = new Date();
                var options = {
                    weekday: "long",
                    year: "numeric",
                    month: "long",
                    day: "2-digit"
                };
                var date = cejour.toLocaleDateString("fr-FR", options);
                var heure = ("0" + cejour.getHours()).slice(-2) + ":" + ("0" + cejour.getMinutes()).slice(-2) + ":" + ("0" + cejour.getSeconds()).slice(-2);
                var dateheure = date + " " + heure;
                var dateheure = dateheure.replace(/(^\w{1})|(\s+\w{1})/g, lettre => lettre.toUpperCase());
                document.getElementById('dateheure').innerHTML = dateheure;
            }
        }
        afficherDate();


        $("#passOublie").click(function(evt) {
            //location.reload()
            $('#PassOublieModale').modal("show")
        })


        $("#verifierPass").click(function(evt) {

            var loginPO = document.getElementById("loginPO").value;
            var email = document.getElementById("email").value;


            if (loginPO == "") {
                alert("Veuillez renseigner votre login svp !!");
                document.getElementById("loginPO").focus();
                return false;
            } else if (email == "") {
                alert("Veuillez renseigner votre email svp !!");
                document.getElementById("email").focus();
                return false;
            } else {

                if (!checkEmail(email)) {
                    alert("Veuillez renseigner une adresse email valide svp !!");
                    document.getElementById("email").focus();
                    return false;

                }

                $('#PassOublieModale').modal("hide")
                //console.log(loginPO, email)

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        loginPO: loginPO,
                        email: email,
                        etat: "motdepasseOublie"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,

                    success: function(response, status) {

                        console.log(response)

                        etat = response
                        if (etat !== '-1') {
                            $('#PassOublieModale').modal("hide")
                            alert("Un email vient de vous etre envoye !")
                        } else {
                            var a_afficher = "Desoler le login reseigne n'existe pas , Merci de ressayer plus tard"
                            $("#a_afficher2").text(a_afficher)
                            $('#error').modal("show")
                        }
                    }
                })
            }
        })


        $("#connexion").click(function(evt) {
            var login = document.getElementById("login").value;
            var passW = document.getElementById("passW").value;



            if (login == "") {
                alert("Veuillez renseigner votre login svp !!");
                document.getElementById("login").focus();
                return false;
            } else if (passW == "") {
                alert("Veuillez renseigner votre mot de passe svp !!");
                document.getElementById("passW").focus();
                return false;
            } else {

                alert("Connexion en cours ...")
                $.ajax({
                    url: "config/routes.php",
                    data: {
                        login: login,
                        passW: passW,
                        etat: "connexion"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,

                    success: function(response, status) {

                        console.log(response)
                        if (response !== '-1') {

                            var tablo = response.split("|");
                            console.log(tablo)
                            if (tablo[1] == "gestionnaire") {
                                tablo[1] = "rdv"; 
                            }
                            location.href =  tablo[1] + "/intro";
                            // if (tablo[6] == "agent") {
                            //     location.href = tablo[1] + "/intro";
                            // } else if (tablo[6] == "gestionnaire") {
                            //     location.href = "intro";
                            // } else {
                            //     location.href = "intro";
                            // }

                        } else {
                            var a_afficher = "DESOLE LOGIN / MOT DE PASS INCORRECT , Merci de ressayer"
                            $("#a_afficher2").text(a_afficher)
                            $('#error').modal("show")
                        }
                    },
                    error: function(response, status, etat) {
                        console.log(response)
                        etat = '-1';
                        alert("Erreur Systeme , Merci de ressayer plus tard ")
                    }
                })
            }
        })


        $("#close").click(function() {
            $('#error').modal('hide')
            location.reload(true)
        })

        function connexionUser(login, passW) {
            let etat;
            return etat;
        }

        function checkEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    </script>
</body>

</html>