<?php 

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");
	
	require('fpdf/fpdf.php');

	class PDF extends FPDF
	{
		// En-tête
		function Header()
		{
			
			global $uid;
			global $gNominstitutionelcomplet;
			global $gEtablissement;
			global $etablissement;
			
			$decalagelogo=33;
			
			if ($etablissement!="")
			{
				if (isset($gEtablissement[$etablissement]['logo_hd']))
				{
					if (file_exists($gEtablissement[$etablissement]['logo_hd']))
					{
						$this->Image($gEtablissement[$etablissement]['logo_hd'],12,16,25);
						$decalagelogo=50;
					}
				}
			}
			
			// Police Arial gras 15
			$this->SetFont('Arial','B',15);
			// Décalage à droite
			$this->Cell(80);
			// Titre
			$this->Cell($decalagelogo,37,utf8_decode($gNominstitutionelcomplet),0,0,'C');

			// Saut de ligne
			$this->Ln(20);		
			
		}

		// Pied de page
		function Footer()
		{
			// Positionnement à 1,5 cm du bas
			$this->SetY(-15);
			// Police Arial italique 8
			$this->SetFont('Arial','I',8);
			// Numéro de page
			$this->Cell(0,12,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
		
	$message = "";
	$quituscrypt = "";
	$uid = "";
	$datelimite = "";
	
	$bOK = true;
	
	if (isset($_GET["quitus"])) 
		$quituscrypt=$_GET["quitus"];

	if ($quituscrypt == "" || strlen($quituscrypt) <= 3)
	{
		$bOK = false;
		$message = "Param&egrave;tre vide ou trop court";
	}
	
	if($bOK)
	{
		$quitus = decrypt_quitus($quituscrypt,$gKey);
			
		if (count($quitus)!=3) {
			$bOK = false;
			$message = "Nombre de param&egrave;tres incorrect (".count($quitus)."/3)";
		}
			
		if ($quitus[0] == "" && $bOK)
		{
			$bOK = false;
			$message = "uid vide";
		}
		else
			$uid = $quitus[0];
		
		if (strlen($uid) <= 3 && $bOK)
		{
			$bOK = false;
			$message .= "l'identifiant carte compte lecteur est trop court";
		}
		
		if (!preg_match ('/^[a-zA-Z0-9]+$/', $uid) && !filter_var($uid, FILTER_VALIDATE_EMAIL) && $bOK)
		{
			$bOK = false;
			$message .= "l'identifiant de la carte compte lecteur est incorrect";
		}
		
		if ($bOK)
		{
			if ($quitus[1] == ""){
				$bOK = false;
				$message = "Date naissance vide";
			}
			
			if (!validateDate($quitus[1]." 00:00:00")){
				$bOK = false;
				$message = "Date naissance invalide";
			}
			else
				$datenaissance = $quitus[1];
			
			if ($quitus[2] == ""){
				$bOK = false;
				$message = "Date limite vide";
			}
			
			if (!validateDate($quitus[2])){
				$bOK = false;
				$message = "Date limite invalide";
			}
			else
				$datelimite = $quitus[2];
			
			if (strtotime("now")>strtotime($datelimite)) {
				$bOK = false;
				$message = "date limite de t&eacute;l&eacute;chargement d&eacute;pass&eacute;e.<br/><i class='fa fa-info-circle' aria-hidden='true'></i> Veuillez refaire une demande de <a href='$gURLs'>quitus en ligne</a>";
			}
		}
		
	}
	
	if($bOK) 
	{
		$url_user = "$gAdrAlma/almaws/v1/users/$uid?apikey=$gTokenAlma&lang=fr";

		$xml_user = get_xml($url_user);
		
		if (!$xml_user==false)
		{
			$datenaissancealma = get_birthdate($xml_user);
			
			if ($datenaissance == $datenaissancealma) {
				
				$url_loans = "$gAdrAlma/almaws/v1/users/$uid/loans?user_id_type=all_unique&limit=10&offset=0&order_by=id&direction=ASC&apikey=$gTokenAlma&lang=fr";
				$xml_loans = get_xml($url_loans);
				if ($xml_loans==false)
					$bOK = false;
				
				$url_fees = "$gAdrAlma/almaws/v1/users/$uid/fees?user_id_type=all_unique&status=ACTIVE&apikey=$gTokenAlma&lang=fr";
				$xml_fees = get_xml($url_fees);
				if ($xml_loans==false)
					$bOK = false;
				
				if ($bOK)
				{
					// il faut vérifier que l'obtention du quitus est toujours possible... ça a pu changer en 24h (durée du lien)
					$validation = get_validation_for_quitus($xml_user,$xml_loans,$xml_fees);
					
					if ($validation!="")
						$bOK = false;
					
					if (!$bOK)
						$message = $validation;
					
					// on génère le quitus dans alma
					$url_upd_user = "$gAdrAlma/almaws/v1/users/{user_id}";
					
					$codequitus = strtoupper(uniqid());
					
					$quitusajout = add_quitus($xml_user, $url_upd_user, $uid, $gTokenAlma, $codequitus);

					if (!$quitusajout['OK'])
					{
						$bOK = false;
						$message = $quitusajout['message'];
					}
					else
						$codequitus = $quitusajout['codequitus'];
					
				}
				else  // on ne devrait jamais passer par là
					$message = "Erreur : lecture du compte lecteur impossible";
			}
			else // on ne devrait jamais passer par là
				$message = "La date de naissance ne correspond pas au compte $uid.";
		}
		else  // on ne devrait jamais passer par là
			$message = "Impossible de lire les informations, votre identifiant n'est pas correct.";
	}
	
	if ($bOK) 
	{
		
		$etablissement="";
		
		// on cherche l'établissement à partir de l'uid (@domaine.tld ou code-barres)
		if(filter_var($uid, FILTER_VALIDATE_EMAIL)) //@domaine.tld
		{
			
			$parts = explode('@', $uid);
			$domaine=strtolower($parts[1]); // on récupère le domaine
			if (isset($gDomaine[$domaine]))
				$etablissement=$gDomaine[$domaine];
		}
		else //code-barres
		{
			
			foreach ($gEtablissement as $key => $value){

					if (preg_match($value['cb_regex'], $uid)) 
					{
						// la regex match l'uid
						$etablissement = $key; // on met la clé qui est l'établissment
						break;
					}
					// on teste le suivant
			}
	
		}
		
		if ($etablissement=="")
			$etablissement="default";
		
		//on génère le pdf du quitus
		$pdf = new PDF();
		$pdf->SetAuthor($gNominstitutionel);
		$pdf->SetTitle("QUITUS");
		$pdf->SetSubject("$gTagblocage $codequitus");
		$pdf->AliasNbPages();
		
		$pdf->SetLeftMargin(10);
		
		$pdf->AddPage();
		$pdf->SetFont('Times','',20);
		$pdf->Cell(0,13,'',0,1,'C');
		$pdf->Cell(0,50,'QUITUS ',0,1,'C');
		$pdf->SetFont('Times','',12);
		
		// ajout : "Ville", le "date"
		if ($etablissement!="")
		{
			if (isset($gEtablissement[$etablissement]['ville']))
			{
				$pdf->Cell(0,30,utf8_decode($gEtablissement[$etablissement]['ville']).", le ".date("d/m/Y"),0,1,'L');
			}
		}
		else // pas de Ville définit dans config.php
		{
			$pdf->Cell(0,30,"Le ".date("d/m/Y"),0,1,'L');
		}
		
		$pdf->MultiCell(180,8,ucfirst(utf8_decode($gLNominstitutionelcomplet))." certifie que M./Mme ".utf8_decode(get_name($xml_user)).", identifiant $uid, né/née le ".date("d/m/Y",strtotime(get_birthdate($xml_user))).", a rendu tous les documents qu'il/elle y avait empruntés et est quitte de toute obligation envers la bibliothèque.");
		
		$pdf->Cell(0,40,ucfirst(utf8_decode($gLNominstitutionel)),0,1,'C');
		
		// ajout : tampon
		if ($etablissement!="")
		{
			if (isset($gEtablissement[$etablissement]['tampon']))
			{
				if (file_exists($gEtablissement[$etablissement]['tampon']))
				{
					$pdf->Image($gEtablissement[$etablissement]['tampon'],140,160,35);
					$decalagelogo=50;
				}
			}
		}
		
		$pdf->Cell(0,28,'',0,1,'C');

		$codevalidation = "v-".$codequitus."-".urlsafe_b64encode($uid);

		$pngAbsoluteFilePath = "temp/".uniqid('',true).".png";

		$urlvalidation = $gURLs.$codevalidation;
		
		// création du QRCode
		include ("genere_qrcode.php");
		
		$pdf->Image($pngAbsoluteFilePath,9,227,30);
		
		$pdf->Cell(0,0,"--------------------------------------- Réservé à l'université de destination ---------------------------------------",0,1,'C');
		// $pdf->Cell(0,4,"",0,1,'C');
		
		// $pdf->Cell(0,14,'Pour vérifier la validité de ce quitus sur le site du '.utf8_decode($gNominstitutionelcomplet).', flasher le QR Code suivant :',0,1);
		$pdf->Cell(0,14,'Pour vérifier la validité de ce quitus, flasher le QR Code suivant :',0,1);
		// $pdf->MultiCell(0,6,'Pour vérifier la validité de ce quitus, flasher le QR Code suivant :');
		
		$pdf->Cell(0,30,'',0,1,'C');
		
		$pdf->Cell(100,5,'Vous pouvez aussi cliquer sur le lien : ',0,1);

		$pdf->SetTextColor(0,0,255);
		$pdf->SetFont('','U');

		$pdf->Cell(200,6,$gURLs."validation",0,1,'L',false,$urlvalidation);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('','');
		$pdf->SetFont('Times','',10);
		
		$pdf->Cell(200,6,"(Identifiant compte carte lecteur : $uid / Code validation : $codequitus)",0,1,'L',false);

		$pdf->Output();
		
		// suppression du QRCode temporaire
		unlink($pngAbsoluteFilePath);
		
		// on met à jour la demande dans la table (nombre téléchargment)
		$dbh = new PDO('mysql:host='.$gaSql['server'].';dbname='.$gaSql['db'], $gaSql['user'], $gaSql['password']);
		// la demande quitus doit exister... (insert à l'envoie du mail)
		$sql = "UPDATE demande_quitus SET code_validation=?, date_telechargement=?, nb_telechargement=nb_telechargement + 1 WHERE uid=? AND date_limite=?";
		$dbh->prepare($sql)->execute([$codequitus, date("Y-m-d H:i:s"), $uid, $datelimite]);
		
		$dbh = null;
	}
	else 
	{
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

		echo "	<!-- Bootstrap -->";
		echo "  <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>\n";
		
		echo "  <!-- Font-awesome -->\n";
		echo "	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' />\n";
			
		echo "	<!-- styles perso -->\n";
		echo "  <link rel=\"stylesheet\" href=\"css/sticky-footer-navbar.css\"></link>\n";
			
		echo "  </head>\n";
		
		echo "  <body>\n";
		
		echo "  <!-- Begin page content -->";
		echo "<main role='main' class='container'>";
		
		echo "<h1 style='margin-top:0px;margin-bottom:15px' class=\"mt-4\">G&eacute;n&eacute;ration du quitus</h1>";
		
		if (trim($message)=="")
			$message = "Erreur inconnue pendant la création du quitus, veuillez contacter votre Bibliothèque.";
		else
			$message = "Erreur : $message";
		
		echo "		<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> $message</div>";
		
		if ($uid !="" && $datelimite != "")
		{
			// on met à jour la demande dans la table (log erreur)
			$dbh = new PDO('mysql:host='.$gaSql['server'].';dbname='.$gaSql['db'], $gaSql['user'], $gaSql['password']);
			// la demande quitus doit exister... (insert à l'envoie du mail)
			$sql = "UPDATE demande_quitus SET erreur=? WHERE uid=? AND date_limite=?";
			$dbh->prepare($sql)->execute([strip_tags(utf8_decode(html_entity_decode($message))), $uid, $datelimite]);
		}
		
		include ('pied_page_aide.php');	
		
		echo "</main>";
		
		include ('pied_page.php');
		
		echo '  <!-- jQuery -->';
		echo '  <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>';
		
		echo '  <!-- jQueryUI -->';
		echo '  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
		
		echo '  <!-- Popper -->';
		echo '  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>';
		
		echo '  <!-- Bootstrap -->';
		echo '  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>';
			
		echo '  <!-- Fonctions perso -->';
		echo '  <script type="text/javascript" src="js/fonctions.js?1001"></script> ';
		
		echo $gScriptMatomo;

		echo "  </body>";
		echo "</html>";
	}
	
?>