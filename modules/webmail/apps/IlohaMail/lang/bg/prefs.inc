<?php
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Настройки";
$prefHeaderStrings[1] = "Основни";
$prefHeaderStrings[3] = "Самоличности";
$prefHeaderStrings[2] = "Външен вид";

											
$prefsStrings["0.0"] = "Подразбираща се самоличност";
$prefsStrings["0.1"] = "Име: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Промени могат да бъдат направени в страницата \"%s\".";
$prefsStrings["0.4"] = "Неопределено";
$prefsStrings["1.0"] = "Езикови настройки";
$prefsStrings["1.1"] = "Интерфейс: ";
$prefsStrings["1.2"] = "Кодиране: ";
$prefsStrings["1.3"] = "Времева зона: ";
$prefsStrings["1.4"] = "Тип: ";
$prefsStrings["1.5"] = array(12=>"12 hour clock", 24=>"24 hour clock");
$prefsStrings["2.0"] = "Настройки за показване на списъка";
$prefsStrings["2.1"] = "Показвай до ";
$prefsStrings["2.2"] = " съобщения";
$prefsStrings["2.3"] = "Показвай размера";
$prefsStrings["2.4"] = "Сортирай по: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ред на сортирането: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Показвай списък с папките";
$prefsStrings["2.9"] = "Ширина на списъка с папки";
$prefsStrings["2.10"] = "Проверявай за нови съобщения всеки %n секунди";
$prefsStrings["2.11"] = "Показвай лентата с инструменти %m";
$prefsStrings["2.12"] = array("b"=>"долу", "t"=>"горе", "bt"=>"долу и горе");
$prefsStrings["2.13"] = "Пренареждане на колоните: ";
$prefsStrings["2.14"] = "натисни тук";
$prefsStrings["3.0"] = "Натройки за автоматично запазване";
$prefsStrings["3.1"] = "Запазвай изпратените съобщения.";
$prefsStrings["3.2"] = "Запазвай изпратените съобщения в папка: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Автоматично изпразвай кошчето";
$prefsStrings["3.5"] = "Премествай изтрите съобщения в папка: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Главна директория: ";
$prefsStrings["3.8"] = "Други";
$prefsStrings["3.9"] = "Скрий неподписаните папки";
$prefsStrings["3.10"] = "Показване на квотите %m";
$prefsStrings["3.11"] = array(""=>"не показвай", "m"=>"в списъка със съобщенията", "f"=>"в списъка с папки");
$prefsStrings["4.0"] = "Настройки за показване на съобщенията";
$prefsStrings["4.1"] = "Показвай във фрейм";
$prefsStrings["4.2"] = "Оцветявай цитатите";
$prefsStrings["4.3"] = "Включването на тази възможност може да забави ситемата.";
$prefsStrings["4.4"] = "Конвертирай URL -ите във връзки";
$prefsStrings["4.5"] = "Показвай HTML съобщения";
$prefsStrings["4.6"] = "Показвай картинките вътре";
$prefsStrings["4.7"] = "Показвай връзките за навигация";
$prefsStrings["4.8"] = "Не маркирай съобщенията когато се преминава над тях";
$prefsStrings["5.0"] = "Подпис";
$prefsStrings["5.1"] = "Показвай подписа по подразбиране";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Съставяне";
$prefsStrings["6.1"] = "Показвак контактите";
$prefsStrings["6.2"] = "Показвай CC/BCC полета";
$prefsStrings["6.3"] = "Затвори след изпращане";
$prefsStrings["6.4"] = "Съставяй във фрейм";
$prefsStrings["7.0.0"] = "Настройки на Диск &amp; Папки";
$prefsStrings["7.0.1"] = "Настройки за квотите";
$prefsStrings["7.1"] = "Показвай броя непрочетени";
$prefsStrings["7.2"] = "Опреснявай списъка с папките всеки %n секунди";

$pref_col_title = "Пренареждане на колоните";
$pref_colstr["order"] = "Ред";
$pref_colstr["field"] = "Поле";
$pref_col_label['c'] = "Check box";
$pref_col_label['a'] = "Прикачени файлове";
$pref_col_label['m'] = "Флагове/статус";

$sort_fields=array("DATE"=>"Дата", "SUBJECT"=>"Тема", "SIZE"=>"Размер");
$sort_orders=array("ASC"=>"Възходящ", "DESC"=>"Низходящ");

$prefsButtonStrings = array( 0=> "Обнови", 1=>"Приложи", 2=> "Откажи", 3=> "Подразбиращи се");

$prefs_new_user = "Изглежда влизате в системата за първи път...добре дошли!<br>";
$prefs_new_user .= "Моля преминете през настройките, и после натиснете \"Приложи\"";
?>
