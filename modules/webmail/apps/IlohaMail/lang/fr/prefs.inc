<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/fr/prefs.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Préférences";
$prefHeaderStrings[1] = "Générales";
$prefHeaderStrings[2] = "Couleurs";
$prefHeaderStrings[3] = "Identités";
$prefHeaderStrings[4] = "Filtres";
											
$prefsStrings["0.0"] = "Identité principale";
$prefsStrings["0.1"] = "Nom: ";
$prefsStrings["0.2"] = "Courriel: ";
$prefsStrings["0.3"] = "Les changements peuvent s'effectuer dans la page \"%s\".";
$prefsStrings["0.4"] = "Non spécifié";
$prefsStrings["1.0"] = "Paramètres internationaux";
$prefsStrings["1.1"] = "Langue: ";
$prefsStrings["1.2"] = "Jeu de caractères: ";
$prefsStrings["1.3"] = "Fuseau horaire: ";
$prefsStrings["1.4"] = "Affichage ";
$prefsStrings["1.5"] = array(12=>"sur 12 heures", 24=>"sur 24 heures");
$prefsStrings["2.0"] = "Options de visualisation";
$prefsStrings["2.1"] = "Afficher jusqu'à ";
$prefsStrings["2.2"] = " Objets";
$prefsStrings["2.3"] = "Afficher la taille des fichiers";
$prefsStrings["2.4"] = "Champ du tri: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ordre du tri: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Afficher la liste des dossiers";
$prefsStrings["2.9"] = "Largeur de la liste des dossiers";
$prefsStrings["2.10"] = "Relever le courrier toutes les %n secondes";
$prefsStrings["2.11"] = "Afficher la barre d'outils %m";
$prefsStrings["2.12"] = array("b"=>"en bas", "t"=>"en haut","bt"=>"en haut et en bas"); 
$prefsStrings["2.13"] = "Réorganiser les colonnes: ";
$prefsStrings["2.14"] = "cliquer ici ";


$prefsStrings["3.0"] = "Sauvegarde automatique";
$prefsStrings["3.1"] = "Conserver les courriels envoyés.";
$prefsStrings["3.2"] = "Conserver les messages envoyés dans le dossier: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Vider la corbeille automatiquement";
$prefsStrings["3.5"] = "Déplacer les messages supprimés dans le dossier: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Dossier principal (racine): ";
$prefsStrings["3.8"] = "Autre chemin";
$prefsStrings["3.9"] = "Cacher les dossiers sans abonnement";
$prefsStrings["3.10"] = "Affichage des quotas %m";
$prefsStrings["3.11"] = array(""=>"ne pas afficher","m"=>"dans la liste des messages","f"=>"dans la liste des dossiers");


$prefsStrings["4.0"] = "Options pour l'affichage des messages";
$prefsStrings["4.1"] = "Visualiser dans une fenêtre";
$prefsStrings["4.2"] = "Mettre les citations en couleur";
$prefsStrings["4.3"] = "Cette option peut ralentir votre système.";
$prefsStrings["4.4"] = "Convertir les URLs en liens";
$prefsStrings["4.5"] = "Afficher les messages HTML";
$prefsStrings["4.6"] = "Afficher les images";
$prefsStrings["4.7"] = "Afficher les URLs";
$prefsStrings["4.8"] = "Ne pas marquer les messages ...";
$prefsStrings["5.0"] = "Signature";
$prefsStrings["5.1"] = "Afficher la signature par défaut.";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Rédiger";
$prefsStrings["6.1"] = "Afficher les contacts";
$prefsStrings["6.2"] = "Afficher CC/BCC";
$prefsStrings["6.3"] = "Fermer la fenêtre après l'envoi";
$prefsStrings["6.4"] = "Rédiger dans une fenêtre";
$prefsStrings["7.0.0"] = "Disque &amp; options des dossiers";
$prefsStrings["7.0.1"] = "Options de quotas";
$prefsStrings["7.1"] = "Afficher le nombre de messages non lus";
$prefsStrings["7.2"] = "Rythme de rafraîchissement des dossiers: %n secondes";

$pref_col_title = "Réorganiser les colonnes";
$pref_colstr["order"] = "Ordre";
$pref_colstr["field"] = "Champs";
$pref_col_label['c'] = "Cases à cocher";
$pref_col_label['a'] = "Pièces jointes";
$pref_col_label['m'] = "Indicateurs d'état";

$sort_fields=array("DATE"=>"Date", "SUBJECT"=>"Sujet", "SIZE"=>"Taille");
$sort_orders=array("ASC"=>"Croissant", "DESC"=>"Décroissant");

$prefsButtonStrings = array( 0=>"Enregistrer", 1=>"Activer", 2=>"Annuler", 3=>"Valeurs par défaut");

$prefs_new_user = "C'est votre première visite? Bienvenue !<br>";
$prefs_new_user .= "Veuillez remplir ce formulaire et cliquer sur \"Enregistrer\"";
?>
