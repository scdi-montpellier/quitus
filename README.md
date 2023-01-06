# Quitus Alma SCDI
**Quitus Alma SCDI** est une application écrite en PHP/JS/CSS qui permet de générer un Quitus en PDF et de bloquer l'utilisateur dans l'application Alma Exlibris.

## Fonctionnement de l'application
L'utilisateur s'authentifie via son identifiant universitaire (SAML shibboleth) qui doit correspondre à un identifiant utilisateur dans Alma. Il peut aussi s'identifier directement avec son identifiant Alma (code-barres en général) et sa date de naissance.
Si l'utilisateur n'a pas de blocage, de prêts en cours ou en retard, ou d'amende/frais, un mail avec un lien unique peut être envoyé sur le mail principal indiqué dans le compte utilisateur Alma.
Ce lien valable 24h, permet de télécharger le Quitus sous forme de PDF. Une fois le Quitus téléchargé, un blocage est créé sur l'utilisateur Alma afin qu'il ne puisse plus faire d'opération sur son compte.
Le PDF contient un QRCode et un lien vers le serveur "Quitus" pour permettre de vérifier la validité du Quitus (vérifie la présence du code du Blocage sur l'utilisateur Alma).
Les interactions entre l'application Quitus et Alma sont faites à travers l'API RESTful Alma mis à disposition par Exlibris.

## Pré-requis :
- Serveur Apache en HTTPS (testé sur CentOS v7.9 / Apache v2.4) avec module URLrewriting activé 
- PHP v7.3 (non testé sur PHP v7.4+/8+)
- Serveur MariaDB v10.3 (non testé sur version supérieure)
- 1 token Developer Network Exlibris pour utiliser l'API RESTful Alma Exlibris (https://developers.exlibrisgroup.com/ -> demander à exlibris d'associer votre compte developpeur à votre instance Alma, créer un token dans - Manage API Keys - pour l'application Alma avec les bons droits : Users - Read/write)

### Autres dépendances :
#### JS/CSS (via CDN - lien en dur dans le code) :
- utilisation de bootstrap (testé en v4.3) (https://getbootstrap.com/)
- utilisation de JQuery (testé en v1.12) (https://jquery.com/)
- utilisation de PopperJS (testé en v1.14) (https://popper.js.org/)
- utilisation de FontAwesome (testé en v4.7) (https://fontawesome.com/v4/icons/)
#### PHP (installation locale) :
- SimpleSAMLPHP (testé en v1.19) pour l'authentification via SAML (shibboleth) afin de récupérer l'identifiant de l'utilisateur Alma (https://simplesamlphp.org/) : un SP doit être installé et fonctionnel pour fonctionner avec l'application Quitus.
- fpdf (testé en v1.8) : génération du quitus en pdf (http://www.fpdf.org/)
- phpqrcode (testé en v1.1) : génération d'un qrcode dans le quitus (http://phpqrcode.sourceforge.net/)
- PHPMailer (testé en v6.0) : envoi de mail pour télécharger le quitus (https://github.com/PHPMailer/PHPMailer). Un serveur d'envoi de mail SMTP doit être disponible (sur port tcp/25 sans chiffrement et authentification) pour pouvoir envoyer le mail de demande de quitus à l'utilisateur.

## Installation
- Copier tous les fichiers de l'application dans le répertoire root d'apache
- Modifier le fichier config.php pour paramètrer/personnaliser l'application (chemin, url, titre, logo, serveur base de données, token Alma, serveur smtp, serveurs IDP SAML...) : voir exemple fourni
- Importer le script quitus.sql sur votre serveur MariaDB pour créer la base et la table nécessaire à l'application (attention à bien mettre les bons droits utilisateur). Supprimer le fichier quitus.sql du serveur.
- Copier vos logos en png dans le dossier /images et /images/logo_hd -> respecter la nomenclature : nomdomaine.tld.png du compte universitaire/attribut eppn et/ou prefixe_identifiant_alma.png - Mettre des logos de taille comparable aux exemples fournis (l'appli de redimensionne pas la taille des logos)
- Créer un dossier /temp à la racine du site pour la gestion des fichiers temporaires (l'utilisateur apache doit pouvoir écrire dans le dossier)
- Téléchager fpdf et copier les fichiers dans un dossier /fpdf à la racine du site (nom dossier en dur dans le code)
- Téléchager phpqrcode et copier les fichiers dans un dossier /phpqrcode à la racine du site (nom dossier en dur dans le code)
- Téléchager PHPMailer et copier les fichiers dans un dossier /PHPMailer à la racine du site (nom dossier en dur dans le code)
- Téléchager et installer SimpleSAMLPHP, configurer un SP dans l'application et tester que l'authentification fonctionne bien depuis l'interface SimpleSAMLPHP (il faut renseigner le chemin vers SimpleSAMLPHP et le nom du SP dans config.php).

## Notes
- Application en français uniquement pour le moment
- Plusieurs serveurs IDP Shibboleth peuvent être utilisés pour l'authentification des utilisateurs (voir fichier config.php)
- Il n'est pas possible de configurer le port (par défaut : tcp/25), le chiffrement (par défaut : sans) et l'authentification (par défaut : sans) pour le serveur SMTP à utiliser directement dans config.php

## Licence
Quitus Alma SCDI est un logiciel libre sous licence GNU GPL (voir fichier LICENSE / https://www.gnu.org/licenses/).
  
Aucun support n'est assuré pour le moment par le SCDI de Montpellier mais si vous utilisez notre application pour votre établissement, nous serions content de le savoir !

## Captures
- Accueil :
  
![Accueil](https://quitus.scdi-montpellier.fr/capture/1.png)

- Après authentification :
  
![Après authentification](https://quitus.scdi-montpellier.fr/capture/2.png)
