<?php
/********************************************************
        lang/eng/prefs.inc
        (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

        This file is part of IlohaMail, and released under GPL.
        See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated: David Gimeno i Ayuso info@sima-pc.com

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Prefer&egrave;ncies";
$prefHeaderStrings[1] = "General";
$prefHeaderStrings[3] = "Identitats";
$prefHeaderStrings[2] = "Aparen&ccedil;a";

											
$prefsStrings["0.0"] = "Identitat predefinida";
$prefsStrings["0.1"] = "Nom: ";
$prefsStrings["0.2"] = "Correu: ";
$prefsStrings["0.3"] = "Els canvis es poden fer a la p&agrave;gina \"%s\".";
$prefsStrings["0.4"] = "No especificat";
$prefsStrings["1.0"] = "Internacional";
$prefsStrings["1.1"] = "Interf&iacute;cie: ";
$prefsStrings["1.2"] = "Joc de car&agrave;cters: ";
$prefsStrings["1.3"] = "Fus horari: ";
$prefsStrings["1.4"] = "Opci&oacute; hor&agrave;ria: ";
$prefsStrings["1.5"] = array(12=>"12 hores", 24=>"24 hores");
$prefsStrings["2.0"] = "Llistar opcions de visualitzaci&oacute;";
$prefsStrings["2.1"] = "Mostrar fins ";
$prefsStrings["2.2"] = " elements";
$prefsStrings["2.3"] = "Mostrar tamany";
$prefsStrings["2.4"] = "Ordenar per: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ordre de classificaci&oacute;: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mostrar llista de carpetes";
$prefsStrings["2.9"] = "Amplada de la llista de carpetes";
$prefsStrings["2.10"] = "Comprovar els missatges nous cada %n segons";
$prefsStrings["2.11"] = "Mostrar barra d'eines a %m";
$prefsStrings["2.12"] = array("b"=>"davall", "t"=>"adalt", "bt"=>"adalt i davall");
$prefsStrings["2.13"] = "Reorganitzar columnes: ";
$prefsStrings["2.14"] = "cliqueu aqu&iacute;";
$prefsStrings["3.0"] = "Desar les opcions autom&agrave;ticament";
$prefsStrings["3.1"] = "Guardar elements enviats.";
$prefsStrings["3.2"] = "Desar els missatges enviats a la carpeta: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Buidar la paperera autom&agrave;ticament";
$prefsStrings["3.5"] = "Moure els elements esborrats a la carpeta: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Directori arrel: ";
$prefsStrings["3.8"] = "Altres";
$prefsStrings["3.9"] = "Ocultar carpetes no subscrites";
$prefsStrings["3.10"] = "Mostrar quota %m";
$prefsStrings["3.11"] = array(""=>"no mostrar", "m"=>"al llistat de missatges", "f"=>"a la llista de carpetes");
$prefsStrings["4.0"] = "Opcions de visualitzaci&oacute; de missatges";
$prefsStrings["4.1"] = "Visualitzar en marc";
$prefsStrings["4.2"] = "Acolorir cites";
$prefsStrings["4.3"] = "Activar aquesta opci&oacute; pot ralentitzar el sistema.";
$prefsStrings["4.4"] = "Convertir URL a enlla&ccedil;os";
$prefsStrings["4.5"] = "Mostrar missatges HTML";
$prefsStrings["4.6"] = "Mostrar imatges inserides";
$prefsStrings["4.7"] = "Mostrar enlla&ccedil;os de navegaci&oacute;";
$prefsStrings["4.8"] = "No marcar els missatges en rec&oacute;rrer-los";
$prefsStrings["5.0"] = "Signatura";
$prefsStrings["5.1"] = "Mostrar per defecte la signatura";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Redactar";
$prefsStrings["6.1"] = "Mostrar contactes";
$prefsStrings["6.2"] = "Mostrar camps C&ograve;pia/C&ograve;pia oculta";
$prefsStrings["6.3"] = "Tancar despr&eacute;s d'enviar";
$prefsStrings["6.4"] = "Composar dins el marc";
$prefsStrings["7.0.0"] = "Opcions de disc i carpeta";
$prefsStrings["7.0.1"] = "Opcions de quota";
$prefsStrings["7.1"] = "Mostrar nombre de no llegits";
$prefsStrings["7.2"] = "Refrescar llista de carpetes cada %n segons";

$pref_col_title = "Reorganitzar columnes";
$pref_colstr["order"] = "Ordre";
$pref_colstr["field"] = "Camp";
$pref_col_label['c'] = "Quadre de verificaci&oacute;";
$pref_col_label['a'] = "Annexos";
$pref_col_label['m'] = "Senyaladors/estat";

$sort_fields=array("DATE"=>"Data", "SUBJECT"=>"Tema", "SIZE"=>"Tamany");
$sort_orders=array("ASC"=>"Ascendent", "DESC"=>"Descendent");

$prefsButtonStrings = array( 0=> "Actualitzar", 1=>"Aplicar", 2=> "Cancel&middot;lar", 3=> "Predeterminat");

$prefs_new_user = "Sembla ser que hi heu entrat per primer cop...benvingut!<br>"
$prefs_new_user .= "Repasseu els par&agrave;metres seg&uuml;ents i polseu \"Aplicar\"";
?>
