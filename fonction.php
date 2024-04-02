<?php 

function get_IP() {
	return $_SERVER['REMOTE_ADDR'];
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
	
function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }
 
function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }
	
function formatFrenchPhoneNumber($phoneNumber, $international = false)
	{
	//Supprimer tous les caractères qui ne sont pas des chiffres
	$phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
	//Garder les 9 derniers chiffres
	$phoneNumber = substr($phoneNumber, -9);
	//On ajoute +33 si la variable $international vaut true et 0 dans tous les autres cas
	$motif = $international ? '+33 (\1).\2.\3.\4.\5' : '0\1.\2.\3.\4.\5';
	$phoneNumber = preg_replace('/(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/', $motif, $phoneNumber);

	return $phoneNumber;
	} 

function genererPwd($taille = 10)
	{
		$tab =array('a','z','e','r','t','y','u','i','o','p','q','s','d','f','g','h','j','k','l','m','w','x','c','v','b','n','1','2','3','4','5','6','7','8','9','*','+','$','!','&');
		shuffle($tab);
		return substr(implode('',$tab),0,$taille);
	}

function noAccentFeed($text, $EncIn = 'CP1252')
	{
		return iconv($EncIn, 'ASCII//TRANSLIT//IGNORE', $text);
	}

function removeAccent($string)
	{
		$string = utf8_decode($string);
		$string = strtr($string,    'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
									'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$string = utf8_encode($string);                 
		return $string;
	}

function UTCdatestringToTime($utcdatestring)
{
    $tz = date_default_timezone_get();
    date_default_timezone_set('UTC');

    $result = strtotime($utcdatestring);

    date_default_timezone_set($tz);
    return $result;
}
	
function cryptText($texte,$key)
	{

		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_ECB, "");
		$iv =  str_pad("",32,"123");
		mcrypt_generic_init($td, $key, $iv);
		
		$temp = mcrypt_generic($td, $texte);
		// mcrypt_generic_end ($td);
		
		return $temp;
	}

function decryptText($texte,$key)
	{
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_ECB, "");
		$iv =  str_pad("",32,"123");
		mcrypt_generic_init($td, $key, $iv);
		$temp = mdecrypt_generic($td, $texte);
		// mcrypt_generic_end ($td);
		return trim($temp);
	}

function urlsafe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}

function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

function decrypt_quitus($quituscrypt,$key)
{
	$quitustemp = urlsafe_b64decode($quituscrypt);
	$quitusdecrypt = decryptText($quitustemp,$key);
	// $quitus = $uid."¦".$datenaissance."¦".$datelimite
	return explode("¦", $quitusdecrypt);
}

function crypt_quitus($uid,$datenaissance,$datelimite,$key)
{
	// $quitus = $uid."¦".$datenaissance."¦".$datelimite
	return urlsafe_b64encode(cryptText($uid."¦".$datenaissance."¦".$datelimite,$key));
}

function get_mask_string($str) {
	$replacementChar = '*';
	$limit=1;
	
	if (strlen($str)>=7 && strlen($str)<10)
		$limit=2;
	elseif (strlen($str)>=10)
		$limit=3;
	
	$partievisible1 = substr($str, 0, $limit);
	
	if (strlen($str)>5){
		$partievisible2 = substr($str, strlen($str)-$limit, $limit);
		return str_pad($partievisible1, strlen($str)-$limit, $replacementChar).$partievisible2;
	}
	else
		return str_pad($partievisible1, strlen($str), $replacementChar);
}
	
function get_masked_email($email) {
        $keywords = preg_split("/[@]+/", $email);
		$keywordsBeforeAt = preg_split("/[\.]+/", $keywords[0]);
		$keywordsAfterAt = preg_split("/[\.]+/", $keywords[1]);
		$keywordsBeforeAtm = array_map("get_mask_string", $keywordsBeforeAt);
		$keywordsAfterAtm = array_map("get_mask_string", $keywordsAfterAt);

		return implode(".", $keywordsBeforeAtm)."@".implode(".", $keywordsAfterAtm);
	}
	
function get_xml($url)
    {	
		libxml_use_internal_errors(true);
		$xml=@simplexml_load_file($url,"SimpleXMLElement",LIBXML_NOCDATA);

		if($xml ===  FALSE){
			$xml= false;
		}
		elseif ($xml->web_service_result->errorsExist=="true")
			{
				$xml= false;
			}
		
		return $xml;
	
    }

