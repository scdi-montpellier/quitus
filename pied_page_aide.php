<?php

	$aide = "default";
	if (isset($_GET["idp"]))
			$aide = $_GET['idp'];
	if (!isset($gAide[$aide]))
			$aide = "default";
		
	echo "<div style='margin-top:10px;'>";
	echo "<div style='margin-top:10px;'><i class='fa fa-question-circle' aria-hidden='true'></i> Besoin d'aide ? Accéder au service <a class='text-decoration-none' alt='Accéder à ".$gAide[$aide]['nom']."' title='Accéder à ".$gAide[$aide]['nom']."' target='_blank' href='".$gAide[$aide]['url']."'>".$gAide[$aide]['nom']."</a></div>";
	echo "<div class='chargement'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> chargement en cours...</div>";
	echo "</div>";

?>