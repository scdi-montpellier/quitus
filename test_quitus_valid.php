<?php 

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");
	
	$reponse = "KO";
	$message = "";
	$debug = "";
	
	$bOK = true;
	$erreur ="";
	
	if (isset($_POST["uid"])) 
		$uid=$_POST["uid"];
	else
		$uid = "";
	
	if (isset($_POST["codequitus"])) 
		$codequitus=strtoupper($_POST["codequitus"]);
	else
		$codequitus = "";

	if ($uid == "")
	{
		$bOK = false;
		$erreur .= "- l'identifiant de la carte compte lecteur est absent";
	}
	
	if (strlen($uid) <= 3 && $bOK)
	{
		$bOK = false;
		$erreur .= "- l'identifiant carte compte lecteur est trop court";
	}
	
	if (!preg_match ('/^[a-zA-Z0-9]+$/', $uid) && !filter_var($uid, FILTER_VALIDATE_EMAIL) && $bOK)
	{
		$bOK = false;
		$erreur .= "- l'identifiant de la carte compte lecteur est incorrect";
	}
	
	if ($codequitus == "" && $bOK){
		$bOK = false;
		$erreur .= "- le code de validation est absent ou incorrect";
	}

	if (strlen($codequitus) != 13 && $bOK){
		$bOK = false;
		$erreur .= "- le code de validation est incorrect (il doit comporter 13 chiffres et lettres)";
	}
	
	if (!preg_match ('/^[a-zA-Z0-9]+$/', $codequitus) && $bOK)
	{
		$bOK = false;
		$erreur .= "- le code de validation est incorrect (il doit comporter 13 chiffres et lettres)";
	}
	
	if($bOK) 
	{
		$url = "$gAdrAlma/almaws/v1/users/$uid?apikey=$gTokenAlma&lang=fr";

		$xml = get_xml($url);
		
		if (!$xml==false)
		{	
			$name = get_name($xml);
			$datenaissance = date("d/m/Y",strtotime(get_birthdate($xml)));
			$datejour = date("d/m/Y \à H:i:s",strtotime("now"));
			
			// on vérifie que compte à toujours un quitus et que donc le quitus est toujours valide.
			$validation = has_quitus_valide($xml,$codequitus);
			
			if ($validation=="")
				$bOK = false;
			
			if (strtoupper($validation)!=$codequitus)
				$bOK = false;
			
			if ($bOK)
			{
				$reponse = "OK";
				$message .= "<div class='alert alert-success' role='alert'>";
				$message .= "<i class='fa fa-check-square' aria-hidden='true'></i> <b>Ce compte lecteur possède un quitus valide au $datejour</b><br/>$name, carte lecteur $uid, né/née le $datenaissance a rendu tous les documents qu'il/elle avait empruntés et est quitte de toute obligation envers la bibliothèque.";
				$message .= "</div>";
			}
			else
				$message .= "<i class='fa fa-times' aria-hidden='true'></i> <b>Ce compte lecteur n'a pas de quitus valide associé au code de validation $codequitus</b><br/>L'usager doit peut-être refaire une demande de quitus ou se renseigner auprès de sa bibliothèque.";

		}
		else
			$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur : impossible de lire les informations du compte carte lecteur (identifiant incorrect).";
	}
	else 
		$message .= "<i class='fa fa-times' aria-hidden='true'></i> Erreur :<br/>".$erreur;
	
	$array['reponse'] = $reponse;
	$array['message'] = $message;
	$array['debug'] = $debug;
	
	echo json_encode($array);
	
?>