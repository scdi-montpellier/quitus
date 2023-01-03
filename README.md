# Quitus Alma SCDI
Application écrite en PHP/JS/CSS pour la génération de quitus dans l'application Alma Exlibris.

## Fonctionnement de l'application
L'utilisateur s'authentifie via son identifiant universitaire (SAML shibboleth) qui doit correspondre à un identifiant utilisateur dans Alma. Il peut aussi s'identifier directement avec son identifiant Alma (code-barres) et sa date de naissance.
Si l'utilisateur n'a pas de blocage, de prêts en cours ou en retard, ou d'amende/frais, un mail avec un lien unique peut être envoyé sur le mail principal indiqué dans le compte utilisateur Alma.
Ce lien valable 24h, permet de télécharger le Quitus sous forme de PDF. Une fois le lien téléchargé, un blocage est créé sur l'utilisateur Alma.
Le PDF contient un QRCode et un lien vers le serveur "Quitus" pour permettre de vérifier la validité du Quitus (vérifie la présence du code du Blocage sur l'utilisateur Alma).

## Pré-requis :
- Serveur Apache en HTTPS (testé sur CentOS v7.9 / Apache v2.4) avec module URLrewriting activé 
- PHP v7.3 (non testé sur PHP v7.4+/8+)
- Serveur MariaDB v10.3
- 1 token Developer Network Exlibris pour utiliser l'API Alma Exlibris (https://developers.exlibrisgroup.com/ -> demander à exlibris d'associer votre compte developpeur à votre instance Alma)

### Autres dépendances :
JS/CSS (via CDN) :
- utilisation de bootstrap (testé en v4.3) (https://getbootstrap.com/)
- utilisation de JQuery (testé en v1.12) (https://jquery.com/)
- utilisation de PopperJS (testé en v1.14) (https://popper.js.org/)
- utilisation de FontAwesome (testé en v4.7) (https://fontawesome.com/v4/icons/)
PHP (installation locale) :
- SimpleSAMLPHP (testé en v1.19) pour l'authentification via SAML (shibboleth) afin de récupérer l'identifiant de l'utilisateur Alma (https://simplesamlphp.org/) : un SP doit être installé et fonctionnel pour fonctionner avec l'application Quitus.
- fpdf (testé en v1.8) : génération du quitus en pdf (http://www.fpdf.org/)
- phpqrcode (testé en v1.1) : génération d'un qrcode dans le quitus (http://phpqrcode.sourceforge.net/)
- PHPMailer (testé en v6.0) : envoi de mail pour télécharger le quitus (https://github.com/PHPMailer/PHPMailer)

## Installation
- Copier tous les fichiers de l'application dans le répertoire root d'apache
- Modifier le fichier config.php pour paramètrer/personnaliser l'application (url, titre, logo, base de données, token Alma, serveur smtp, serveurs IDP SAML...) : voir exemple fourni
- Importer le script quitus.sql sur votre serveur MariaDB pour créer la base et la table nécessaire à l'application (attention à bien mettre les bons droits utilisateur)
- Copier vos logos en png dans le dossier /images et /images/logo_hd (respecter la nomenclature : nomdomaine.tld.png ou prefixe_identifiant_alma.png)
- Laisser (ou créer) un dossier temp à la racine du site pour la gestion des fchiers temporaires

## Note
- Application en français uniquement pour le moment
- Plusieurs serveurs IDP Shibboleth peuvent être utilisés pour l'authentification des utilisateurs (voir fichier config.php)

## Licence
Quitus Alma SCDI est un logiciel libre sous license GNU GPL (voir fichier LICENSE / https://www.gnu.org/licenses/).
Aucun support n'est assuré pour le moment par le SCDI de Montpellier.