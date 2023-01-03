<?php
	// Site en maintenance
	//die("Site en maintenance, merci de revenir plus tard !");

	$gNomAppli = "Quitus BU";
	$gNomAppliCourt = 'Quitus';
	$gVersionAppli = '1.2';
	
	// Adresse serveur API exlibris
	$gAdrAlma = "https://api-eu.hosted.exlibrisgroup.com";
	
	// Token API Exlibris Alma
	$gTokenAlma = urlencode("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
	
	// url de l'appli
	$gURLs = "https://quitus.scdi-montpellier.fr/";
	// chemin de l'appli sur le serveur
	$gDossier = "/var/www/html/quitus-scdi/";
	
	// Labels institution	  
	$gNominstitutionelcourt = "BU";
	$gNominstitutionel = "SCDI de Montpellier";
	$gNominstitutionelcomplet = "Réseau des Bibliothèques Universitaire de Montpellier";
	$gLNominstitutionel = "le réseau des BU de Montpellier";
	$gLNominstitutionelcomplet = "le réseau des Bibliothèques Universitaire de Montpellier";
	$gURLsiteinstitutionel = "https://www.scdi-montpellier.fr/";
	
	// Service d'aide par défaut
	// URL d'aide par défaut
	$gAide['default']['nom'] = "Une question ?";
	$gAide['default']['url'] = "https://www.scdi-montpellier.fr/boomerang";
	
	// Service d'aide par id IDP
	// URL d'aide par id IDP					 
	$gAide['upv']['nom'] = "Une question ? Un.e bibliothécaire vous répond";
	$gAide['upv']['url'] = "https://bibliotheques.univ-montp3.fr/une-question/";
	
	$gAide['um']['nom'] = "UBIB, un.e bibliothécaire répond à vos questions";
	$gAide['um']['url'] = "https://ubib.libanswers.com/contactez-nous/";
	
	$gAide['enscm']['nom'] = "UBIB, un.e bibliothécaire répond à vos questions";
	$gAide['enscm']['url'] = "https://ubib.libanswers.com/contactez-nous/";
	
	//chemin vers favicon du site
	$gURLfavicon = "images/favicon.ico";
	
	//chemin vers logo en bas du site
	$gURLlogo = "images/logo.png";
	
	//chemin vers logo HD par défaut dans le PDF du Quitus
	$gURLlogoHD = "images/logo_hd/scdi.png";
	
	//dossier Logo HD dans PDF Quitus sans le '/' de fin -> les images auront la forme : nomdomaine.tld.png ou prefixe_identifiant_alma.png - elles doivent être copiée dans le dossier
	$gURLlogoHDPdf = "images/logo_hd";
	
	//chemin vers image du tampon dans PDF Quitus
	$gURLtampon = "images/tampon.png";
	
	//ville affichée dans PDF Quitus
	$gURLville = "Montpellier";
	
	//Nom ou TAG du blocage dans Alma en MAJUSCULE
	$gTagblocage = "QUITUS BU";
	
	//Nom du SP shibboleth déclaré dans simplesamlphp
	$gNomSP = "quitus-scdi";
	
	// config des serveurs IDP AML
	// serveur idp UPV
	$gIdp['upv']['id']="upv";
	$gIdp['upv']['server']="urn:mace:cru.fr:federation:univ-montp3.fr"; // Identifiant du serveur IDP
	$gIdp['upv']['url']=$gURLs."connexion-".$gIdp['upv']['id']; // URL de connexion directe : attention voir fichier .htaccess pour la redirection d'URL
	$gIdp['upv']['text']="Université Paul-Valéry Montpellier 3"; // Texte du bouton de connexion au serveur IDP
	$gIdp['upv']['buttoncolor']="background-color:#1f73ba;border:1px solid #eeeeee;"; // couleurs du bouton de connexion au serveur IDP
	$gIdp['upv']['active']=true;  // activer ou désactiver la connexion possible via ce serveur IDP
	
	// serveur idp UM
	$gIdp['um']['id']="um";
	$gIdp['um']['server']="https://federation.umontpellier.fr/idp/shibboleth";
	$gIdp['um']['url']=$gURLs."connexion-".$gIdp['um']['id'];
	$gIdp['um']['text']="Université de Montpellier";
	$gIdp['um']['buttoncolor']="background-color:#ff545d;border:1px solid #eeeeee;";
	$gIdp['um']['active']=true;
	
	// serveur idp ENSCM
	$gIdp['enscm']['id']="enscm";
	$gIdp['enscm']['server']="https://idp.enscm.fr/idp/shibboleth";
	$gIdp['enscm']['url']=$gURLs."connexion-".$gIdp['enscm']['id'];
	$gIdp['enscm']['text']="Ecole Nationale Supérieure de Chimie";
	$gIdp['enscm']['buttoncolor']="background-color:#8bc039;border:1px solid #db9d3c;";
	$gIdp['enscm']['active']=true;
	
	// clé de chiffrement opérations générales (32 caractères aléatoires à définir)
	$gKey = "12345678912345678912345678912345";
	
	// configuration des envois de mail
	$gMailAddFrom = "no-reply@quitus.scdi-montpellier.fr";
	$gMailNameFrom = "Quitus BU";
	$gMailSMTP = "smtp.scdi-montpellier.fr";				   
	
	// chemin sur le serveur de SimpleSAMLPhp
	$gCheminSSP = "/var/simplesamlphp/";
	
	// information de connexion à la base mysql
	// importer le script quitus.sql pour créer la base et la table nécessaire au bon fonctionnement de l'application (attention : mettre les bons droits utilisateur)
	$gaSql['user']       = "quitus";
	$gaSql['password']   = "xxxxxxxxxxxx";
	$gaSql['db']         = "quitus";
	$gaSql['server']     = "localhost";

	// script matomo (pour serveur de statistiques) - laisser la variable vide pour désactiver la fonctionnalité
	$gScriptMatomo =
	"
	<!-- Matomo -->
	<script>
	  var _paq = window._paq = window._paq || [];
	  /* tracker methods like \"setCustomDimension\" should be called before \"trackPageView\" */
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
		var u=\"https://stats.xxxxxxxxxxx.tld/\";
		_paq.push(['setTrackerUrl', u+'matomo.php']);
		_paq.push(['setSiteId', 'xxx']);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<!-- End Matomo Code -->
	";

?>