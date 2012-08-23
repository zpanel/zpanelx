<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/ga/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Preferencias";
$prefHeaderStrings[1] = "Xeral";
$prefHeaderStrings[3] = "Identidades"; 
$prefHeaderStrings[2] = "Cores";

											
$prefsStrings["0.0"] = "Identidade por defecto";
$prefsStrings["0.1"] = "Nome: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Pode cambiarse na páxina \"%s\".";
$prefsStrings["0.4"] = "Non especificado";

$prefsStrings["1.0"] = "Internacional";
$prefsStrings["1.1"] = "Idioma: ";
$prefsStrings["1.2"] = "Caracteres: ";
$prefsStrings["1.3"] = "Zona Horaria: ";
$prefsStrings["1.4"] = "Modo Horario: "; 
$prefsStrings["1.5"] = array(12=>"12 horas", 24=>"24 horas");
$prefsStrings["2.0"] = "Opcións de Vista de Lista";
$prefsStrings["2.1"] = "Amosar ata ";
$prefsStrings["2.2"] = " elementos";
$prefsStrings["2.3"] = "Amosar tamaño";
$prefsStrings["2.4"] = "Ordear por: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Dirección: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Amosar lista de carpetas";
$prefsStrings["2.9"] = "Ancho da lista de Carpetas";
$prefsStrings["2.10"] = "Buscar novas mensaxes cada %n segundos"; 
$prefsStrings["2.11"] = "Amosar barra de ferramentas %m"; 
$prefsStrings["2.12"] = array("b"=>"arriba", "t"=>"abaixo", "bt"=>"arriba e abaixo");
$prefsStrings["2.13"] = "Reordear columnas: "; 
$prefsStrings["2.14"] = "prema aquí"; 
$prefsStrings["3.0"] = "Opcións de Auto-Gardado";
$prefsStrings["3.1"] = "Gardar elementos enviados.";
$prefsStrings["3.2"] = "Gardar elementos enviados na carpeta: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Borrar Eliminados automaticamente";
$prefsStrings["3.5"] = "Mover elementos eliminados na carpeta: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Directorio Raíz: ";
$prefsStrings["3.8"] = "Outro";
$prefsStrings["3.9"] = "Esconder carpetas non subscritas";
$prefsStrings["3.10"] = "Amosar cuota %m";
$prefsStrings["3.11"] = array(""=>"non amosar", "m"=>"na lista de mensaxes", "f"=>"na lista de carpetas");
$prefsStrings["4.0"] = "Opcións de Vista de Mensaxes";
$prefsStrings["4.1"] = "Ver nun marco";
$prefsStrings["4.2"] = "Colorear citas";
$prefsStrings["4.3"] = "Esta opción pode ralentiza-lo sistema.";
$prefsStrings["4.4"] = "Converter URL a ligazóns";
$prefsStrings["4.5"] = "Amosar mensaxes HTML";
$prefsStrings["4.6"] = "Amosar imaxes nas mensaxes";
$prefsStrings["4.7"] = "Amosar elementos de navegación";
$prefsStrings["4.8"] = "Non marcar mensaxes ó pasar por elas con Anterior/Seguinte";

$prefsStrings["5.0"] = "Sinatura";
$prefsStrings["5.1"] = "Amosar sinatura por defecto";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Escribir";
$prefsStrings["6.1"] = "Amosar contactos";
$prefsStrings["6.2"] = "Amosar campos CC/BCC";
$prefsStrings["6.3"] = "Pechar despois de enviar";
$prefsStrings["6.4"] = "Escribir nun marco";
$prefsStrings["7.0.0"] = "Opcións de Disco e Carpeta";
$prefsStrings["7.0.1"] = "Opcións de Cuota";
$prefsStrings["7.1"] = "Amosar o número de mensaxes non lidos";
$prefsStrings["7.2"] = "Actualizar lista de carpeta cada %n segundos";

$pref_col_title = "Reordear columnas";
$pref_colstr["order"] = "Orde";
$pref_colstr["field"] = "Campo";
$pref_col_label['c'] = "Selección";
$pref_col_label['a'] = "Adxuntos";
$pref_col_label['m'] = "Estado";

$sort_fields=array("DATE"=>"Data", "SUBJECT"=>"Asunto", "SIZE"=>"Tamaño");
$sort_orders=array("ASC"=>"Ascendente", "DESC"=>"Descendente");

$prefsButtonStrings = array( 0=> "Actualizar", 1=>"Aplicar", 2=> "Cancelar", 3=> "Predefinidos");

$prefs_new_user = "Parece que é a primeira vez que fai login... ¡benvido!<br>";
$prefs_new_user .= "Por favor, configure os seguintes datos e prema \"Aplicar\"";
?>
