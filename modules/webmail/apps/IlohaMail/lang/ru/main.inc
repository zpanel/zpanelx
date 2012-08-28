<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/ruw/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="Удалить"){
		$str = "Удалить $num сообщение".($num>1?"(й)":"");
	}else if ($type == "move"){
		$str = "Перенести $num сообщение".($num>1?"(й)":"")." в $folder";
	}
	
	return $str;
}

$mainStrings[0] = "Показано %p из %n";
$mainStrings[1] = " найдено сообщений";
$mainStrings[2] = "Предыдущее";
$mainStrings[3] = " сообщений";
$mainStrings[4] = "Следующее";
$mainStrings[5] = " сообщений";
$mainStrings[6] = "Тема";
$mainStrings[7] = "Кому";
$mainStrings[8] = "От";
$mainStrings[9] = "Дата";
$mainStrings[10] = "Удалить";
$mainStrings[11] = "Очистить корзину";
$mainStrings[12] = "Перенести";
$mainStrings[13] = "Поиск";
$mainStrings[14] = "Размер";
$mainStrings[15] = "Без темы";
$mainStrings[16] = "Показать";
$mainStrings[17] = "Проверить почту";
$mainStrings[18] = "Удалить все";
$mainStrings[19] = "Отменить";
$mainStrings[20] = "Пометить как %b";
$mainStrings[21] = "Прочитанное";
$mainStrings[22] = "Непрочитанное";


$mainErrors[0] = "Не найдено сообщений в этой папке";
$mainErrors[1] = "В этой папке нет сообщений";
$mainErrors[2] = "Невозможно перенести сообщения в корзину.";
$mainErrors[3] = "Невозможно пометить сообщения как удаленные.";
$mainErrors[4] = "Невозможно перенести сообщения.";
$mainErrors[5] = "Папка корзины не определена.";
$mainErrors[6] = "Невозможно очистить корзину";
$mainErrors[7] = "Вы уверены что хотите удалить ВСЕ сообщения в %f?";
$mainErrors[8] = "Нажмите на стрелки под заголовками столбцов для того что-бы сдвинуть их.<br>Когда закончите, просто закройте это окно.";



?>