function get_xml_from_string($str_xml)
    {	
		libxml_use_internal_errors(true);
		$xml=@simplexml_load_string($str_xml,"SimpleXMLElement",LIBXML_NOCDATA);
		// print_r($xml->errorsExist);
		
		if($xml ===  FALSE){
			$xml = false;
		}
		elseif ($xml->errorsExist=="true")
			{
				$xml = false;
				// die("là");
			}
		
		return $xml;
    }
	
function get_error_alma_xml_from_string($str_xml)
    {	
		$strErreur = "";
		
		libxml_use_internal_errors(true);
		$xml=@simplexml_load_string($str_xml,"SimpleXMLElement",LIBXML_NOCDATA);
		
		if($xml ===  FALSE){
			$strErreur = "XML invalide";
		}
		elseif ($xml->errorsExist=="true")
			{
				$strErreur  = "Code erreur : ".$xml->errorList->error->errorCode."<br/>";
				$strErreur .= "Message : ".$xml->errorList->error->errorMessage."<br/>";
				$strErreur .= "TrackingId : ".$xml->errorList->error->trackingId;
			}
		else{
			$strErreur = "Code erreur inconnu";
		}
		
		return "<div style='padding-left:3px; border-left: 3px solid #721c24;font-size: 0.800em; font-style: italic;'>$strErreur</div>";
    }
	
function get_name($xml)
    {	
		$ligne = "";

		if (trim($xml->full_name) != "" && strlen(trim($xml->full_name)) > 1)
			$ligne = $xml->full_name;
		elseif (trim($xml->first_name) != "" && trim($xml->last_name) != "")
			$ligne = $xml->first_name." ".$xml->last_name;
		else
			$ligne = "";
		
		return $ligne;
	
    }
	
function get_birthdate($xml)
    {	
		$ligne = "";

		if ($xml->birth_date != "") {
			$datenaissance = date("Y-m-d",UTCdatestringToTime($xml->birth_date));
			$ligne = $datenaissance;
		}
		else
			$ligne = "Non renseigné";
		
		return $ligne;
	
    }
	
function get_mail_preferred($xml,$mask=true)
    {	
		$ligne = "";
		
		foreach ($xml->contact_info->emails->email as $emailinfo)
		{
			
			if ($emailinfo['preferred']=="true")
				if ($mask)
					$ligne = get_masked_email($emailinfo->email_address);
				else
					$ligne = $emailinfo->email_address;
			
		}
		
		return $ligne;
    }
	
function get_first_mail($xml,$mask=true)
    {	
		$ligne = "";
		
		if ($xml->contact_info->emails->email->email_address != "")
		{	
			if ($mask)
				$ligne = get_masked_email($xml->contact_info->emails->email->email_address);
			else
				$ligne = $xml->contact_info->emails->email->email_address;
			
			return $ligne;
			
		}	
		
    }
	
function get_mails($xml,$exlure_mail_pref=true,$mask=true)
    {	
		$ligne = "";
		
		foreach ($xml->contact_info->emails->email as $emailinfo)
		{
			if (!$exlure_mail_pref && $emailinfo['preferred']=="true") {
				$pref="";
				if ($emailinfo['preferred']=="true")
					$pref=" (votre email préféré)";
				
				if ($mask)
					$ligne .= get_masked_email($emailinfo->email_address);
				else
					$ligne .= $emailinfo->email_address;
				
				$ligne .= "$pref, ";
			}
			elseif ($emailinfo['preferred']!="true") {
				if ($mask)
					$ligne .= get_masked_email($emailinfo->email_address);
				else
					$ligne .= $emailinfo->email_address;
				$ligne .= ", ";
			}
		}
		
		$ligne = substr($ligne,0,-2);
		
		return $ligne;
    }

function get_blocks($xml, $activeonly = true)
	{
		$ligne = "";
		$cpt=0;
		foreach ($xml->user_blocks->user_block as $blockinfo)
		{
			if (!$activeonly || strtoupper($blockinfo->block_status)=="ACTIVE") 
			{
				$cpt++;
				$ligne .= "<li>$cpt/ Description : ".$blockinfo->block_description['desc']." - ";
				$ligne .= "note : ".$blockinfo->block_note;
				
				if ($blockinfo->expiry_date!="") 
				{
					$datedeblocage="";
					$datedeblocage = date("d/m/Y \à H:i:s",UTCdatestringToTime($blockinfo->expiry_date));
					$ligne .= " (expire le $datedeblocage)";
				}
				
				$ligne .= "</li>";
				
			}
		}
		
		if ($ligne!="")
			$ligne = "<ul>$ligne</ul>";
		return $ligne;
	}
	
