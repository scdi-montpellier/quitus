<?php
	// die("Service actuellement indisponible. Merci de revenir plus tard.");
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");

	if (substr(strtolower(get_full_url()),0,5)!="https")
		 header("location: https://".substr(strtolower(get_full_url()),7));
	
	require_once($gCheminSSP.'lib/_autoload.php');
	
	// on force la deconnexion simplesamlphp (cas improbable ou on change d'IDP sans fermer le navigateur)
	$saml_auth = new \SimpleSAML\Auth\Simple($gNomSP);
	
	if ($saml_auth->isAuthenticated())
	{
		// logout de la session Shibboleth simplesamlphp
		//$saml_auth->logout(); // ne fonctionne pas sur IDP Unimes et IDP ENSCM... erreur de sécurité
		// alors suuprime la session manuellement
		setcookie (session_id(), "", time() - 3600);
		session_destroy();
		session_write_close();
	}
	
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
	
	<script>
	var eppnc='';
	</script>
	
	</head>
	
  <body>
    <!-- Begin page content -->
    <?php
	echo "<main role='main' class='container'>";
      
		echo "<h1 style='margin-top:0px;margin-bottom:15px' class=\"mt-4\">Faire une demande de $gNomAppli</h1>";
		$btn_soumettre_disable="";
		$input_readonly = "";
		
		$eppn = "";
		$eppnc = "";
		$dvc = "";
		$dv = 0;
		$datenaissance = "";
		
	    echo "<div class=\"row\" id=\"identification\">";
		
		if (isset($_GET["dvc"]))
		{
			$dvc = $_GET["dvc"];
			
			if ($dvc!="" && strlen($dvc) > 3)
			{
				$dvctmp = urlsafe_b64decode($dvc);
				
				if ($dvctmp!="")
					$dv = decryptText($dvctmp,$gKey);
				
				if (strlen($dv)<5)
					$dv=0;
				elseif (!validateDate($dv,"U"))
					$dv=0;
			}
		}
		
		if (!isset($_GET["eppnc"]))
		{
			echo "<div class= \"col-sm\" id=\"ent\">";
			echo "<p style='margin-top: 3px;margin-bottom:26px;' class='lead'><i class='fa fa-university' aria-hidden='true'></i> <b>Accès via compte ENT</b></p>";
			
			// affichage des boutons de connexions aux IDP
			foreach ($gIdp as $key => $value){
				if ($value['active'])
				{
					echo "<a style='margin-bottom:20px;min-height:40px;".$value['buttoncolor']."' class='btn-block btn btn-dark ttt' data-toggle='tooltip' data-container='body' data-placement='top' data-html='true' title=\"Accéder à l'application via votre compte ENT ".$value['buttontext']."\" href='".$value['url']."'><i class='fa fa-sign-in fa-fw'></i> ".$value['buttontext']."</a>\n";
				}
			}

			echo "</div>";
		}
		else 
		{
			$eppnc=trim($_GET["eppnc"]);
			
			if (intval(strtotime("now"))-30<=$dv)
			{
				if ($eppnc!="" && strlen($eppnc) > 3)
				{
					$eppntmp = urlsafe_b64decode($eppnc);
					
					if ($eppntmp!="")
						$eppn = decryptText($eppntmp,$gKey);

					// on verifie que l'eppn est bien sous la forme d'un mail
					if (filter_var($eppn, FILTER_VALIDATE_EMAIL) && strlen($eppn) > 3 )
					{					
						// Tout est OK

						$btn_soumettre_disable = "disabled";
						$input_readonly = "readonly disabled";
						
						//variable JS pour checker la date de naissance dans alma au démarrage
						echo "<script>";
						echo "var eppnc='$eppnc';";
						echo "</script>";
						
					}
					else
					{   // on ne doit jamais passer ici
						echo "<div class=\"col-sm\" id=\"ent\">";
							echo "<div class='alert alert-danger' role='alert'>";
							echo "<i class='fa fa-times-circle'></i> Impossible de lire les informations du compte lecteur via votre identifiant ENT, le paramètre eppnc crypté n'est pas correct.";
							echo "</div>";
						echo "</div>";
						$eppn="";
						$eppnc="null";
					}
					
				}
				else
				{   // on ne doit jamais passer ici
					echo "<div class=\"col-sm\" id=\"ent\">";
						echo "<div class='alert alert-danger' role='alert'>";
						echo "<i class='fa fa-times-circle'></i> Impossible de lire les informations du compte lecteur via votre identifiant ENT, le paramètre eppnc est vide ou incorrect.";
						echo "</div>";
					echo "</div>";
					$eppn="";
					$eppnc="null";
				}
			}
			else
			{
				echo "<div class=\"col-sm\" id=\"ent\">";
					echo "<div class='alert alert-danger' role='alert'>";
					echo "<i class='fa fa-times-circle'></i> Impossible de se connecter : délai de connexion via ENT université dépassé.<hr>";
					echo "<i class='fa fa-info-circle'></i> Veuillez vous reconnecter via la <a href='$gURLs' title='Accueil' alt='Accueil' >page d'accueil</a>.";
					echo "</div>";
				echo "</div>";
				$eppn="";
				$eppnc="null";
			}
		}
		
		if ($eppnc=="")
		{ 
			// Mode carte lecteur, on affiche les 2 champs uid et date naissance
			echo "<div style='margin-left:15px;border-left:1px solid #e5e5e5;' class='d-none d-sm-block d-sm-none'>";
			echo "<div id='ou1' style='position: relative;top:44%;left:-15px;' class='badge badge-secondary'>OU</div>";
			echo "</div>";
			
			echo "<div class='col-sm' id='carte'>";
			echo "<div id='ou2' style='padding-top:5px' class='d-block d-sm-none'><hr><div style='position: relative;left:48%;top:-30px;' class='badge badge-secondary'>OU</div></div>";
			
			echo "<p style='margin-top:0px;margin-bottom:0px;' class='lead'><i class='fa fa-id-card-o' aria-hidden='true'></i> <b>Accès via votre carte étudiant</b></p>";
			
			echo "<p style='margin-bottom:0px;' class=\"lead\">Votre identifiant compte carte lecteur $gNominstitutionelcourt <i class='fa fa-info-circle' aria-hidden='true' data-toggle=\"tooltip\" data-placement=\"top\" title=\"Votre identifiant est inscrit sous le code barre de votre carte étudiant\"></i>&nbsp;:</p>";
									
			echo "<div class=\"input-group mb-1 w-100\">";
			   echo "<div class=\"input-group-prepend\">";
				 echo "<span class=\"input-group-text\"><i class='fa fa-key' aria-hidden='true'></i></span>";
			   echo "</div>";
			   echo "<input type=\"text\" class=\"form-control\" id=\"uid\" name=\"uid\" placeholder=\"Votre identifiant compte carte lecteur $gNominstitutionelcourt\" value=\"$eppn\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Votre identifiant est inscrit sous le code barre de votre carte étudiant\" $input_readonly >";
			echo "</div>";
		
			echo "<p style='margin-bottom:0px;' class=\"lead\">Votre date de naissance <i class='fa fa-info-circle' aria-hidden='true' data-toggle=\"tooltip\" data-placement=\"top\" title=\"Votre date de naissance sous la forme jj/mm/aaaa\"></i>&nbsp;:</p>";
			echo "<div class=\"input-group mb-2 w-100\">";
			   echo "<div class=\"input-group-prepend\">";
				 echo "<span class=\"input-group-text\"><i class='fa fa-calendar' aria-hidden='true'></i></span>";
			   echo "</div>";
			   echo "<input class='form-control' maxlength='10' required pattern='[0-9]{2}/[0-9]{2}/[0-9]{4}' id='datenaissance' name='datenaissance' placeholder='jj/mm/aaaa' value='' $input_readonly >";
			echo "</div>";
			
			echo "<div id ='soumettre'>";
			if ($eppnc=="")
				echo "<button type=\"button\" style='margin-top:5px;margin-bottom:5px;' class=\"btn btn-primary w-100\" id='btn_soumettre' $btn_soumettre_disable><i class='fa fa-arrow-circle-right' aria-hidden='true'></i> Soumettre ces informations</button>";
			echo "</div>";
		}
		elseif (intval(strtotime("now"))-30<=$dv)
		{
			// mode ENT on peut masquer les 2 champs uid et date naissance
			echo "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"$eppn\">";
			echo "<input type=\"hidden\" id=\"datenaissance\" name=\"datenaissance\" value=\"\">";
		}
		// else //voir le temps ecoulé depuis identification par shibboleth
			// echo (intval(strtotime("now"))-$dv)." sec";
		
		echo "</div>";
		
	  echo "</div>";
	  
	  echo "<div id='information'></div>";
	  
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
	<script type="text/javascript" src="js/fonctions.js?202212161509"></script> 

	<?php
	echo $gScriptMatomo;
	?>
	
  </body>
</html>
