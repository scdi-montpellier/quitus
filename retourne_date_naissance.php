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
	
	$eppnc = "";
	
	if (isset($_POST["eppnc"])) 
		$eppnc=$_POST["eppnc"];
	else
		$eppnc = "";
	
	if (trim($eppnc) != "" && strlen($eppnc) > 3)
	{
		// on decrypte l'eppnc
		$eppntmp = urlsafe_b64decode($eppnc);
		$eppn = decryptText($eppntmp,$gKey);

		// if (preg_match ('/^[A-Z0-9]+$/', $eppn) == 1 && strlen($eppn) > 3 )
		if (filter_var($eppn, FILTER_VALIDATE_EMAIL) && strlen($eppn) > 3 )
		{
			$url = "$gAdrAlma/almaws/v1/users/$eppn?apikey=$gTokenAlma&lang=fr";
			
			$xml = get_xml($url);
			
			if (!$xml==false)
			{
				$datenaissancealma = get_birthdate($xml);
				
				if ($datenaissancealma!="")
				{
					if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$datenaissancealma))
					{
						// transformation date EN -> FR
						$datenaissancealma=DateTime::createFromFormat('Y-m-d', $datenaissancealma)->format('d/m/Y');				
						$reponse = "OK";
						$message = $datenaissancealma;
					}
					else
						$message .= "<i class='fa fa-times' aria-hidden='true'></i> Le compte lecteur '$eppn' comporte une date de naissance invalide. Impossible de continuer. Veuillez contacter votre bibliothèque.";
				}
				else
					$message .= "<i class='fa fa-times' aria-hidden='true'></i> Le compte lecteur '$eppn' ne comporte pas de date de naissance. Impossible de continuer. Veuillez contacter votre bibliothèque.";
			}
			else
				$message .= "<i class='fa fa-times' aria-hidden='true'></i> L'identifiant compte lecteur '$eppn' n'existe pas. Impossible de continuer. Veuillez contacter votre bibliothèque.";
		}
		else // on ne devrait jamais passer ici !
			$message .= "<i class='fa fa-times' aria-hidden='true'></i> Impossible de lire les informations du compte lecteur, votre identifiant crypté n'est pas correct.";
	}
	else // on ne devrait jamais passer ici !
		$message .= "<i class='fa fa-times' aria-hidden='true'></i> Impossible de lire les informations du compte lecteur, le paramètre eppnc est incorrect.";
	
	$array['reponse'] = $reponse;
	$array['message'] = $message;
	$array['debug'] = $debug;
	
	echo json_encode($array);
	
?>