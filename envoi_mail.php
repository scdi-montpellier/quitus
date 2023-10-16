<?php
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
	include ("config.php");
	
	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	require ($gDossier.'PHPMailer/src/Exception.php');											   
	require ($gDossier.'PHPMailer/src/PHPMailer.php');
	require ($gDossier.'PHPMailer/src/SMTP.php');
	
	session_start();
	
	include ("config.php");
	include ("fonction.php");
	
	$reponse = "KO";
	$message = "";
	
	if (isset($_POST["uid"])) 
		$uid=$_POST["uid"];
	else
		$uid = "";
		
	if (isset($_POST["datenaissance"])) 
		if (validateDate($_POST["datenaissance"],"d/m/Y"))
			$datenaissance=DateTime::createFromFormat('d/m/Y', $_POST["datenaissance"])->format('Y-m-d');
		else
			$datenaissance = "";
	else
		$datenaissance = "";
	
	if ($uid != "") 
	{
		if (strlen($uid) > 3 && (preg_match ('/^[a-zA-Z0-9]+$/', $uid) || filter_var($uid, FILTER_VALIDATE_EMAIL)))
		{
			if($datenaissance != "") 
			{
				$url = "$gAdrAlma/almaws/v1/users/$uid?apikey=$gTokenAlma&lang=fr";
				
				$xml = get_xml($url);
				
				if (!$xml==false)
				{
					$datenaissancealma = get_birthdate($xml);
						
					if ($datenaissance == $datenaissancealma) 
					{
						$nomdemande = get_name($xml);
						
						if ($nomdemande!="")
						{
							
							$maildemande = get_mail_preferred($xml,false);
							
							if ($maildemande == "")
								$maildemande = get_first_mail($xml,false);
								
							if ($maildemande != "")
							{
								//!!!!!!!!!!! Pour test et envoi à un compte de test !!!!!!!!!!!!!!!!
								// $maildemande = "xxxxxx.xxxxxxxxxxxx@nomdedomaine.tld";
								
								$datelimite = date("Y-m-d H:i:s",strtotime(date("d M Y H:i:s")." + 1 day"));
								
								$datedemande = date("Y-m-d H:i:s"); //date heure courante

								// $tokendemande = urlsafe_b64encode(cryptText($uid."¦".$datenaissance."¦".$datelimite,$gKey));
								$tokendemande = crypt_quitus($uid,$datenaissance,$datelimite,$gKey);
								
								// on génère le lien de téléchargement
								$lien = $gURLs."lien-$tokendemande";
									
								$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
								try {
									
									if ($gMailSMTP!="")
									{
										$mail->isSMTP();                                            // Set mailer to use SMTP
										$mail->Host       = $gMailSMTP;  // Specify main and backup SMTP servers
										$mail->SMTPAuth   = false;                                   // Enable SMTP authentication
										$mail->Port       = 25;
									}	
									
									$mail->setLanguage('fr', '/PHPMailer/language/');
									
									$mail->CharSet = 'UTF-8';
									
									//Recipients
									$mail->setFrom($gMailAddFrom, $gMailNameFrom);
									$mail->addAddress($maildemande, $nomdemande);     // Add a recipient
									
									//Attachments
									// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
									// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
									
									//Content
									$mail->isHTML(true);                                  // Set email format to HTML
									$mail->Subject  = 'Accéder à votre quitus';
									$mail->Body     = "Bonjour $nomdemande,<br/><br/>";
									$mail->Body    .= "Vous venez de faire une demande de quitus au ".$gNominstitutionelcomplet.".<br/><br/>";
									$mail->Body    .= "Veuillez cliquer sur le lien suivant pour récupérer votre quitus :<br/>";
									$mail->Body    .= "<a target='_blank' href='$lien'>$lien</a><br/><br/>";
									$mail->Body    .= "Si le lien ne s'ouvre pas, veuillez copier/coller le lien dans votre navigateur.<br/>";
									$mail->Body    .= "Ce lien est valide 24h. Passé ce délai, il faudra refaire une demande de quitus en ligne.<br/><br/>";
									$mail->Body    .= "<b>Attention, ce mail est strictement confidentiel : ne pas transférer ce mail, ne pas partager le lien qu'il contient.</b><br/><br/>";
									$mail->Body    .= "Cordialement,<br/>";
									$mail->Body    .= ucfirst($gLNominstitutionel)."<br/>";
									
									$mail->AltBody  = "Bonjour $nomdemande,\n\n";
									$mail->AltBody .= "Vous venez de faire une demande de quitus au ".$gNominstitutionelcomplet.".\n\n";
									$mail->AltBody .= "Veuillez cliquer sur le lien suivant pour récupérer votre quitus :\n";
									$mail->AltBody .= "$lien\n\n";
									$mail->AltBody .= "Si le lien ne s'ouvre pas, veuillez copier/coller le lien dans votre navigateur.\n\n";
									$mail->AltBody .= "Ce lien est valide 24h. Passé ce délai, il faudra refaire une demande de quitus en ligne.\n\n";
									$mail->AltBody .= "Attention, ce mail est strictement confidentiel : ne pas transférer ce mail, ne pas partager le lien qu'il contient.\n\n";
									$mail->AltBody .= "Cordialement,\n";
									$mail->AltBody .= ucfirst($gLNominstitutionel);

									$mail->send();
									
									// on insere dans la table la demande
									$dbh = new PDO('mysql:host=localhost;dbname=quitus', $gaSql['user'], $gaSql['password']);
									
									$sql = "INSERT INTO demande_quitus (uid, date_limite) VALUES (?,?)";
									$dbh->prepare($sql)->execute([$uid,$datelimite]);
									
									$dbh = null;
									
									$reponse = 'OK';
									$message = "<i class='fa fa-check-square' aria-hidden='true'></i>  Le mail a été envoyé à $maildemande";
									
								} catch (Exception $e) {
									$message = "Le mail n'a pas pu être envoyé. Erreur : ".$mail->ErrorInfo;
								}
							}
							else
								$message = "Le mail n'a pas pu être envoyé. Aucun mail lecteur n'a pu être trouvé.";
						}
						else
							$message = "Le mail n'a pas pu être envoyé. Le nom du compte lecteur est vide.";
					}
					else
						$message = "Le mail n'a pas pu être envoyé. La date de naissance ne correspondant pas.";
				}
				else
					$message = "Le mail n'a pas pu être envoyé. Identifiant invalide.";
			}
			else
				$message = "Le mail n'a pas pu être envoyé. Date de naissance vide ou incorrecte.";
		}
		else
			$message = "Le mail n'a pas pu être envoyé. Identifiant invalide.";
	}
	else
		$message = "Le mail n'a pas pu être envoyé. Identifiant vide.";
	
	$array['reponse'] = $reponse;
	$array['message'] = $message;
	
	echo json_encode($array);
?>