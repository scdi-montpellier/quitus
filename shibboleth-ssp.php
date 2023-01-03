<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	session_start();
	
	include_once("config.php");
	
	include ("fonction.php");
	
	if (substr(strtolower(get_full_url()),0,5)!="https")
		 die ("Veuillez accéder à cette page en https");
	
	// die(get_full_url());
	
	$idp="";
	if (isset($_GET["idp"])) 
		$idp=strtolower($_GET["idp"]);
	
	if (trim($idp)=="")
		die("L'idp est non valide, impossible de continuer...");
	
	$idp_trouve = false;
	
	foreach ($gIdp as $key => $value){
		if ($value['id']==$idp)
		{
			$idp_trouve = true;
			break;
		}
	}
	
	if (!$idp_trouve)
		die("L'IDP '$idp' n'est pas dans la liste des IDP autorisés, impossible de continuer...");
	
	
	if (!$gIdp[$idp]['active'])
		die("L'authentification via '".$gIdp[$idp]['text']."' n'est pas encore disponible, veuillez utiliser votre numéro de carte lecteur et date de naissance à la place.");
		
	require_once($gCheminSSP.'lib/_autoload.php');
	
	$saml_auth = new \SimpleSAML\Auth\Simple($gNomSP);
	
	$attributes = array();
	
	if ($saml_auth->isAuthenticated()) {
		$attributes = $saml_auth->getAttributes();    
	}
	else {
		
		$saml_auth->login(array(
				'saml:idp' => $gIdp[$idp]['server'],
			));
		
		$saml_auth->requireAuth();
	}
	
	$session = SimpleSAML_Session::getSessionFromRequest();
	$session->cleanup();
	
	// ### DEBUG retour info IDP ###
	// echo "idp : ".$gIdp[$idp]['server']."<br/>";
	// echo "uid : ".$attributes["uid"][0]."<br/>";
	// echo "eppn : ".$attributes["eduPersonPrincipalName"][0]."<br/>";
	
	// echo "sn : ".$attributes["sn"][0]."<br/>";
	// echo "givenname : ".$attributes["givenName"][0]."<br/>";
	// echo "mail : ".$attributes["mail"][0]."<br/>";
	
	// $eduPersonAffiliation = "";
	// foreach ($attributes["eduPersonAffiliation"] as &$value) {
		// $eduPersonAffiliation .= $value."-";
	// }
	// if ($eduPersonAffiliation!="")
		// $eduPersonAffiliation = substr($eduPersonAffiliation,0,-1);
	
	// echo "eduPersonAffiliation : ".$eduPersonAffiliation."<br/>";
	
	// if (isset($attributes["urn:oid:1.3.6.1.4.1.7135.51.5"][0]))
		// echo "ancien mail um1 : ".$attributes["urn:oid:1.3.6.1.4.1.7135.51.5"][0]."<br/>";
	
	// if (isset($attributes["urn:oid:1.3.6.1.4.1.7135.51.6"][0]))
		// echo "ancien mail um2 : ".$attributes["urn:oid:1.3.6.1.4.1.7135.51.6"][0]."<br/>";
	
	// echo "Session-ID : ".$attributes["eduPersonTargetedID"][0]."<br/>";
	
	// récupération des informations utiles pour le quitus
	$uid = "";  // ne sert pas
	$eppn = "";
	$mail = "";  // ne sert pas
	
	// récupération de l'uid
	// if (isset($attributes["uid"][0]))   // ne sert pas serveur IDP
		// $uid =  $attributes["uid"][0]; 

	// if (trim($uid)=="") // ne doit pas arriver
		// die ("Erreur d'authentification avec l'IDP, l'attribut uid est retourné vide; Veuillez contacter le service d'assistance de '".$gIdp[$idp]['text']."'.");
	
	// récupération de l'eppn
	if (isset($attributes["eduPersonPrincipalName"][0])) 
		$eppn = $attributes["eduPersonPrincipalName"][0];

	if (trim($eppn)=="") // ne doit pas arriver sauf bug serveur IDP
		die ("Erreur d'authentification avec l'IDP, l'attribut eppn est retourné vide; Veuillez contacter le service d'assistance de '".$gIdp[$idp]['text']."'.");
	
	// if (isset($attributes["mail"][0])) // ne sert pas, on utilise le mail du compte lecteur
		// $mail = strtolower($attributes["mail"][0]);

	$eppnc = "";
	$eppnc = urlsafe_b64encode(cryptText($eppn,$gKey));
	
	$dvc = "";
	$dvc = urlsafe_b64encode(cryptText(strtotime("now"),$gKey));
	
	header("location: ".$gURLs."?eppnc=$eppnc&dvc=$dvc&idp=$idp");	
?>