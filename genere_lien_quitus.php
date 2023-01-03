<?php 

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");
	
	$reponse = "KO";
	$message = "";
	$debug = "";
	$bOK = true;
	
	if (isset($_POST["quitus"])) 
		$quituscrypt=$_POST["quitus"];
	else
		$quituscrypt = "";

	$quitus = decrypt_quitus($quituscrypt,$gKey);
		
	if (count($quitus)!=3) {
		$bOK = false;
		$erreur = "nombre de paramètres incorrect (".count($quitus)."/3)";
	}
		
	if ($quitus[0] == ""){
		$bOK = false;
		$erreur = "uid vide";
	}
	else
		$uid = $quitus[0];
	
	if (strlen($uid) <= 3)
	{
		$bOK = false;
		$erreur .= "l'identifiant carte compte lecteur est trop court";
	}
	
	if (!preg_match ('/^[a-zA-Z0-9]+$/', $uid) && !filter_var($uid, FILTER_VALIDATE_EMAIL))
	{
		$bOK = false;
		$erreur .= "l'identifiant de la carte compte lecteur est incorrect";
	}
	
	if ($quitus[1] == ""){
		$bOK = false;
		$erreur = "date naissance vide";
	}
	
	if (!validateDate($quitus[1]." 00:00:00")){
		$bOK = false;
		$erreur = "date naissance invalide";
	}
	else
		$datenaissance = $quitus[1];
	
	if ($quitus[2] == ""){
		$bOK = false;
		$erreur = "date limite vide";
	}
	
	if (!validateDate($quitus[2])){
		$bOK = false;
		$erreur = "date limite invalide";
	}
	else
		$datelimite = $quitus[2];
	
	if (strtotime("now")>strtotime($datelimite)) {
		$bOK = false;
		$erreur = "date limite dépassée";
	}
	
	if($bOK) {

		$url = "$gAdrAlma/almaws/v1/users/$uid?apikey=$gTokenAlma&lang=fr";

		$xml = get_xml($url);
		
		if (!$xml==false)
		{
			$datenaissancealma = get_birthdate($xml);
			
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
					$validation = get_validation_for_quitus($xml,$xml_loans,$xml_fees);
					
					if ($validation!="")
						$bOK = false;
					
					if ($bOK)
					{
						$reponse = "OK";
						$message .= "<div class='alert alert-success' role='alert'>";
						$message .= "<i class='fa fa-check-square' aria-hidden='true'></i> Pour récupérer votre quitus au format PDF, cliquez sur : ";
						$message .= "<i class='fa fa-file-pdf-o text-danger' aria-hidden='true'></i>&nbsp;<a target ='_blank' href='quitus-$quituscrypt'>Télécharger votre quitus</a><br/>";
						$message .= "</div>";
					}
					else
						$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : $validation Impossible de continuer.";
				}
				else
					$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : lecture du compte lecteur impossible";
			}
			else // on ne devrait jamais passer par là
				$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : la date de naissance ne correspond pas au compte $uid.";
		}
		else
			$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : impossible de lire les informations, votre identifiant n'est pas correct.";
	}
	else
		$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : $erreur.";
	
	$array['reponse'] = $reponse;
	$array['message'] = $message;
	$array['debug'] = $debug;
	
	echo json_encode($array);
	
?>