<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	session_start();

	include ("config.php");
	include ("fonction.php");
	
	$reponse = "KO";
	$message = "";
	$message2 = "";
	$debug = "";
	
	if (isset($_POST["uid"])) 
		$uid=$_POST["uid"];
	else
		$uid = "";
		
	if (isset($_POST["datenaissance"])) {
		if (validateDate($_POST["datenaissance"],"d/m/Y"))
			$datenaissance=DateTime::createFromFormat('d/m/Y', $_POST["datenaissance"])->format('Y-m-d');
		else
			$datenaissance = "";
	}
	else
		$datenaissance = "";
	
	if ($uid != "") 
	{
		if (strlen($uid) > 3 && (preg_match ('/^[a-zA-Z0-9]+$/', $uid) || filter_var($uid, FILTER_VALIDATE_EMAIL)))
		{
			if($datenaissance != "") {
				
				if(!filter_var($uid, FILTER_VALIDATE_EMAIL))
					$uid = strtoupper($uid);
				
				$url = "$gAdrAlma/almaws/v1/users/$uid?apikey=$gTokenAlma&lang=fr";

				$xml = get_xml($url);
				
				if (!$xml==false)
				{
					$datenaissancealma = get_birthdate($xml);
						
					if ($datenaissance == $datenaissancealma) {
						$quitusOK=true;
						
						$message .= "<div>";
						
						$message .= "<div class='titre2'><i class='fa fa-info-circle' aria-hidden='true'></i> Informations sur votre compte carte lecteur&nbsp;:</div>";
						
						$message .= "<div style='overflow: hidden;'>";
						$message .= "- Identifiant compte carte lecteur&nbsp;: <span data-toggle=\"tooltip\" data-placement=\"top\" title=\"$uid\">$uid</span>";
						$message .= "</div>";
						
						$nom = get_name($xml);
						$message .= "<div>";
						if ($nom!="") {
							$message .= "- Votre nom&nbsp;: $nom";
						}
						else {
							$quitusOK=false;
							$message .= "<i class='fa fa-times' aria-hidden='true'></i> Votre compte lecteur ne comporte pas de nom.";
						}
						$message .= "</div>";
						
						$message .= "<div>";
						if ($datenaissancealma!="") {
							$datenaissancealmafr = date("d/m/Y",strtotime($datenaissancealma));
							$message .= "- Votre date de naissance&nbsp;: $datenaissancealmafr";
						}
						else {
							$quitusOK=false;
							$message .= "<i class='fa fa-times' aria-hidden='true'></i> Votre compte lecteur ne comporte pas de date de naissance.";
						}
						$message .= "</div>";
						
						$message .= "</div>";
						
						$message .= "<div style='overflow: hidden;'>";
						$mailpref = get_mail_preferred($xml,false);
						if ($mailpref!="")
							$message .= "- Votre mail préféré&nbsp;: <span data-toggle=\"tooltip\" data-placement=\"top\" title=\"$mailpref\">$mailpref</span>";
						else {
							$message .= "- Votre mail préféré&nbsp;: pas d'email préféré";
							$mailpref =get_first_mail($xml, false);
						}
						$message .= "</div>";
						
						$message .= "<div style='overflow: hidden;'>";
						$mails = get_mails($xml,true,false);
						if ($mails!="")
							$message .= "- Autres mails&nbsp;: <span data-toggle=\"tooltip\" data-placement=\"top\" title=\"$mails\">$mails</span>";
						
						if ($mailpref == "" && $mails == "") {
							$quitusOK=false;
							$message .= "<i class='fa fa-times' aria-hidden='true'></i> Votre compte lecteur ne comporte pas de mail.";
						}
						
						$message .= "</div>";

						$message .= "<div class='mt-1'>";
						$message .= "<div class='titre2'><i class='fa fa-info-circle' aria-hidden='true'></i> Informations sur les blocages en cours&nbsp;:</div>";
						
						if (has_blocks($xml)) 
							{
								$nbblocages = sum_blocks($xml);
								$nbquitus = get_nb_quitus($xml);
								$quitusDejaDemande = get_quitus($xml);
								
								if ($nbblocages != $nbquitus) 
								{
									if ($nbblocages>1)
										$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>$nbblocages blocages</b> actifs";
									else
										$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>1 blocage</b> actif";
									
									$quitusOK=false;
								
									if ($nbquitus>0) 
									{
										if ($quitusDejaDemande !="")
											$message .= " dont quitus&nbsp;:$quitusDejaDemande";
										else
											$message .= ".";
									}
								}
								elseif ($nbquitus==1) {
									$message .= "<i class='fa fa-check-square-o' aria-hidden='true'></i> Un quitus est déjà existant (cela ne bloque pas la demande)&nbsp;:";
									$message .= "$quitusDejaDemande";
								}
								elseif ($nbquitus>1) {
									$message .= "<i class='fa fa-check-square-o' aria-hidden='true'></i> $nbquitus quitus sont déjà existants (cela ne bloque pas la demande)&nbsp;:";
									$message .= "$quitusDejaDemande";
								}
							}
						else
							$message .= "<i class='fa fa-check-square-o' aria-hidden='true'></i> Vous n'avez pas de blocage actif.<br/>";
						
						$message .= "</div>";
						
						$url = "$gAdrAlma/almaws/v1/users/$uid/loans?user_id_type=all_unique&limit=10&offset=0&order_by=id&direction=ASC&apikey=$gTokenAlma&lang=fr";
						
						$message .= "<div class='mt-2'>";
						$message .= "<div class='titre2'><i class='fa fa-info-circle' aria-hidden='true'></i> Informations sur vos prêts&nbsp;:</div>";
						
						$xml = get_xml($url);
						
						if (!$xml==false)
						{
							if (has_loans($xml)) {
								$nbpret = sum_loans($xml);
								if ($nbpret>1)
									$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>$nbpret prêts</b> en cours<br/>";
								else
									$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>1 prêt</b> en cours.<br/>";
								$quitusOK=false;
							}
							else
								$message .= "<i class='fa fa-check-square-o' aria-hidden='true'></i> Vous n'avez pas de prêt en cours.<br/>";
						}
						
						$message .= "</div>";
						
						$url = "$gAdrAlma/almaws/v1/users/$uid/fees?user_id_type=all_unique&status=ACTIVE&apikey=$gTokenAlma&lang=fr";
						
						$message .= "<div class='mt-2'>";
						$message .= "<div class='titre2'><i class='fa fa-info-circle' aria-hidden='true'></i> Informations sur les amendes/frais&nbsp;:</div>";
						
						$xml = get_xml($url);

						if (!$xml==false)
						{
							// $debug = has_fees($xml);
							
							if (has_fees($xml)) {
								$nbfees = nb_fees($xml);
								$montantfees = sum_fees($xml);
								if ($nbfees>1)
									$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>$nbfees amendes/frais</b> en cours (pour un <b>montant</b> total de <b>$montantfees</b>)";
								else
									$message .= "<i class='fa fa-times' aria-hidden='true'></i> Vous avez <b>1 amende/frais en cours</b> (pour un <b>montant</b> total de <b>$montantfees</b>)";
								$quitusOK=false;
							}
							else {
								$message .= "<i class='fa fa-check-square-o' aria-hidden='true'></i> Vous n'avez pas d'amende ou de frais.";
							}
						}
						
						$message .= "</div>";
						
						// pour test
						// $quitusOK=false;
						
						if ($quitusOK) {
							$reponse = "OK";
							
							$message2 .= "<div id='resultat'>";
							if ($mailpref !="") {
								$message2 .= "<div class='alert alert-warning mt-3 alert-dismissible fade show' role='alert'><i class='fa fa-info-circle' aria-hidden='true'></i> Si vous voulez utiliser <b>un autre mail</b>, merci de contacter votre bibliothèque<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
								$message2 .= "<div class='alert alert-warning mt-3 alert-dismissible fade show' role='alert'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Attention, votre quitus ne sera valide qu'une fois téléchargé depuis le lien qui vous sera envoyé par mail (lien valable 24h)<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
								$message2 .= "<div style='margin-bottom:3px;'><button type='button' class='btn btn-primary w-100' id='btn_envoi_mail' ><i class='fa fa-share-square-o' aria-hidden='true'></i> Envoyer le lien de téléchargement du quitus sur mon mail <b>$mailpref</b></button></div>";
								$message2 .= "<div><button type='button' class='btn btn-danger w-100' id='btn_annuler' ><i class='fa fa-ban' aria-hidden='true'></i> Annuler la demande</button></div>";
							
							}
							else // on ne devrait jamais passer ici !
								$message2 .= "<i class='fa fa-times' aria-hidden='true'></i> Impossible de récupérer votre quitus, aucun mail spécifié. Veuillez contacter votre bibliothèque ou consulter le service une question ?<br/>";
							$message2 .= "</div>";
							
							$message2 .=  "<script>";
							$message2 .=  "$('[data-toggle=\"tooltip\"]').tooltip();";
							$message2 .=  "$('#btn_envoi_mail').click(function() {";
							$message2 .=  "	envoi_mail();";
							$message2 .=  "});";
							$message2 .=  "$('#btn_annuler').click(function() {";
							$message2 .=  "	document.location.href='$gURLs';";
							$message2 .=  "});";
							$message2 .=  "</script>";

						}
						else {
							$reponse = "PBQUITUS";
							$message .= "<hr><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Votre situation ne permet pas de récupérer votre quitus automatiquement</b>.<br/>Veuillez vous connecter à votre compte lecteur pour plus d'informations ou contacter votre bibliothèque pour régulariser votre situation.";
							
							$message2 .= "<div><button type='button' class='btn btn-danger w-100' id='btn_annuler' ><i class='fa fa-ban' aria-hidden='true'></i> Annuler la demande</button></div>";
							
							$message2 .=  "<script>";
							$message2 .=  "$('[data-toggle=\"tooltip\"]').tooltip();";
							$message2 .=  "$('#btn_annuler').click(function() {";
							$message2 .=  "	document.location.href='$gURLs';";
							$message2 .=  "});";								
							$message2 .=  "</script>";
						}
					}
					else
						$message .= "<i class='fa fa-times' aria-hidden='true'></i> La date de naissance ".date("d/m/Y",strtotime($datenaissance))." ne correspond pas à votre identifiant.";
				}
				else // on ne devrait jamais passer ici !
					$message .= "<i class='fa fa-times' aria-hidden='true'></i> Impossible de lire les informations de votre compte lecteur, votre identifiant n'est pas correct.";
			}
			else // on ne devrait jamais passer ici !
				$message .= "<i class='fa fa-times' aria-hidden='true'></i> Veuillez saisir une date de naissance valide (format jj/mm/aaaa).";
		}
		else // on ne devrait jamais passer ici !
			$message .= "<i class='fa fa-times' aria-hidden='true'></i> Veuillez saisir un identifiant correct.";
	}
	else // on ne devrait jamais passer ici !
		$message .= "<i class='fa fa-times' aria-hidden='true'></i> Veuillez saisir l'identifiant présent sous le code barre de votre carte universitaire.";
	
	$array['reponse'] = $reponse;
	$array['message'] = $message;
	$array['message2'] = $message2;
	$array['debug'] = $debug;
	
	echo json_encode($array);
	
?>