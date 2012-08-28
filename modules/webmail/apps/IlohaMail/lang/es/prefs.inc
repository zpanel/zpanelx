<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/es/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Preferencias";
$prefHeaderStrings[1] = "General";
$prefHeaderStrings[3] = "Identidades";
$prefHeaderStrings[2] = "Apariencia";


$prefsStrings["0.0"] = "Identidad predeterminada";
$prefsStrings["0.1"] = "Nombre: ";
$prefsStrings["0.2"] = "Correo: ";
$prefsStrings["0.3"] = "Se pueden hacer cambios en la pagina de \"%s\".";
$prefsStrings["0.4"] = "No especificado";
$prefsStrings["1.0"] = "Internacional";
$prefsStrings["1.1"] = "Interfaz: ";
$prefsStrings["1.2"] = "Caracteres: ";
$prefsStrings["1.3"] = "Horario: ";
$prefsStrings["1.4"] = "Opciones de Tiempo: ";
$prefsStrings["1.5"] = array(12=>"Reloj de 12 horas", 24=>"Reloj de 24 horas");
$prefsStrings["2.0"] = "Opciones de vista de lista";
$prefsStrings["2.1"] = "Mostrar hasta ";
$prefsStrings["2.2"] = " elementos";
$prefsStrings["2.3"] = "Mostrar tamaño";
$prefsStrings["2.4"] = "Ordenar por: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Orden: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mostrar lista de carpetas";
$prefsStrings["2.9"] = "Ancho del listado de Carpetas";
$prefsStrings["2.10"] = "Revisar nuevos mensajes cada %n segundos";
$prefsStrings["2.11"] = "Mostrar barra de herramientas %m";
$prefsStrings["2.12"] = array("b"=>"abajo", "t"=>"arriba", "bt"=>"arriba y abajo");
$prefsStrings["2.13"] = "Orden de las columnas: ";
$prefsStrings["2.14"] = "hacer click aquí";
$prefsStrings["3.0"] = "Opciones de Auto-Guardado";
$prefsStrings["3.1"] = "Guardar elementos enviados.";
$prefsStrings["3.2"] = "Guardar elementos enviados en la carpeta: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Automaticamente eliminar basura";
$prefsStrings["3.5"] = "Mover elementos eliminados a la carpeta: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Directorio Raiz: ";
$prefsStrings["3.8"] = "Otro";
$prefsStrings["3.9"] = "Ocultar carpetas no suscriptas";
$prefsStrings["3.10"] = "Mostrar cuota %m";
$prefsStrings["3.11"] = array(""=>"no mostrar", "m"=>"en listado mensajes", "f"=>"en listado carpetas");
$prefsStrings["4.0"] = "Opciones de ver mensaje";
$prefsStrings["4.1"] = "Ver dentro del marco";
$prefsStrings["4.2"] = "Poner color a comillas";
$prefsStrings["4.3"] = "Habilitar esta opción puede enlentecer el sistema.";
$prefsStrings["4.4"] = "Convertir URLs a enlaces";
$prefsStrings["4.5"] = "Mostrar mensajes en HTML";
$prefsStrings["4.6"] = "Mostrar imágenes en el contexto";
$prefsStrings["4.7"] = "Mostrar flechas de navegación";
$prefsStrings["4.8"] = "No marcar mensajes cuando se navega por ellos";
$prefsStrings["5.0"] = "Firma";
$prefsStrings["5.1"] = "Siempre agregar firma en los mensajes que envía";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Composición";
$prefsStrings["6.1"] = "Mostrar contactos";
$prefsStrings["6.2"] = "Mostrar campos CC/BCC";
$prefsStrings["6.3"] = "Cerrar luego de enviar";
$prefsStrings["6.4"] = "Componer dentro del marco";
$prefsStrings["7.0.0"] = "Opciones de Disco &amp; Carpetas";
$prefsStrings["7.0.1"] = "Opciones de Cuota";
$prefsStrings["7.1"] = "Mostrar cantidad de no leídos";
$prefsStrings["7.2"] = "Refrescar la lista de carpetas cada %n segundos";


$sort_fields=array("DATE"=>"Fecha", "SUBJECT"=>"Asunto", "SIZE"=>"Tamaño");
$sort_orders=array("ASC"=>"Ascendente", "DESC"=>"Descendente");

$prefsButtonStrings = array( 0=> "Actualizar", 1=>"Aplicar", 2=> "Revertir", 3=> "Predefinido");

$prefs_new_user = "Parece ser que es la primera vez que inicias sesión. ¡Bienvenido!<br>";
$prefs_new_user .= "Por favor, configura tu cuenta y posteriormente presiona \"Aplicar\"";
?>
