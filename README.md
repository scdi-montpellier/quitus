# Quitus Alma SCDI
Application �crite en PHP/JS/CSS pour la g�n�ration de Quitus dans l'application Alma Exlibris.

## Fonctionnement de l'application
L'utilisateur s'authentifie via son identifiant universitaire (SAML shibboleth) qui doit correspondre � un identifiant utilisateur dans Alma. Il peut aussi s'identifier directement avec son identifiant Alma (code-barres en g�n�ral) et sa date de naissance.
Si l'utilisateur n'a pas de blocage, de pr�ts en cours ou en retard, ou d'amende/frais, un mail avec un lien unique peut �tre envoy� sur le mail principal indiqu� dans le compte utilisateur Alma.
Ce lien valable 24h, permet de t�l�charger le Quitus sous forme de PDF. Une fois le Quitus t�l�charg�, un blocage est cr�� sur l'utilisateur Alma afin qu'il ne puisse plus faire d'op�ration sur son compte.
Le PDF contient un QRCode et un lien vers le serveur "Quitus" pour permettre de v�rifier la validit� du Quitus (v�rifie la pr�sence du code du Blocage sur l'utilisateur Alma).
Les interactions entre l'application Quitus et Alma sont faites � travers l'API RESTful Alma mis � disposition par Exlibris.

## Pr�-requis :
- Serveur Apache en HTTPS (test� sur CentOS v7.9 / Apache v2.4) avec module URLrewriting activ� 
- PHP v7.3 (non test� sur PHP v7.4+/8+)
- Serveur MariaDB v10.3
- 1 token Developer Network Exlibris pour utiliser l'API RESTful Alma Exlibris (https://developers.exlibrisgroup.com/ -> demander � exlibris d'associer votre compte developpeur � votre instance Alma)

### Autres d�pendances :
JS/CSS (via CDN - lien en dur dans le code) :
- utilisation de bootstrap (test� en v4.3) (https://getbootstrap.com/)
- utilisation de JQuery (test� en v1.12) (https://jquery.com/)
- utilisation de PopperJS (test� en v1.14) (https://popper.js.org/)
- utilisation de FontAwesome (test� en v4.7) (https://fontawesome.com/v4/icons/)
PHP (installation locale) :
- SimpleSAMLPHP (test� en v1.19) pour l'authentification via SAML (shibboleth) afin de r�cup�rer l'identifiant de l'utilisateur Alma (https://simplesamlphp.org/) : un SP doit �tre install� et fonctionnel pour fonctionner avec l'application Quitus.
- fpdf (test� en v1.8) : g�n�ration du quitus en pdf (http://www.fpdf.org/)
- phpqrcode (test� en v1.1) : g�n�ration d'un qrcode dans le quitus (http://phpqrcode.sourceforge.net/)
- PHPMailer (test� en v6.0) : envoi de mail pour t�l�charger le quitus (https://github.com/PHPMailer/PHPMailer)

## Installation
- Copier tous les fichiers de l'application dans le r�pertoire root d'apache
- Modifier le fichier config.php pour param�trer/personnaliser l'application (chemin, url, titre, logo, serveur base de donn�es, token Alma, serveur smtp, serveurs IDP SAML...) : voir exemple fourni
- Importer le script quitus.sql sur votre serveur MariaDB pour cr�er la base et la table n�cessaire � l'application (attention � bien mettre les bons droits utilisateur)
- Copier vos logos en png dans le dossier /images et /images/logo_hd -> respecter la nomenclature : nomdomaine.tld.png du compte universitaire/attribut eppn et/ou prefixe_identifiant_alma.png
- Laisser (ou cr�er) un dossier /temp � la racine du site pour la gestion des fichiers temporaires (l'utilisateur apache doit pouvoir �crire dans le dossier)
- T�l�chager fpdf et copier les fichiers dans le dossier /fpdf � la racine du site (nom dossier en dur dans le code)
- T�l�chager phpqrcode et copier les fichiers dans le dossier /phpqrcode � la racine du site (nom dossier en dur dans le code)
- T�l�chager PHPMailer et copier les fichiers dans le dossier /PHPMailer � la racine du site (nom dossier en dur dans le code)
- T�l�chager et installer SimpleSAMLPHP, configurer un SP dans l'application et tester que l'authentification fonctionne bien depuis SimpleSAMLPHP (il faut renseigner le chemin vers SimpleSAMLPHP et le nom du SP dans config.php).

## Note
- Application en fran�ais uniquement pour le moment
- Plusieurs serveurs IDP Shibboleth peuvent �tre utilis�s pour l'authentification des utilisateurs (voir fichier config.php)

## Licence
Quitus Alma SCDI est un logiciel libre sous license GNU GPL (voir fichier LICENSE / https://www.gnu.org/licenses/).
Aucun support n'est assur� pour le moment par le SCDI de Montpellier.