<?php
	// Site en maintenance
	//die("Site en maintenance, merci de revenir plus tard !");

	$gNomAppli = "Quitus BU";
	$gNomAppliCourt = 'Quitus';
	$gVersionAppli = '1.3';
	
	// Adresse serveur API exlibris
	$gAdrAlma = "https://api-eu.hosted.exlibrisgroup.com";
	
	// Token API Exlibris Alma
	$gTokenAlma = urlencode("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
	
	// url de l'appli
	$gURLs = "https://quitus.scdi-montpellier.fr/";
	// chemin de l'appli sur le serveur
	$gDossier = "/var/www/html/quitus-scdi/";
	
	// Labels institution
	$gNominstitutionel = "SCDI de Montpellier";
	$gNominstitutionelcourt = "BU";
	$gNominstitutionelcomplet = "Réseau des Bibliothèques Universitaire de Montpellier/Nîmes";
	$gLNominstitutionel = "le réseau des BU de Montpellier/Nîmes";
	$gLNominstitutionelcomplet = "le réseau des Bibliothèques Universitaire de Montpellier/Nîmes";
	$gURLsiteinstitutionel = "https://www.scdi-montpellier.fr/";
		
	// Geston authentification via serveurs IDP SAML
	// serveur idp UPV
	$gIdp['upv']['id']="upv";
	$gIdp['upv']['server']="urn:mace:cru.fr:federation:univ-montp3.fr"; // Identifiant du serveur IDP
	$gIdp['upv']['url']=$gURLs."connexion-".$gIdp['upv']['id']; // URL de connexion directe : attention voir fichier .htaccess pour la redirection d'URL
	$gIdp['upv']['buttontext']="Université Paul-Valéry Montpellier 3"; // Texte du bouton de connexion au serveur IDP
	$gIdp['upv']['buttoncolor']="background-color:#1f73ba;border:1px solid #eeeeee;"; // couleurs du bouton de connexion au serveur IDP
	$gIdp['upv']['active']=true;  // activer ou désactiver la connexion possible via ce serveur IDP
	
	// serveur idp UM
	$gIdp['um']['id']="um";
	$gIdp['um']['server']="https://federation.umontpellier.fr/idp/shibboleth";
	$gIdp['um']['url']=$gURLs."connexion-".$gIdp['um']['id'];
	$gIdp['um']['buttontext']="Université de Montpellier";
	$gIdp['um']['buttoncolor']="background-color:#ff545d;border:1px solid #eeeeee;";
	$gIdp['um']['active']=true;
		
	// serveur idp ENSCM
	$gIdp['enscm']['id']="enscm";
	$gIdp['enscm']['server']="https://idp.enscm.fr/idp/shibboleth";
	$gIdp['enscm']['url']=$gURLs."connexion-".$gIdp['enscm']['id'];
	$gIdp['enscm']['buttontext']="Ecole Nationale Supérieure de Chimie";
	$gIdp['enscm']['buttoncolor']="background-color:#8bc039;border:1px solid #db9d3c;";
	$gIdp['enscm']['active']=true;
		
	// serveur idp unimes
	$gIdp['unimes']['id']="unimes";
	$gIdp['unimes']['server']="https://federation.unimes.fr/idp/shibboleth";
	$gIdp['unimes']['url']=$gURLs."connexion-".$gIdp['unimes']['id'];
	$gIdp['unimes']['buttontext']="Université de Nîmes";
	$gIdp['unimes']['buttoncolor']="background-color:#f59e00;border:1px solid #ffc826;";
	$gIdp['unimes']['active']=true;
	
	// Correspondance domaine de l'eppn via idp -> permet de trouver l'établissement
	$gDomaine['univ-montp3.fr']='upv';
	$gDomaine['etu.univ-montp3.fr']='upv';
	$gDomaine['umontpellier.fr']='um';
	$gDomaine['etu.umontpellier.fr']='um';
	$gDomaine['enscm.fr']='enscm';
	$gDomaine['unimes.fr']='unimes';
	$gDomaine['etudiant.unimes.fr']='unimes';
	
	// Informations sur les établissements disponibles
	// UPVM3
	$gEtablissement['upv']['cb_regex']="/UM3[a-z]+[0-9]*/mi"; //Regex pour reconnaitre le Code-barres de l'utilisateur pour cet IDP : UM3xyz, UM3xyzl, UM3xyzxyzxyz9
	$gEtablissement['upv']['tampon']="images/tampon/upv.png";
	$gEtablissement['upv']['logo']="images/logo/upv.png"; // Affichage logos bas de page
	$gEtablissement['upv']['logo_hd']="images/logo_hd/upv.png";
	$gEtablissement['upv']['ville']="Montpellier";	
	$gEtablissement['upv']['urlsiteinstitutionel'] = "https://www.univ-montp3.fr/";
	$gEtablissement['upv']['textelien'] = "Site de l'Université Paul-Valéry Montpellier 3";
	$gEtablissement['upv']['logovisible'] = 1; // activer ou désactiver l'affichage du logo
	$gEtablissement['upv']['aide_nom'] = "Une question ? Un.e bibliothécaire vous répond"; // nom site aide dès qu'un serveur IDP est utilisé
	$gEtablissement['upv']['aide_url'] = "https://bibliotheques.univ-montp3.fr/une-question/"; // URL d'aide dès qu'un serveur IDP est utilisé
	
	// UMONTPELLIER
	$gEtablissement['um']['cb_regex']="/UM[p|1-9]{1}[A]?[0-9]+|FDE[0-9]+/mi"; //Regex pour reconnaitre le Code-barres de l'utilisateur pour cet IDP : UMp00000123456,UM1A0012345,UM22221234,UM30012345,FDE12345
	$gEtablissement['um']['tampon']="images/tampon/um.png";
	$gEtablissement['um']['logo']="images/logo/um.png";
	$gEtablissement['um']['logo_hd']="images/logo_hd/um.png";
	$gEtablissement['um']['ville']="Montpellier";	
	$gEtablissement['um']['urlsiteinstitutionel'] = "https://www.umontpellier.fr/";
	$gEtablissement['um']['textelien'] = "Site de l'Université de Montpellier";
	$gEtablissement['um']['logovisible'] = 1; // activer ou désactiver l'affichage du logo
	$gEtablissement['um']['aide_nom'] = "UBIB, un.e bibliothécaire répond à vos questions";
	$gEtablissement['um']['aide_url'] = "https://ubib.libanswers.com/contactez-nous/";
	
	// ENSCM
	$gEtablissement['enscm']['cb_regex']="/ECM[0-9]+/mi"; //Regex pour reconnaitre le Code-barres de l'utilisateur pour cet IDP : ECM21800150,ECM019275
	$gEtablissement['enscm']['tampon']="images/tampon/enscm.png";
	$gEtablissement['enscm']['logo']="images/logo/enscm.png";
	$gEtablissement['enscm']['logo_hd']="images/logo_hd/enscm.png";
	$gEtablissement['enscm']['ville']="Montpellier";
	$gEtablissement['enscm']['urlsiteinstitutionel'] = "https://www.enscm.fr/";
	$gEtablissement['enscm']['textelien'] = "Site de l'ENSCM";
	$gEtablissement['enscm']['logovisible'] = 0; // activer ou désactiver l'affichage du logo
	$gEtablissement['enscm']['aide_nom'] = "UBIB, un.e bibliothécaire répond à vos questions";
	$gEtablissement['enscm']['aide_url'] = "https://ubib.libanswers.com/contactez-nous/";
	
	// UNIMES
	$gEtablissement['unimes']['cb_regex']="/UN[0-9]+/mi"; //Regex pour reconnaitre le Code-barres de l'utilisateur pour cet IDP : UN12345678,UN87654321
	$gEtablissement['unimes']['tampon']="images/tampon/unimes.png";
	$gEtablissement['unimes']['logo']="images/logo/unimes.png";
	$gEtablissement['unimes']['logo_hd']="images/logo_hd/unimes.png";
	$gEtablissement['unimes']['ville']="Nîmes";
	$gEtablissement['unimes']['urlsiteinstitutionel'] = "https://www.unimes.fr/";
	$gEtablissement['unimes']['textelien'] = "Site de l'Université de Nîmes";
	$gEtablissement['unimes']['logovisible'] = 1; // activer ou désactiver l'affichage du logo
	$gEtablissement['unimes']['aide_nom'] = "Une question? Une réponse !";
	$gEtablissement['unimes']['aide_url'] = "https://demat.unimes.fr/se-connecter-au-service?sector=3&service=33";
	
	// Default, à mettre à la fin
	$gEtablissement['default']['cb_regex']="/.+/mi";	 //accepte tous code-barres
	$gEtablissement['default']['tampon']="images/tampon/scdi.png";
	$gEtablissement['default']['logo']="images/logo/scdi.png";
	$gEtablissement['default']['logo_hd']="images/logo_hd/scdi.png";
	$gEtablissement['default']['ville']="Montpellier";
	$gEtablissement['default']['urlsiteinstitutionel'] = "https://www.scdi-montpellier.fr/";
	$gEtablissement['default']['textelien'] = "Site du SCDI de Montpellier";
	$gEtablissement['default']['logovisible'] = 0; // activer ou désactiver l'affichage du logo
	$gEtablissement['default']['aide_nom'] = "Une question ?"; // nom site aide par défaut
	$gEtablissement['default']['aide_url'] = "https://www.scdi-montpellier.fr/boomerang"; // URL d'aide par défaut
	
	// Nom ou TAG du blocage dans Alma en MAJUSCULE
	$gTagblocage = "QUITUS BU";
	
	// chemin vers favicon du site
	$gURLfavicon = "images/favicon.ico";
	
	// Chemin sur le serveur de SimpleSAMLPhp
	$gCheminSSP = "/var/simplesamlphp/";
	
	// Nom du SP shibboleth/SAML déclaré dans SimpleSAMLPhp
	$gNomSP = "quitus-scdi";
	
	// Clé de chiffrement opérations générales notamment pour les liens uniques (32 caractères aléatoires à définir)
	$gKey = "12345678912345678912345678912345";
	
	// Configuration des envois de mail (utilisation d'un serveur SMTP)
	$gMailAddFrom = "no-reply@xxxxxxxxxxx.tld";
	$gMailNameFrom = "Quitus BU";
	$gMailSMTP = "smtp.xxxxxxxxxxx.tld";				   

	// Information de connexion à la base mysql
	// importer le script quitus.sql pour créer la base et la table nécessaire au bon fonctionnement de l'application (attention : mettre les bons droits utilisateur MariaBD), puis supprimer le fichier quitus.sql du serveur
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