function add_quitus($xml, $url, $uid, $gTokenAlma, $codequitus)
	{
		global $gTagblocage;
		
		$bOK = false;
		$message = "";
		
		$codequitustrouve = has_quitus_valide($xml, $codequitus);
		
		if ($codequitustrouve=="") // pas trouvé de quitus créé par l'appli, on le crée
		{
			$user_block = $xml->user_blocks->addChild('user_block');
			$user_block->addAttribute('segment_type', 'Internal');
			$block_type = $user_block->addChild('block_type', 'GENERAL');
				$block_type->addAttribute('desc', "Général");
			$block_description = $user_block->addChild('block_description', '07-LOCAL');
				// 02/04/2024 - changement description du quitus pour aller avec les valeurs qui seraient mises en passant par alma
				// $block_description->addAttribute('desc', "Quitus(LOCAL)");
				$block_description->addAttribute('desc', "Quitus");
			$user_block->addChild('block_status', 'ACTIVE');
			$user_block->addChild('block_note', $gTagblocage.' ['.$codequitus.']');
			$user_block->addChild('created_by', $gTagblocage);
			
			// $datecrea = new DateTime();
			// $tz = date_default_timezone_get();
			// date_default_timezone_set('UTC');
			$user_block->addChild('created_date', gmdate("Y-m-d\TH:i:s.v\Z"));
			// $user_block->addChild('created_date', $datecrea->format("Y-m-d\TH:i:s.v\Z"));
			// date_default_timezone_set($tz);
			
			$user_block->addChild('expiry_date');
			
			$ch = curl_init();
			// $url = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/users/{user_id}';

			$templateParamNames = array('{user_id}');
			$templateParamValues = array(urlencode($uid));
			$url = str_replace($templateParamNames, $templateParamValues, $url);
			$queryParams = '?' . urlencode('user_id_type') . '=' . urlencode('all_unique') . '&' . urlencode('send_pin_number_letter') . '=' . urlencode('false') . '&' . urlencode('apikey') . '=' . urlencode($gTokenAlma) . '&' . urlencode('lang') . '=' . urlencode('fr');
			curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
			$response = curl_exec($ch);
			curl_close($ch);
			
			// on analyse le résultat de la requete
			$responsexml = get_xml_from_string($response);
			
			if ($responsexml != false) // si y a pas d'erreur
			{	
				// 02/04/2024 - pb avec la fonction de validation de quitus
				if (has_quitus_valide($responsexml,$codequitus) == $codequitus) // on vérifie si le quitus codequitus est bien crée
				{
					$bOK = true;
					$codequitustrouve = $codequitus;
				}
				else // on ne devrait jamais passer ici
					$message = "Code 1 - Impossible de retrouver le quitus '$codequitus' qui a &eacute;t&eacute; cr&eacute;&eacute; dans le syst&egrave;me - Erreur Alma / utilisateur<br/><i class='fa fa-info-circle' aria-hidden='true'></i> Veuillez contacter votre bibliothèque.";
			}
			else // on ne devrait jamais passer ici
				$message  = "Code 2 - Impossible de cr&eacute;er le quitus dans le syst&egrave;me - Erreur Alma / utilisateur".get_error_alma_xml_from_string($response)."<i class='fa fa-info-circle' aria-hidden='true'></i> Veuillez contacter votre bibliothèque.";
		}
		else // on a trouvé un autre quitus fait par l'appli quitus, on utilise celui-ci
			$bOK = true;
		
		$array['OK'] = $bOK;
		$array['codequitus'] = $codequitustrouve;
		$array['message'] = $message;
		
		return $array;
	}
	
function sum_blocks($xml, $activeonly = true)
	{ // compte le nombre de blocage ACTIF (défaut) ou NON
		$cpt=0;
		foreach ($xml->user_blocks->user_block as $blockinfo)
		{
			if ($activeonly)
			{
				if (strtoupper($blockinfo->block_status)=="ACTIVE") 
				{
					$bDate = true;
					if ($blockinfo->expiry_date!="")
							if (strtotime("now utc")>UTCdatestringToTime($blockinfo->expiry_date))
								$bDate = false;
					
					if (strtoupper($blockinfo->block_type) == "LOAN" &&
						strtoupper($blockinfo->block_type['desc']) == "PRêT" &&
						strtoupper($blockinfo->block_description) == "OVERDUE" &&
						strtoupper($blockinfo->block_description['desc']) == "BLOCAGE DU PRêT EN RETARD")
						// test  blocages de type "Prêt - Blocage du prêt en retard"
						$bDate = false;
					
					if (strtoupper($blockinfo->block_type) == "GENERAL" &&
						strtoupper($blockinfo->block_type['desc']) == "GéNéRAL" &&
						preg_match ('/^0(1|2|3|4)-GLOBAL$/', strtoupper($blockinfo->block_description)) &&
						preg_match ('/^DOCUMENTS EN RETARD \((1|2|3|4)° (.*)RAPPEL\)\(GLOBAL\)$/', strtoupper($blockinfo->block_description['desc'])))
						// test  blocages de type "GENERAL - Documents en retard (x° xxxxxxx rappel)(GLOBAL)""
						$bDate = false;
					
					if ($bDate)
						$cpt++;
				}
			}
			else
			{
				$cpt++;
			}
		}
		
		return $cpt;
	}

