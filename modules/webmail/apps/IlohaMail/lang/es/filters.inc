<?php
/********************************************************
	lang/eng/filters.inc
	(C)2001-2004 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/filters.inc");

# prefs.inc:$prefHeaderStrings[4] = $filter_title["filters"] = "Filters";

/*
$condition[] fields: "operand", "field", "bool", "condition", "subject"
*/
$fltr["filter"] = "Filtro";
$fltr["conditions"] = "Condiciones";
$fltr["actions"] = "Acciones";
$fltr["auto"] = "Procesar correo nuevo automáticamente";
$fltr["deletemsg"] = "Borrar mensaje";
$fltr["&"] = "y";
$fltr["|"] = "o";
$fltr["^"] = "comienza con";
$fltr["<"] = "contiene";
$fltr["$"] = "termina con";
$fltr["="] = "cuando";
$fltr["!"] = "cuando no";
$fltr["field"] = "Campo";
$fltr["condition"] = "Condición";
$fltr["action"] = "Acción";
$fltr["name"] = "Nombre";
$fltr["sort_order"] = "Orden";
$fltr["edit"] = "editar";
$fltr["add"] = "Agregar Filtro";
$fltr["edit_filter"] = "Editar Filtro";
$fltr["delete"] = "Borrar";
$fltr["filters"] = "Filtros";
$fltr["new"] = "Nuevo Filtro";
$fltr["status"] = "Estado";
$fltr["stat_en"] = "activo";
$fltr["stat_dis"] = "deshabilitado";
$fltr["is_active"] = "filtro activo";
$fltr["other"] = "Otro";
$fltr["move"] = "Mover a";
$fltr["copy"] = "Copiar a";
$fltr["do_nothing"] = "No hacer nada";
$fltr["add_cond"] = "Agregar Condición";
$fltr["remove_cond"] = "Borrar Condición";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(Orden más alto se procesa primero)";

$f_err["no_name"] = "Ingrese un nombre para esta regla de filtrado\n";
$f_err["no_act"] = "Seleccione una o m·s acciones para esta regla\n";
$f_err["no_cond"] = "Especifique una o m·s condiciones para esta regla\n";
$f_err["inv_param"] = "Parámetros inv·lidos para las acciones seleccionadas\n";
$f_err["inv_sort"] = "El órden debe ser numérico\n";

?>
