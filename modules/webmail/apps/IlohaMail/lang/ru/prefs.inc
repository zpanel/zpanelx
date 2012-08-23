<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/ruw/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Настройки";
$prefHeaderStrings[1] = "Общие";
$prefHeaderStrings[2] = "Цвета";
$prefHeaderStrings[2] = "Появление";
$prefsStrings["0.0"] = "Личность";
$prefsStrings["0.1"] = "Имя: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["1.0"] = "Интернациональные";
$prefsStrings["1.1"] = "Интерфейс: ";
$prefsStrings["1.2"] = "Кодовая страница: ";
$prefsStrings["1.3"] = "Часовой пояс: ";
$prefsStrings["1.4"] = "Формат времени: ";
$prefsStrings["1.5"] = array(12=>"12 часовый формат", 24=>"24 часовый формат");
$prefsStrings["2.0"] = "Настройки страницы списка сообщений";
$prefsStrings["2.1"] = "Показать ";
$prefsStrings["2.2"] = " сообщений";
$prefsStrings["2.3"] = "Показать размер";
$prefsStrings["2.4"] = "Сортировать по: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "сортировка: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Показать список папок";
$prefsStrings["2.9"] = "Ширина списка папок";
$prefsStrings["2.10"] = "Проверять почту каждые %n секунд";
$prefsStrings["2.11"] = "Показать Панель инструментов %m";
$prefsStrings["2.12"] = array("b"=>"снизу", "t"=>"сверху", "bt"=>"сверху и снизу");
$prefsStrings["2.13"] = "Чтобы перестроить столбцы:  ";
$prefsStrings["2.14"] = "нажмите здесь";
$prefsStrings["3.0"] = "Автосохранение настроек";
$prefsStrings["3.1"] = "Сохранять отправленные сообщения.";
$prefsStrings["3.2"] = "Сохранять отправленные сообщения в папку: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Автоматически удалять из корзины";
$prefsStrings["3.5"] = "Переносить удаленные сообщения в папку: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Главная папка: ";
$prefsStrings["3.8"] = "Другое";
$prefsStrings["3.9"] = "Спрятать неподписанные папки";
$prefsStrings["3.10"] = "Показать квоту %m";
$prefsStrings["3.11"] = array(""=>"не показывать", "m"=>"в списке сообщений", "f"=>"в списке папок");
$prefsStrings["4.0"] = "Настройки страницы просмотра сообщения";
$prefsStrings["4.1"] = "Просматривать во фрейме";
$prefsStrings["4.2"] = "Выделять цветом цитаты";
$prefsStrings["4.3"] = "Включая эту опцию, вы можете замедлить систему.";
$prefsStrings["4.4"] = "Преобразовывать URL-ы в ссылки";
$prefsStrings["4.5"] = "Показывать HTML сообщения";
$prefsStrings["4.6"] = "Показывать картинки";
$prefsStrings["4.7"] = "Показывать навигационные ссылки";
$prefsStrings["4.8"] = "Не помечать сообщения при пересечении";
$prefsStrings["5.0"] = "Подпись";
$prefsStrings["5.1"] = "Показывать подпись по умолчанию";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Настройки Нового письма";
$prefsStrings["6.1"] = "Показывать контакты";
$prefsStrings["6.2"] = "Показа Копия/Слепая копия поля";
$prefsStrings["6.3"] = "Закрывать после отправки";
$prefsStrings["6.4"] = "Писать новое письмо внутри фрейма";
$prefsStrings["7.0.0"] = "Опции Диска и Папок";
$prefsStrings["7.0.1"] = "Опции квоты";
$prefsStrings["7.1"] = "Показывать кол-во непрочитанных писем";
$prefsStrings["7.2"] = "Обновлять список папок каждые %n секунд";



$sort_fields=array("DATE"=>"Дата", "SUBJECT"=>"Тема", "SIZE"=>"Размер");
$sort_orders=array("ASC"=>"По возрастанию", "DESC"=>"По убыванию");

$prefsButtonStrings = array( 0=> "Обновить", 1=>"Применить", 2=> "Отменить", 3=> "По умолчанию");

$prefs_new_user = "Вы зашли в первый раз, добро пожаловать!<br>";
$prefs_new_user .= "Пожалуйста проверьте следующие настройки, а затем нажмите \"Применить\"";
?>