function has_blocks($xml, $activeonly = true)
	{
		if (sum_blocks($xml, $activeonly)== 0)
			return false;
		else
			return true;
	}

function get_quitus($xml, $activeonly = true)
	{
		// retourne les quitus de l'utilisateur
		$ligne = "";

		foreach ($xml->user_blocks->user_block as $blockinfo)
		{
			if (!$activeonly || strtoupper($blockinfo->block_status)=="ACTIVE") 
			{
				if (strtoupper($blockinfo->block_type) == "GENERAL" &&
					strtoupper($blockinfo->block_description) == "07-LOCAL" &&
					// strtoupper($blockinfo->block_description['desc']) == "QUITUS(LOCAL)")
					strtoupper($blockinfo->block_description['desc']) == "QUITUS")
					{
						$bDate = true;
						
						// pour les quitus avec date d'expiration ????, on vérifie que la date a bien expiré !
						if ($blockinfo->expiry_date!="") {
							if (strtotime("now utc")<=UTCdatestringToTime($blockinfo->expiry_date))
								$bDate = false;
						}

						if ($bDate) { 						
							
							$ligne .= "<li> ".trim($blockinfo->block_note)."";
							
							if ($blockinfo->created_date!="") 
							{
								$datecreation="";
								$datecreation = " du ".date("d/m/Y \à H:i:s",UTCdatestringToTime($blockinfo->created_date));
								$ligne .= " $datecreation";
							}
							else // on ne devrait jamais passer ici
								$ligne .= " (date inconnue)";
							
							$ligne .= "</li>";
						}
					}
			}
		}
		
		if ($ligne!="")
			$ligne = "<ul>$ligne</ul>";
		return $ligne;
	}

function get_nb_quitus($xml, $activeonly = true)
	{
		// retourne le nombre de quitus de l'utilisateur
		$cpt=0;
		
		foreach ($xml->user_blocks->user_block as $blockinfo)
		{
			if (!$activeonly || strtoupper($blockinfo->block_status)=="ACTIVE") 
			{
				if (strtoupper($blockinfo->block_type) == "GENERAL" &&
					strtoupper($blockinfo->block_description) == "07-LOCAL" &&
					strtoupper($blockinfo->block_description['desc']) == "QUITUS")
					{
						$bDate = true;
						
						// pour les quitus avec date d'expiration ????, on vérifie que la date a bien expiré !
						if ($blockinfo->expiry_date!="") {
							if (strtotime("now utc")<=UTCdatestringToTime ($blockinfo->expiry_date))
								$bDate = false;
						}

						if ($bDate) { 						
							$cpt++;
						}
					}
			}
		}

		return $cpt;
	}

