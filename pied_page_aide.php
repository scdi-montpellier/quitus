<?php
		
	$aide = "default";
	if (isset($_GET["idp"]))
			$aide = $_GET['idp'];
	if (!isset($gEtablissement[$aide]))
			$aide = "default";
		
	echo "<div style='margin-top:10px;'>";
	echo "<div style='margin-top:10px;'><i class='fa fa-question-circle' aria-hidden='true'></i> Besoin d'aide ? Accéder au service <a class='text-decoration-none' alt='Accéder à ".$gEtablissement[$aide]['aide_nom']."' title='Accéder à ".$gEtablissement[$aide]['aide_nom']."' target='_blank' href='".$gEtablissement[$aide]['aide_url']."'>".$gEtablissement[$aide]['aide_nom']."</a></div>";
	echo "<div class='chargement'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> chargement en cours...</div>";
	echo "</div>";

?>