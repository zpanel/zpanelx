<?php

// TRANSLATORS: Bla Bla
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/pt/prefs.inc
	(C)2003 Darwings <darwings@lisboa.alface.de || asas@netcabo.pt>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Preferências";
$prefHeaderStrings[1] = "Geral";
$prefHeaderStrings[3] = "Identidades";
$prefHeaderStrings[2] = "Aparência";

											
$prefsStrings["0.0"] = "Identificação";
$prefsStrings["0.1"] = "Nome: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "As alteracoes podem ser feitas na pagina das \"%s\".";
$prefsStrings["0.4"] = "Nao especificado";
$prefsStrings["1.0"] = "Internacional";
$prefsStrings["1.1"] = "Interface: ";
$prefsStrings["1.2"] = "Tipo de caracteres: ";
$prefsStrings["1.3"] = "Fuso horario: ";
$prefsStrings["1.4"] = "Formato: ";
$prefsStrings["1.5"] = array(12=>"12 Horas", 24=>"24 horas");
$prefsStrings["2.0"] = "Opções de listagem";
$prefsStrings["2.1"] = "Mostrar ";
$prefsStrings["2.2"] = " items";
$prefsStrings["2.3"] = "Mostrar tamanho";
$prefsStrings["2.4"] = "Ordenar por: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ordem: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mostrar lista de pastas";
$prefsStrings["2.9"] = "Largura da lista de pastas";
$prefsStrings["2.10"] = "Verificar mensagens todos os %n segundos";
$prefsStrings["2.11"] = "Mostrar barra de feramentas em  %m";
$prefsStrings["2.12"] = array("b"=>"baixo", "t"=>"cima", "bt"=>"ciam e baixo");
$prefsStrings["2.13"] = "Reorganizar colunas: ";
$prefsStrings["2.14"] = "carregue aqui";
$prefsStrings["3.0"] = "Gravar automaticamente opções";
$prefsStrings["3.1"] = "Guardar items enviados.";
$prefsStrings["3.2"] = "Guardar mensagens enviadas na pasta: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Apagar lixo automaticamente";
$prefsStrings["3.5"] = "Mover items apagados para a pasta: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Directoria Raiz: ";
$prefsStrings["3.8"] = "Outras";
$prefsStrings["3.9"] = "Esconder pastas não subscritas";
$prefsStrings["3.10"] = "Mostrar quota %m";
$prefsStrings["3.11"] = array(""=>"não mostrar", "m"=>"na listagem de mensagens", "f"=>"na lista de pastas");
$prefsStrings["4.0"] = "Opções de vizualização de mensagens";
$prefsStrings["4.1"] = "Mostrar na frame";
$prefsStrings["4.2"] = "Colorir transcricões";
$prefsStrings["4.3"] = "Ligar esta opção pode tornar o computador mais lento.";
$prefsStrings["4.4"] = "Converter URLs em links";
$prefsStrings["4.5"] = "Mostrar mensagens em HTML";
$prefsStrings["4.6"] = "Mostrar imagens";
$prefsStrings["4.7"] = "Mostrar links de navegação";
$prefsStrings["4.8"] = "Não marcar mensagens como lidas quando for usado o sistema de navegação dentro das mesmas (<- Entrada ->)";
$prefsStrings["5.0"] = "Assinatura";
$prefsStrings["5.1"] = "Mostrar assinatura por defeito";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Compor";
$prefsStrings["6.1"] = "Mostrar contactos";
$prefsStrings["6.2"] = "Mostrar campos CC/BCC";
$prefsStrings["6.3"] = "Fechar após enviar";
$prefsStrings["6.4"] = "Compor dentro de frame";
$prefsStrings["7.0"] = "Opções de pastas";
$prefsStrings["7.0.0"] = "Disco &amp; Opções de pastas";   
$prefsStrings["7.0.1"] = "Opções de quota";
$prefsStrings["7.1"] = "Mostrar não lidas";
$prefsStrings["7.2"] = "Actualizar lista de pastas todos os %n segundos";

$pref_col_title = "Rearranjar colunas";
$pref_colstr["order"] = "Ordem";
$pref_colstr["field"] = "Campo";
$pref_col_label['c'] = "Caixa de verificacao";
$pref_col_label['a'] = "Ficheiros";
$pref_col_label['m'] = "Prioridade/Estado";

$sort_fields=array("DATE"=>"Data", "SUBJECT"=>"Assunto", "SIZE"=>"Tamanho");
$sort_orders=array("ASC"=>"Ascendente", "DESC"=>"Descendente");

$prefsButtonStrings = array( 0=> "Actualizar", 1=>"Aplicar", 2=> "Cancelar", 3=> "Defeito");

$prefs_new_user = "Parece que se está a ligar pela primeira vez... Bem-Vindo!<br>";
$prefs_new_user .= "Por favor modifique as seguintes opções, e depois carregue em \"Aplicar\"";
?>
