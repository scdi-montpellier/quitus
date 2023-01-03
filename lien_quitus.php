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
	
		echo "<h1 style='margin-top:0px;margin-bottom:15px' class=\"mt-4\">Votre demande de $gNomAppli</h1>";
		
		if (isset($_GET["quitus"])) 
			$quituscrypt=$_GET["quitus"];
		else
			$quituscrypt = "";
			
		if ($quituscrypt!="")
		{		
			$bOK = true;
			$uid = "";
			$datenaissance = "";
			$datelimite = "";
			$erreur = "";
			
			$quitus = decrypt_quitus($quituscrypt,$gKey);
			
			if (count($quitus)!=3) 
			{
				$bOK = false;
				$erreur .= "- nombre de paramètres incorrect (".count($quitus)."/3)<br/>";
			}
			
			if($bOK)
			{
				if ($quitus[0] == "" && $bOK){
					$bOK = false;
					$erreur .= "- uid vide<br/>";
				}
				else
					$uid = $quitus[0];
				
				if (strlen($uid) <= 3 && $bOK)
				{
					$bOK = false;
					$erreur .= "- uid incorrect (trop court)<br/>";
				}
				
				if (!preg_match ('/^[a-zA-Z0-9]+$/', $uid) && !filter_var($uid, FILTER_VALIDATE_EMAIL) && $bOK)
				{
					$bOK = false;
					$erreur .= "- uid incorrect<br/>";
				}

				if ($quitus[1] == "" && $bOK){
					$bOK = false;
					$erreur .= "- date naissance vide<br/>";
				}
				
				if (!validateDate($quitus[1],"Y-m-d") && $bOK) {
					$bOK = false;
					$erreur .= "- date naissance invalide<br/>";
				}
				else
					$datenaissance = $quitus[1];
				
				if ($quitus[2] == "" && $bOK){
					$bOK = false;
					$erreur .= "- date limite vide<br/>";
				}
				
				if (!validateDate($quitus[2]) && $bOK){
					$bOK = false;
					$erreur .= "- date limite invalide<br/>";
				}
				else
					$datelimite = $quitus[2];
				
				if ($bOK)
				{
					// afficher information lecteur
					echo "<div class='alert alert-warning' role='alert'>";
					
						echo "<div class = 'titre2'><i class='fa fa-info-circle' aria-hidden='true'></i> Votre demande concerne :</div>";
						echo "<div>- l'identifiant carte lecteur : $uid</div>";
						echo "<div>- dont le porteur a pour date de naissance : ".date("d/m/Y",strtotime($datenaissance))."</div>";
						
						$datelimite = strtotime($datelimite);
						echo "<div class = 'titre2'><i class='fa fa-calendar-check-o' aria-hidden='true'></i> Validité de votre demande :</div>";
						echo "<div>- Vous pourrez récupérer votre quitus jusqu'au <b>".date("d/m/Y à H:i:m",$datelimite)."</b></div>";
					
					echo "</div>";
					
					//on teste si la date de validité n'est pas dépassé
					if (strtotime("now")<=$datelimite) 
					{
						echo "<div id='information'>";
							echo "<input hidden=true type='text' id='quituscrypt' name='quituscrypt' value='$quituscrypt' />";
							
							echo "<div class='alert alert-danger' role='alert'>";
								echo "<i class='fa fa-exclamation-circle' aria-hidden='true'></i> Attention une fois que vous aurez cliqué sur <b>Télécharger votre quitus</b>, votre compte lecteur $gNominstitutionelcourt sera <b>bloqué</b> et indiquera qu'un quitus est actif.<br/><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Votre quitus ne sera valide qu'une fois téléchargé depuis le lien ci-dessous.";
								
								echo "<div id='divcompris'>";
								
									echo "<div class='custom-control custom-checkbox mr-sm-2'>";
										echo "	<span id='compris' name='compris'><input type='checkbox' class='custom-control-input' id='btn_compris' name='btn_compris' >";
										echo "	<label class=\"custom-control-label\" for=\"btn_compris\" id='label_compris' name='label_compris'><b>J'AI COMPRIS !</b></label></span>";
									echo "</div>";
			  
								echo "</div>";
							echo "</div>";
							
						echo "</div>";
						
						echo "<div id='lien_quitus'>";
						echo "</div>";
					}
					else
						echo "<div class='alert alert-danger' role='alert'><b><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Erreur :</b><br/><i class='fa fa-times' aria-hidden='true'></i> La date limite de téléchargement est dépassée.<hr><i class='fa fa-info-circle' aria-hidden='true'></i> Veuillez refaire une demande de <a href='$gURLs'>quitus en ligne</a>.</div>";
				}
				else
					echo "<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Erreur :<br/>$erreur</div>";
			}
			else
				echo "<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Paramètre incorrect, impossible de continuer.</div>";
		}
		else 
			echo "<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Paramètre non présent, impossible de continuer.</div>";
	  
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
