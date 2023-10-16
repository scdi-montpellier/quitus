<?php
	
	echo "<footer class='footer' style='overflow: hidden;'>";
    echo "<div class='container' style='overflow: hidden;'>";
	
	$logo = "";
	if (isset($_GET["idp"]))
			$logo = $_GET['idp'];
		
		
	// affichage des logos
	foreach ($gEtablissement as $key => $value)
	{
		$urllogo=$value['logo'];
		$urlsiteinstitutionel=$value['urlsiteinstitutionel'];
		$textelien=$value['textelien'];
		$visible= $value['logovisible'];
		
		// affichage du logo en fonction des logo actif dans config.php
		if($visible && $logo == "")
			echo "<span class='text-muted'><a class='text-decoration-none' target='_blank' alt=\"$textelien\" title=\"$textelien\" href='$urlsiteinstitutionel'><img src='$urllogo' style='max-height:48px; height:48px;' alt='Logo' title=\"$textelien\"></a></span>&nbsp;";
		
		// affichage du logo en fonction du forçage provenance
		 if ($logo !="" && $key == $logo)
			echo "<span class='text-muted'><a class='text-decoration-none' target='_blank' alt=\"$textelien\" title=\"$textelien\" href='$urlsiteinstitutionel'><img src='$urllogo' style='max-height:48px; height:48px;' alt='Logo' title=\"$textelien\"></a></span>&nbsp;";
	}
	
	// affigafe institution principal et nom appli / version
	echo "| <span style='white-space: normal;' class='text-muted'><a class='text-decoration-none' target='_blank' alt='Site du $gNominstitutionel' title='Site du $gNominstitutionel' href='$gURLsiteinstitutionel'>$gNominstitutionel</a></span> <span style='white-space: nowrap;'>| $gNomAppliCourt - v$gVersionAppli</span>";
   
	echo "</div>";
    echo "</footer>";

?>