function has_quitus_valide($xml, $codequitus = "")
	{
		// retourne si l'utilisateur a un quitus "Appli quitus" avec $codeunique en cours de validité
		// si $codequitus est vide, 
		// retourne le 1er codequitus crée par l'appli quitus, 
		// sinon retourne le code quitus recherché
		// retourne chaine vide si aucun quitus Appli quitus trouvé
	
		global $gTagblocage;
		
		$bOK=false;
		$codequitustrouve="";
		$codequitus = strtoupper($codequitus);
		
		$cptquitus = 0;
		
		foreach ($xml->user_blocks->user_block as $blockinfo)
		{
			
			// on boucle sur les blocages
			if (strtoupper($blockinfo->block_status)=="ACTIVE") 
			{// on boucle sur les blocages actifs
				
				if (strtoupper($blockinfo->block_type) == "GENERAL" &&
					strtoupper($blockinfo->block_description) == "07-LOCAL" &&
					strtoupper($blockinfo->block_description['desc']) == "QUITUS")
					{
						$cptquitus++;
						
						// on boucle sur les blocages actifs qui sont des quitus
						
						$bDate = true;
						
						// pour les quitus avec date d'expiration, si la date a expiré on ne le compte pas comme valide
						if ($blockinfo->expiry_date!="") {
							if (strtotime("now utc")>UTCdatestringToTime($blockinfo->expiry_date))
								$bDate = false;
						}

						if ($bDate) 
						{ 	// on boucle sur les blocages actifs qui sont des quitus et dont la date d'expiration n'a pas été dépassée			

							// on vérifie que la date création postérieure à maintenant
							if ($blockinfo->created_date!="")  //on doit tjs passer ici
							{
								if (strtotime("now utc")>=UTCdatestringToTime($blockinfo->created_date)) //on doit tjs passer ici
									if ($codequitus!="" && strtoupper($blockinfo->block_note) == "QUITUS BIU [$codequitus]") // pour compatibilité ancien quitus sous l'ère BIU !!!
										$codequitustrouve = $codequitus;
									elseif ($codequitus!="" && strtoupper($blockinfo->block_note) == "$gTagblocage [$codequitus]")
										$codequitustrouve = $codequitus;
									elseif (substr($blockinfo->block_note,0,strlen($gTagblocage)+2) == "$gTagblocage [")
										$codequitustrouve = strtoupper(substr($blockinfo->block_note,strlen($gTagblocage)+2,-1));
							}
							else // on ne devrait jamais passer ici
								if ($codequitus!="" && strtoupper($blockinfo->block_note) == "$gTagblocage [$codequitus]")
										$codequitustrouve = $codequitus;
								elseif (substr($blockinfo->block_note,0,strlen($gTagblocage)+2) == "$gTagblocage [")
									$codequitustrouve = strtoupper(substr($blockinfo->block_note,strlen($gTagblocage)+2,-1));
						}
					}
			}
			
			if ($codequitustrouve != "") // si on a trouvé le quitus, on sort directement
				return $codequitustrouve;
		}
		// endforeach;
		
		return $codequitustrouve;
	}
	
function has_fees($xml)
    {	
		$ligne = "";

		if (intval($xml['total_record_count'])==0)
			return false;
		elseif (intval($xml['total_record_count'])>0 && floatval($xml['total_sum']) == 0) // cas frais d'inscription gratuit
			return false;
		else
			return true;
	
    }

function nb_fees($xml)
    {	
		if (intval($xml['total_record_count'])>0 && floatval($xml['total_sum']) > 0)
			return intval($xml['total_record_count']);
		else
			return 0;

    }
	
function sum_fees($xml)
    {	
		$ligne = "";
		
		$currency = "";
		
		if (floatval($xml['total_sum'])>0)
		{
			if ($xml['currency']!="")
				$currency = " ".$xml['currency'];
			
			return floatval($xml['total_sum']). $currency;
		}
		else
			return 0;
    }

function has_loans($xml)
    {	
		$ligne = "";

		if (intval($xml['total_record_count']) > 0)
			return true;
		else
			return false;
	
    }

function sum_loans($xml)
    {	
		$ligne = "";

		return $xml['total_record_count'];	
    }
	
function get_validation_for_quitus($xml_user,$xml_loans,$xml_fees)
	{
		// retourne une chaine vide si l'obtention du quitus est toujours  possible, sinon message d'erreur
		$ligne = "";
		
		$nom = get_name($xml_user);
		if ($nom=="")
			$ligne = "Votre compte lecteur ne comporte pas de nom.";
		
		$datenaissancealma = get_birthdate($xml_user);
		if ($datenaissancealma=="")
			$ligne = "Votre compte lecteur ne comporte pas de date de naissance.";
		
		$mails = get_mails($xml_user,false,false);
		if ($mails=="")
			$ligne = "Votre compte lecteur ne comporte aucun mail.";
		
		$mailpref = get_mail_preferred($xml_user,false);
		if ($mailpref=="")
			$mailpref = get_first_mail($xml_user, false);

		$mails = get_mails($xml_user,true,false);
		
		if ($mailpref == "" && $mails == "")
				$ligne = "Votre compte lecteur ne comporte pas de mail.";
		
		if (has_blocks($xml_user))
		{
			// vérifions que les blocages ne sont pas que des QUITUS si oui on laisse continuer
			if (sum_blocks($xml_user) != get_nb_quitus($xml_user)) {
				$ligne = "Vous avez au moins 1 blocage actif.";
			}
		}
		
		if (has_loans($xml_loans))
			$ligne = "Vous avez au moins 1 prêt en cours.";
		
		if (has_fees($xml_fees))
			$ligne = "Vous avez au moins 1 amende/frais en cours.";
		
		return $ligne;
		
	}
?>