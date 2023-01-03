<?php
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");

	if (substr(strtolower(get_full_url()),0,5)!="https")
		 header("location: https://".substr(strtolower(get_full_url()),7));

	echo "<!DOCTYPE html>\n";
	echo "<html lang=\"fr\">\n";
	echo "  <head>\n";
	echo "  <meta charset=\"UTF-8\">\n";
	echo "  <title>$gNomAppli</title>\n";
	echo "	<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
	echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
	echo "	<meta name=\"description\" content=\"$gNomAppli\">\n";
	echo "  <meta name=\"author\" content=\"SCDI de Montpellier\">\n";
	echo "  <meta name=\"author\" content=\"Sébastien Leyreloup\">\n";
	echo "  <link rel=\"shortcut icon\" href=\"$gURLfavicon\">\n";
		
	echo "	<!-- Bootstrap -->";
	echo '  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous" />';
	
	echo "	<!-- JQueryUI -->";
	echo "  <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>\n";
	
	echo "  <!-- Font-awesome -->\n";
	echo "	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' />\n";
		
	echo "	<!-- styles perso -->\n";
	echo "  <link rel=\"stylesheet\" href=\"css/sticky-footer-navbar.css\"></link>\n";
?>		
	</head>
	
  <body>
    <!-- Begin page content -->
	<?php
    echo "<main role='main' class='container'>";
     
		echo "<h1 style='margin-top:0px;margin-bottom:15px' class=\"mt-4\">Service de validation d'un $gNomAppli</h1>"; 
		
		$readonly = "readonly";
		
		if (isset($_GET["codequitus"])) 
			$codequitus=$_GET["codequitus"];
		else {
			$codequitus = "";
			$readonly = "";
		}
		
		if (isset($_GET["uid"])) 
			$uidcrypt=$_GET["uid"];
		else {
			$uidcrypt = "";
			$readonly = "";
		}
		
		$uid = "";
		if ($uidcrypt != ""){
			$uid = urlsafe_b64decode($uidcrypt);
		}
		
		if (strlen($uid) == 0){
			$readonly = "";
		}
		
		if (!preg_match ('/^[a-zA-Z0-9]+$/', $uid) && !filter_var($uid, FILTER_VALIDATE_EMAIL))
		{
			$uid = "";
			$readonly = "";
		}
		
		if (strlen($codequitus) != 13)
		{
			$codequitus = "";
			$readonly = "";
		}
		
		// afficher information lecteur
		echo "<div class='alert alert-warning' role='alert'>";
		
		if ($readonly == "")
			echo "<div><i class='fa fa-info-circle' aria-hidden='true'></i> Pour vérifier la validité d'un quitus, veuillez saisir les informations suivantes (présentes en bas du $gNomAppli papier ou pdf) et cliquer sur <b>Vérifier</b></div>";
		else
			echo "<div><i class='fa fa-info-circle' aria-hidden='true'></i> Pour vérifier la validité du quitus, veuillez cliquer sur <b>Vérifier</b></div>";
		
		echo "</div>";
		
		echo "<p class='lead'>Identifiant compte carte lecteur :</p>";
		echo "<div class='input-group mb-3 w-100'>";
		echo "  <div class='input-group-prepend'>";
		echo "	<span class='input-group-text'><i class='fa fa-id-card-o' aria-hidden='true'></i></span>";
		echo "  </div>";
		echo "  <input type='text' maxlength=250 class='form-control' id='uid' name='uid' placeholder='Identifiant compte carte lecteur indiqué sur le $gNomAppli papier ou pdf' value='$uid' $readonly>";
		echo "</div>";
		
		echo "<p class='lead'>Code de validation :</p>";
		echo "<div class='input-group mb-3 w-100'>";
		echo "  <div class='input-group-prepend'>";
		echo "	<span class='input-group-text'><i class='fa fa-lock' aria-hidden='true'></i></span>";
		echo "  </div>";
		echo "  <input type='text' maxlength=13 class='form-control' id='codequitus' name='codequitus' placeholder='Code de validation indiqué sur le $gNomAppli papier ou pdf' value='$codequitus' $readonly>";
		echo "</div>";
		
		echo "<div class='custom-control custom-checkbox mr-sm-2 mb-3'>";
		echo "	<span id='verifier' name='verifier'><input type='checkbox' class='custom-control-input' id='btn_verifier' name='btn_verifier' >";
		echo "	<label class=\"custom-control-label\" for=\"btn_verifier\" id='label_verifier' name='label_verifier'> <b>Vérifier</b></label></span>";
		echo "</div>";
		
		echo "<div id='information_quitus'></div>";
		
		include ('pied_page_aide.php');		

	echo "</main>";

    include ('pied_page.php');
	?>
	
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
	
	<!-- jQueryUI -->
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	<!-- Popper -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
	
	<!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>
	
	<!-- Fonctions perso -->
	<script type="text/javascript" src="js/fonctions.js?1002"></script>

	<?php
	echo $gScriptMatomo;
	?>

  </body>
</html>
