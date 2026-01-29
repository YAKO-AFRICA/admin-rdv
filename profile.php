<?php

session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");


$id = GetParameter::FromArray($_SESSION, 'id');
$plus = " AND id='" . trim($id) . "'";
$retourUsers = $fonction->_GetUsers($plus);
if ($retourUsers != NULL) {

	if (strtoupper($retourUsers->genre) == "F") $images = "images-users-2.jpg";
	else $images = "images-users-3.jpg";
} else header("Location:deconnexion.php");

?>

<!DOCTYPE html>
<html>

<head>
	<?php include "include/entete.php"; ?>
</head>

<body>

	<?php include "include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>MON PROFIL</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">Mon Profil</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
			</div>

			<div class="row clearfix">

				<div class="col-lg-5 col-md-8 col-sm-12 mb-30">
					<div class="da-card">

						<div class="da-card-photo">
							<img src="vendors/images/<?= $images ?>" alt="">
							<div class="da-overlay">
								<div class="da-social">
									<ul class="clearfix">
										<li><?= $retourUsers->userConnect ?></li><br>
									</ul>
								</div>
							</div>
						</div>
						<div class="da-card-content">
							<h5 class="h5 mb-10"><?= $retourUsers->userConnect ?></h5>
							<p class="mb-0">
							<ul id="accordion-menu">
								<li class="dropdown">
									Telephone : <span class="mtext"> <?= $retourUsers->telephone ?></span>
								</li>
								<li>
									Email : <span class="mtext"> <?= $retourUsers->email ?></span>
								</li>
								<li>
									Type Compte : <span class="mtext"> <?= $retourUsers->typeCompte ?></span>
								</li>
								<li>
									Adresse : <span class="mtext"> <?= $retourUsers->pays ?> - <?= $retourUsers->pays ?></span>
								</li>

							</ul>
							</p>
						</div>
					</div>
				</div>
				<div class="col-lg-7 col-md-9 col-sm-12 mb-30">

					<div class="card-box height-100-p overflow-hidden">
						<div class="card-body p-2">
							<h2 class="text-center" style="color:#F9B233; font-size:26px; font-weight:bold;"> Veuillez renseigner vos informations svp !!</h2>
						</div>
						<hr>
						<div class="card-body p-2">
							<button type="button" id="modifierPasse" name="modifierPasse" class="btn btn-secondary btn-lg btn-block">Je modifie mon mot de passe</button>
							<button type="button" id="modifierInfos" name="modifierInfos" class="btn btn-success btn-lg btn-block">Je modifie mes informations personnelles</button>

						</div>
					</div>
				</div>

			</div>
			<div class="footer-wrap pd-20 mb-20">
				<?php include "include/footer.php";    ?>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modifierMesInformations" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-header" style="background-color: #033f1f !important;">
					<h5 style="color:white !important;">Modifier mes informations </h5>
				</div>
				<div class="modal-body">
					<div class="col-12 col-lg-12 col-xl-12 d-flex">
						<div class="card">
							<div class="card-body radius-12 w-100">
								<div class="row">
									<div class="card-body p-2">
										<h4 class="text-center" style="color:#F9B233; font-size:16px; font-weight:bold;"> Veuillez renseigner vos informations svp !!</h4>
									</div>
									<hr>
									<div class="form-group col-sm-12 col-md-12">
										<label for="nomRdv" style="color: #000000;">Nom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="nom" name="nom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez votre nom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-12">
										<label for="prenom" style="color: #000000;"> Prenom(s) <bold style="color: #F9B233;">*</bold></label>
										<input type="text" id="prenoms" name="prenoms" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez vos Prénom(s)" value="" class="form-control">
										<div class="validation" id="validPrenom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-12">
										<label for="email" style="color: #000000;"> Email <bold style="color: #F9B233;"> * </label>
										<input type="text" id="email" name="email" placeholder="Entrez votre Email" value="" class="form-control">
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="mobile" style="color: #000000;"> Mobile 1 <bold style="color:   #F9B233;"> * </label>
										<input type="text" id="telephone" name="telephone" data-rule="required" required placeholder="Entrez votre numéro de telephone mobile" value="" class="form-control">
										<div class="validation" id="validmobile" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="mobile2" style="color: #000000;"> Mobile 2</label>
										<input type="text" id="mobile2" name="mobile2" data-rule="required" placeholder="Entrez votre numéro de telephone portable 2" class="form-control">
										<div class="validation" id="validtelMobile2" style="color:red"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="traitAll" id="traitAll" class="btn btn-warning" onclick="getModifierMesInfos()">Modifier mes informations</button>
					<button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-body text-center">
					<div class="card-body">
						<p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
					</div>
				</div>
				<div class="modal-footer" id="closeEchec">
					<!--button type="submit" name="traitGen" id="traitGen" class="btn btn-success" style="background: #033f1f !important;">OK</button-->
					<button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
				</div>
			</div>
		</div>
	</div>

	<!-- js -->
	<script src="vendors/scripts/core.js"></script>
	<script src="vendors/scripts/script.min.js"></script>
	<script src="vendors/scripts/process.js"></script>
	<script src="vendors/scripts/layout-settings.js"></script>
	<script src="src/plugins/cropperjs/dist/cropper.js"></script>
	<script>
		$("#closeEchec").click(function(evt) {
			$('#notificationValidation').modal('hide')
			location.reload();
		})

		$("#modifierPasse").click(function(evt) {
			location.href = "reset-password";
		})

		$("#modifierInfos").click(function(evt) {

			let idusers = "<?php echo $_SESSION['id']; ?>";

			$.ajax({
				url: "config/routes.php",
				data: {
					idusers: idusers,
					etat: "checkUsers"
				},
				dataType: "json",
				method: "post",
				//async: false,
				success: function(response, status) {

					let a_afficher = ""
					if (response != '-1') {

						$("#nom").val(response.nom)
						$("#prenoms").val(response.prenom)
						$("#telephone").val(response.telephone)
						$("#email").val(response.email)

						$('#modifierMesInformations').modal("show")

					} else {
						a_afficher = `
						<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
						</div>`
						$("#msgEchec").html(a_afficher)
						$('#notificationValidation').modal("show")
					}

				},
				error: function(response, status, etat) {
					console.log(etat, response)
				}
			})

		})


		function getModifierMesInfos() {

			let idusers = "<?php echo $_SESSION['id']; ?>";

			var nom = document.getElementById("nom").value;
			var prenoms = document.getElementById("prenoms").value;
			var telephone = document.getElementById("telephone").value;

			var email = document.getElementById("email").value;
			var mobile2 = document.getElementById("mobile2").value;

			if (nom == "") {

				notification = "veuillez renseigner votre nom SVP !!"
				alert("veuillez renseigner votre nom SVP !!");
				document.getElementById("nom").focus();

			} else {
				if (prenoms == "") {
					alert("veuillez renseigner vos prenoms SVP !!");
					document.getElementById("prenoms").focus();
					return false;
				} else {
					if (email == "") {
						alert("veuillez renseigner votre email SVP !!");
						document.getElementById("email").focus();
						return false;
					} else {
						if (checkEmail(email)) {
							alert("Veuillez renseigner votre adresse email  SVP !!");
							document.getElementById("email").focus();
							return false;
						} else {
							if (telephone == "" || telephone.length < '10') {
								alert("Veuillez renseigner votre numero de  telephone  SVP !!");
								document.getElementById("telephone").focus();
								return false;
							} else {

								//console.log(nom, prenoms, telephone, email, mobile2)

								$('#modifierMesInformations').modal("hide")

								$.ajax({
									url: "config/routes.php",
									data: {
										idusers: idusers,
										nom: nom,
										prenoms: prenoms,
										telephone: telephone,
										email: email,
										mobile2: mobile2,
										etat: "ModifierMesInfos"
									},
									dataType: "json",
									method: "post",
									//async: false,
									success: function(response, status) {


										let a_afficher = ""
										if (response != '-1') {

											a_afficher = `<div class="alert alert-success" role="alert">
														<h2>Cher(e) <span class="text-success">` + nom + " " + prenoms + `</span> vos informations ont bien a bien été modifiée  !</h2></div>`


										} else {
											a_afficher = `
												<div class="alert alert-danger" role="alert">
														<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
												</div>`

										}
										$("#msgEchec").html(a_afficher)
										$('#notificationValidation').modal("show")

									},
									error: function(response, status, etat) {
										console.log(etat, response)
									}
								})
							}
						}
					}

				}

			}


		}


		function checkEmail(email) {
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}
	</script>
</body>

</html>