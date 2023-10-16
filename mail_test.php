<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	require ('/var/www/html/quitus-scdi/PHPMailer/src/Exception.php');
	require ('/var/www/html/quitus-scdi/PHPMailer/src/PHPMailer.php');
	
	// require ('/var/www/html/PHPMailer/src/SMTP.php');
	
	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
			
		$mail->setLanguage('fr', '/PHPMailer/language/');
		
		$mail->CharSet = 'UTF-8';
		
		//Recipients
		$mail->setFrom("no-reply@quitus.scdi-montpellier.fr", "Quitus BU");
		$mail->addAddress("sebastien.leyreloup@univ-montp3.fr", "Sebastien Leyreloup");     // Add a recipient
		
		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject  = 'Test mail quitus';
		$mail->Body     = "Bonjour,<br/><br/>";
		$mail->Body    .= "TEST mail quitus";

		
		$mail->AltBody  = "Bonjour,\n\n";
		$mail->AltBody .= "TEST mail quitus";

		if ($mail->send())
			echo "Le mail a été envoyé.";
		else
			echo "Erreur lors de l'envoi du mail.";
		
	} catch (Exception $e) {
		echo "Le mail n'a pas pu être envoyé. Erreur : ".$mail->ErrorInfo;
	}
	
	?>